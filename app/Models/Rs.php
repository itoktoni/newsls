<?php

namespace App\Models;

use App\Properties\RsEntity;

class Rs extends BaseModel
{
    use RsEntity;

    protected $table = 'rs';

    protected $primaryKey = 'rs_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        'rs_id_group',
        'rs_nama',
        'rs_alamat',
        'rs_deskripsi',
        'rs_harga_cuci',
        'rs_harga_sewa',
        'rs_aktif',
        'rs_code',
        'rs_status',
        'rs_logo',
    ];

    public static $filterColumns = ['rs_nama', 'rs_code', 'rs_status'];

    public static $sortColumns = ['rs_nama', 'rs_code', 'rs_alamat'];

    protected function casts(): array
    {
        return [
            'rs_id' => 'integer',
            'rs_id_group' => 'integer',
            'rs_harga_cuci' => 'integer',
            'rs_harga_sewa' => 'integer',
        ];
    }

    public static function field_name(): string
    {
        return 'rs_nama';
    }

    public function rules(): array
    {
        return [
            'rs_id_group' => 'nullable|integer|exists:group_rs,group_rs_id',
            'rs_nama' => 'required|string|max:255',
            'rs_alamat' => 'nullable|string',
            'rs_deskripsi' => 'nullable|string',
            'rs_harga_cuci' => 'nullable|integer|min:0',
            'rs_harga_sewa' => 'nullable|integer|min:0',
            'rs_aktif' => 'nullable|integer',
            'rs_code' => 'nullable|string|max:255',
            'rs_status' => 'nullable|string|max:50',
            'rs_logo' => 'nullable|string|max:255',
        ];
    }

    public function hasGroup()
    {
        return $this->hasOne(GroupRs::class, 'group_rs_id', 'rs_id_group');
    }

    public function hasRuangan()
    {
        return $this->belongsToMany(Ruangan::class, 'rs_dan_ruangan', 'rs_id', 'ruangan_id');
    }

    public function hasJenis()
    {
        return $this->belongsToMany(JenisLinen::class, 'rs_dan_jenis', 'rs_id', 'jenis_id')->withPivot(['parstock']);
    }

    /**
     * Ruangan milik RS dalam bentuk yang dibaca desktop (RsAllDAO/RsSingleDAO:
     * rs_ruangan[{ruangan_id, ruangan_nama}]) — legacy lewat RsSingleResource.
     */
    public function getRsRuanganAttribute(): array
    {
        return $this->hasRuangan
            ->map(fn ($ruangan) => [
                'ruangan_id' => (int) $ruangan->ruangan_id,
                'ruangan_nama' => $ruangan->ruangan_nama,
            ])
            ->values()
            ->all();
    }

    /** Jenis linen milik RS — rs_jenis[{jenis_id, jenis_nama}]. */
    public function getRsJenisAttribute(): array
    {
        return $this->hasJenis
            ->map(fn ($jenis) => [
                'jenis_id' => (int) $jenis->jenis_id,
                'jenis_nama' => $jenis->jenis_nama,
            ])
            ->values()
            ->all();
    }
}
