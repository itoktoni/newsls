<?php

namespace App\Models;

class GantiChip extends BaseModel
{
    protected $table = 'ganti_chip';

    protected $primaryKey = 'ganti_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'ganti_rfid_lama',
        'ganti_rfid_baru',
        'ganti_tanggal',
        'ganti_by',
        'ganti_keterangan',
    ];

    public static $filterColumns = ['ganti_rfid_lama', 'ganti_rfid_baru'];

    public static $sortColumns = ['ganti_rfid_baru', 'ganti_tanggal'];

    protected function casts(): array
    {
        return [
            'ganti_id' => 'integer',
            'ganti_by' => 'integer',
            'ganti_tanggal' => 'datetime',
        ];
    }

    public static function field_name(): string
    {
        return 'ganti_rfid_baru';
    }

    public function rules(): array
    {
        return [
            'ganti_rfid_lama' => 'required|string|max:255',
            'ganti_rfid_baru' => 'required|string|max:255',
            'ganti_tanggal' => 'nullable|date',
            'ganti_by' => 'nullable|integer',
            'ganti_keterangan' => 'nullable|string',
        ];
    }

    public function hasUser()
    {
        return $this->hasOne(User::class, 'id', 'ganti_by');
    }

    // ponytail: rantai A→B→C — tumpuk semua baris yang terhubung
    // transitif dengan RFID ini, terbaru dulu. Mis. A→B lalu B→C:
    // buka C = tampil B→C dan A→B.
    public static function forRfid(string $rfid)
    {
        $all = static::query()->orderByDesc('ganti_tanggal')->orderByDesc('ganti_id')->get();
        if ($all->isEmpty()) {
            return $all;
        }

        $seenRfids = [$rfid => true];
        $result = collect();
        $expanded = true;

        while ($expanded) {
            $expanded = false;

            foreach ($all as $row) {
                if ($result->contains('ganti_id', $row->ganti_id)) {
                    continue;
                }

                if (isset($seenRfids[$row->ganti_rfid_lama]) || isset($seenRfids[$row->ganti_rfid_baru])) {
                    $result->push($row);
                    $seenRfids[$row->ganti_rfid_lama] = true;
                    $seenRfids[$row->ganti_rfid_baru] = true;
                    $expanded = true;
                }
            }
        }

        return $result->sortByDesc('ganti_tanggal')->sortByDesc('ganti_id')->values();
    }
}
