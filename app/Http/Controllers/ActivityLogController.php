<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogController extends Controller
{
    use ControllerTrait;

    /**
     * Nama filter/quick-search → kolom nyata (harus qualified).
     *
     * ponytail: query tabel ini leftJoin `users` (untuk nama causer), jadi
     * `created_at`/`subject_id` polos jadi ambigu — activity_log dan users
     * punya `created_at` (MySQL error 1052). Semua kolom karena itu ditulis
     * lengkap. Whitelist sekaligus pengaman: field di luar daftar ini
     * diabaikan, jadi query string tidak bisa menyuntik kolom sembarang.
     */
    private const FILTERS = [
        'log_name' => 'activity_log.log_name',
        'event' => 'activity_log.event',
        'subject_id' => 'activity_log.subject_id',
        'subject_type' => 'activity_log.subject_type',
        'description' => 'activity_log.description',
        'user_name' => 'users.name',
        'created_at' => 'activity_log.created_at',
    ];

    /**
     * Kolom tabel → kolom sortir nyata.
     * ponytail: `causer_name` (alias join) disortir lewat `users.name` —
     * cursor pagination menaruh kolom sortir di WHERE (`users.name > ?`) dan
     * MySQL tidak menerima alias di WHERE.
     */
    private const SORTABLE = [
        'log_name' => 'activity_log.log_name',
        'event' => 'activity_log.event',
        'subject_id' => 'activity_log.subject_id',
        'subject_model_name' => 'activity_log.subject_type',
        'causer_name' => 'users.name',
        'created_at' => 'activity_log.created_at',
    ];

    public function __construct(Activity $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'logNameOptions' => Activity::query()
                ->distinct()->pluck('log_name')->filter()->values()
                ->mapWithKeys(fn ($v) => [$v => $v])->all(),
            // ponytail: opsi Event untuk dropdown filter (created/updated/deleted + custom).
            'eventOptions' => Activity::query()
                ->distinct()->pluck('event')->filter()->values()
                ->mapWithKeys(fn ($v) => [$v => $v])->all()
                + ['created' => 'created', 'updated' => 'updated', 'deleted' => 'deleted'],
            // ponytail: filter Model pakai FQCN sebagai value (kolomnya FQCN),
            // labelnya basename supaya enak dibaca.
            'subjectTypeOptions' => Activity::query()
                ->distinct()->pluck('subject_type')->filter()->values()
                ->mapWithKeys(fn ($v) => [$v => class_basename($v)])->all(),
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        $query = $this->model->query()
            ->select('activity_log.*')
            // ponytail: causer_id di-join ke users supaya tabel menampilkan
            // NAMA user (dulu cuma ID). Relasi `causer` (MorphTo) sengaja tidak
            // dipakai lewat PowerJoins — leftJoinRelationship('causer') butuh
            // morphable konkret dan fatal untuk baris dengan causer_type null.
            ->addSelect('users.name as causer_name')
            ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id');

        $this->applySearch($query);
        $this->applyFilters($query);
        $this->applySort($query);

        return $query;
    }

    /** Quick-search (`_q` + `_field`): field divalidasi ke whitelist FILTERS. */
    private function applySearch(Builder $query): void
    {
        $term = request('_q');
        $field = (string) request('_field');

        if ($term === null || $term === '' || ! isset(self::FILTERS[$field])) {
            return;
        }

        $this->applyCondition($query, $field, '$contains', $term);
    }

    /** Advanced filter: `filters[field][$operator]=value` (lihat x-filter-item). */
    private function applyFilters(Builder $query): void
    {
        foreach ((array) request('filters', []) as $field => $conditions) {
            if (! isset(self::FILTERS[$field])) {
                continue;
            }

            foreach ((array) $conditions as $operator => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }

                $this->applyCondition($query, (string) $field, (string) $operator, $value);
            }
        }
    }

    private function applyCondition(Builder $query, string $field, string $operator, mixed $value): void
    {
        $column = self::FILTERS[$field];

        // Input date mengirim 'Y-m-d' sedangkan kolomnya datetime → bandingkan
        // tanggalnya saja, bukan timestamp penuh.
        if ($field === 'created_at') {
            $query->whereDate($column, match ($operator) {
                '$gt' => '>',
                '$gte' => '>=',
                '$lt' => '<',
                '$lte' => '<=',
                default => '=',
            }, $value);

            return;
        }

        match ($operator) {
            '$eq' => $query->whereRaw('LOWER('.$column.') = ?', [strtolower((string) $value)]),
            '$ne' => $query->whereRaw('LOWER('.$column.') != ?', [strtolower((string) $value)]),
            '$in' => $query->whereIn($column, (array) $value),
            '$gt' => $query->where($column, '>', $value),
            '$gte' => $query->where($column, '>=', $value),
            '$lt' => $query->where($column, '<', $value),
            '$lte' => $query->where($column, '<=', $value),
            '$notContains' => $query->whereRaw('LOWER('.$column.') NOT LIKE ?', ['%'.strtolower((string) $value).'%']),
            default => $query->whereRaw('LOWER('.$column.') LIKE ?', ['%'.strtolower((string) $value).'%']),
        };
    }

    /** `sort[0]=kolom:arah` — nama kolom tabel dipetakan ke kolom nyata. */
    private function applySort(Builder $query): void
    {
        $sort = (string) request('sort.0', '');
        [$field, $direction] = array_pad(explode(':', $sort, 2), 2, 'asc');

        if ($sort === '' || ! isset(self::SORTABLE[$field])) {
            // ponytail: default terbaru dulu bila user belum memilih sort.
            $query->orderByDesc('activity_log.id');

            return;
        }

        $query->orderBy(self::SORTABLE[$field], $direction === 'desc' ? 'desc' : 'asc');
    }
}
