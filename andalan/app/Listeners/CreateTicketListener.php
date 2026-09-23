<?php

namespace App\Listeners;

use App\Dao\Enums\KontrakType;
use App\Dao\Enums\NotificationStatus;
use App\Dao\Enums\WorkStatus;
use App\Dao\Models\Notification as ModelsNotification;
use App\Dao\Models\TicketSystem;
use App\Dao\Models\User;
use App\Dao\Models\WorkSheet;
use App\Events\CreateTicketEvent;
use Carbon\Carbon;

class CreateTicketListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateTicketEvent $event)
    {
        $data = $event->data;
        $report_from = $data->has_reported ?? false;
        $report_to = $data->has_category->has_user ?? false;

        $email_from = $receive_handphone = false;

        if (env('WA_ENABLE', false)) {
            $receive_handphone = $report_from->field_phone ?? false;
        }

        if ($receive_handphone) {
            $description = '';
            if ($event->data->has_type) {
                $tipe = $event->data->has_type->field_name ?? '';
                $description = $description.'Type : '.$tipe.PHP_EOL;
            }

            if ($report_from) {
                $pelapor = $data->field_reported_name ?? $report_from->field_name ?? '';
                $description = $description.'Pelapor : '.$pelapor.PHP_EOL;
            }

            if ($event->data->has_location) {
                $location = $event->data->has_location->field_name ?? '';
                $description = $description.'Lokasi : '.$location.PHP_EOL;
            }

            $description = $description.'Deskripsi : '.$event->data->field_description.PHP_EOL;

            if ($product = $data->has_product) {
                $product_name = $product->field_name ?? '';
                $description = $description.'Alat : '.$product_name.PHP_EOL;

                $saveWorksheet = [
                    WorkSheet::field_description() => $data->field_description,
                    WorkSheet::field_ticket_code() => $data->field_primary,
                    WorkSheet::field_status() => WorkStatus::Open,
                    WorkSheet::field_type_id() => $data->field_work_type_id ?? null,
                    WorkSheet::field_name() => $data->has_type->field_name ?? null,
                    WorkSheet::field_contract() => $product->field_contract ?? null,
                    WorkSheet::field_product_id() => $product->field_primary ?? null,
                    WorkSheet::field_location_id() => $data->{TicketSystem::field_location_id()} ?? null,
                    WorkSheet::field_reported_at() => date('Y-m-d H:i:s'),
                    WorkSheet::field_reported_name() => $data->field_reported_name ?? $data->field_reported_by_name ?? null,
                ];

                if ($product->field_contract == KontrakType::Kontrak) {
                    $vendor = $product->has_vendor;
                    if ($vendor_phone = $vendor->field_phone) {

                        $saveWorksheet = array_merge($saveWorksheet, [
                            WorkSheet::field_vendor_id() => $vendor->field_primary,
                        ]);

                        $link = WorkSheet::create($saveWorksheet);

                        $description_vendor = $description.'Link : '.route(env('WORK_ROUTE').'.getUpdate', ['code' => $link->field_primary]);
                        $this->saveNotification($vendor->field_name, $description_vendor, $vendor_phone, $data->field_category_id, $data->field_picture);
                    }
                } else {
                    $data_teknisi = json_decode($product->field_teknisi_data);
                    if (! empty($data_teknisi)) {
                        $get_teknisi = User::whereIn(User::field_primary(), array_values($data_teknisi))
                            ->whereNotNull(User::field_phone())->get();

                        if (! empty($get_teknisi)) {
                            $saveWorksheet = array_merge($saveWorksheet, [
                                WorkSheet::field_implementor() => json_encode($data_teknisi),
                            ]);

                            $link = WorkSheet::create($saveWorksheet);

                            foreach ($get_teknisi as $teknisi) {
                                $description_teknisi = $description.'Link : '.route(env('WORK_ROUTE').'.getUpdate', ['code' => $link->field_primary]);
                                $this->saveNotification($teknisi->field_name, $description_teknisi, $teknisi->field_phone, $data->field_category_id, $data->field_picture);
                            }
                        }
                    }
                }
            }

            $description = $description.'Link : '.route(env('TICKET_ROUTE').'.getUpdate', ['code' => $event->data->field_primary]);

            $this->saveNotification($pelapor, $description, $receive_handphone, $data->field_category_id, $data->field_picture);

            if ($report_to->count() > 0) {
                foreach ($report_to as $teknisi) {
                    $this->saveNotification($teknisi->field_name, $description, $teknisi->field_phone, $data->field_category_id, $data->field_picture);
                }
            }
        }
    }

    private function saveNotification($pelapor, $description, $phone, $type, $picture = null)
    {
        ModelsNotification::create([
            ModelsNotification::field_name() => $pelapor,
            ModelsNotification::field_description() => $description,
            ModelsNotification::field_status() => NotificationStatus::Create,
            ModelsNotification::field_phone() => $phone,
            ModelsNotification::field_eta() => Carbon::today(),
            ModelsNotification::field_type() => $type,
            ModelsNotification::field_image() => $picture ? asset('storage/ticket/'.$picture) : null,
        ]);
    }
}
