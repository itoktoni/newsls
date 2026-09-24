<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @mixin SpatieActivity
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property string|null $subject_id
 * @property string|null $event
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $attribute_changes
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $properties
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $causer
 * @property-read string|null $causer_email
 * @property-read string|null $causer_name
 * @property-read mixed $field_attribute_changes
 * @property-read mixed $field_causer_id
 * @property-read mixed $field_causer_type
 * @property-read mixed $field_created_at
 * @property-read mixed $field_description
 * @property-read mixed $field_event
 * @property-read mixed $field_id
 * @property-read mixed $field_key
 * @property-read mixed $field_log_name
 * @property-read mixed $field_name
 * @property-read mixed $field_primary
 * @property-read mixed $field_subject_id
 * @property-read mixed $field_subject_type
 * @property-read string $subject_model_name
 * @property-read string|null $subject_name
 * @property-read \Illuminate\Database\Eloquent\Model|null $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forEvent(\Spatie\Activitylog\Enums\ActivityEvent|string $event)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity inLog(\BackedEnum|array|string ...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereAttributeChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCauserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUpdatedAt($value)
 */
	class Activity extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperBaseModel
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel sortFields(array|string $fields)
 */
	class BaseModel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $rs_id
 * @property string $detail_rfid
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read mixed $field_rfid
 * @property-read mixed $field_rs_id
 * @property-read \App\Models\DetailLinen|null $hasDetail
 * @property-read \App\Models\Rs|null $hasRs
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen whereDetailRfid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfigLinen whereRsId($value)
 */
	class ConfigLinen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereUserAgent($value)
 */
	class ContactMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $detail_id
 * @property string $detail_rfid
 * @property int|null $detail_id_rs
 * @property int|null $detail_id_ruangan
 * @property int|null $detail_id_jenis
 * @property int|null $detail_id_bahan
 * @property int|null $detail_id_supplier
 * @property string|null $detail_deskripsi
 * @property int|null $detail_created_by
 * @property int|null $detail_updated_by
 * @property int|null $detail_deleted_by
 * @property \Carbon\CarbonImmutable|null $detail_created_at
 * @property \Carbon\CarbonImmutable|null $detail_updated_at
 * @property string|null $detail_deleted_at
 * @property string|null $detail_status_cuci
 * @property string|null $detail_status_register
 * @property string|null $detail_status_kepemilikan
 * @property string|null $detail_status_linen
 * @property int|null $detail_total_rewash
 * @property int|null $detail_total_reject
 * @property int|null $detail_total_bersih
 * @property \Carbon\CarbonImmutable|null $detail_tgl_cek
 * @property \Carbon\CarbonImmutable|null $detail_report
 * @property string|null $detail_lama
 * @property string|null $detail_pengantian_user
 * @property string|null $detail_pengantian_waktu
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read mixed $field_bahan_id
 * @property-read mixed $field_cek
 * @property-read mixed $field_description
 * @property-read mixed $field_jenis_id
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read mixed $field_report
 * @property-read mixed $field_rs_id
 * @property-read mixed $field_ruangan_id
 * @property-read mixed $field_status_cuci
 * @property-read string $field_status_cuci_name
 * @property-read mixed $field_status_kepemilikan
 * @property-read mixed $field_status_linen
 * @property-read string $field_status_linen_name
 * @property-read mixed $field_status_register
 * @property-read string $field_status_register_name
 * @property-read mixed $field_supplier_id
 * @property-read \App\Models\JenisBahan|null $hasBahan
 * @property-read \App\Models\JenisLinen|null $hasJenis
 * @property-read \App\Models\Rs|null $hasRs
 * @property-read \App\Models\Ruangan|null $hasRuangan
 * @property-read \App\Models\Supplier|null $hasSupplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailIdBahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailIdJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailIdRs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailIdRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailIdSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailLama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailPengantianUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailPengantianWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailRfid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailStatusCuci($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailStatusKepemilikan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailStatusLinen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailStatusRegister($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailTglCek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailTotalBersih($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailTotalReject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailTotalRewash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailLinen whereDetailUpdatedBy($value)
 */
	class DetailLinen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $ganti_id
 * @property string $ganti_rfid_lama
 * @property string $ganti_rfid_baru
 * @property \Carbon\CarbonImmutable|null $ganti_tanggal
 * @property int|null $ganti_by
 * @property string|null $ganti_keterangan
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\User|null $hasUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip whereGantiBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip whereGantiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip whereGantiKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip whereGantiRfidBaru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip whereGantiRfidLama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GantiChip whereGantiTanggal($value)
 */
	class GantiChip extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $group_rs_id
 * @property string $group_rs_nama
 * @property string|null $group_rs_code
 * @property string|null $group_rs_deskripsi
 * @property-read mixed $field_code
 * @property-read mixed $field_deskripsi
 * @property-read mixed $field_key
 * @property-read mixed $field_nama
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rs> $hasRs
 * @property-read int|null $has_rs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs whereGroupRsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs whereGroupRsDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs whereGroupRsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupRs whereGroupRsNama($value)
 */
	class GroupRs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $bahan_id
 * @property string|null $bahan_nama
 * @property string|null $bahan_deskripsi
 * @property-read mixed $field_description
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan whereBahanDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan whereBahanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisBahan whereBahanNama($value)
 */
	class JenisBahan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $jenis_id
 * @property int|null $jenis_id_rs
 * @property int|null $jenis_id_kategori
 * @property string|null $jenis_nama
 * @property string|null $jenis_deskripsi
 * @property string|null $jenis_gambar
 * @property float|null $jenis_berat
 * @property-read mixed $field_category_id
 * @property-read mixed $field_description
 * @property-read mixed $field_image
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read mixed $field_rs_id
 * @property-read mixed $field_weight
 * @property-read string $gambar_url
 * @property-read \App\Models\Kategori|null $hasCategory
 * @property-read \App\Models\Rs|null $hasRs
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisBerat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisGambar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisIdRs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisLinen whereJenisNama($value)
 */
	class JenisLinen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $kategori_id
 * @property string|null $kategori_nama
 * @property string|null $kategori_deskripsi
 * @property-read mixed $field_description
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereKategoriDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereKategoriNama($value)
 */
	class Kategori extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $location
 * @property array<array-key, mixed>|null $items
 * @property bool $is_active
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu withoutTrashed()
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $icon
 * @property string $icon_color
 * @property string $title
 * @property string|null $body
 * @property string|null $url
 * @property string $type
 * @property bool $read
 * @property array<array-key, mixed>|null $meta
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\User|null $has_user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIconColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $opname_id
 * @property \Carbon\CarbonImmutable|null $opname_mulai
 * @property \Carbon\CarbonImmutable|null $opname_selesai
 * @property string|null $opname_nama
 * @property int|null $opname_id_rs
 * @property \Carbon\CarbonImmutable|null $opname_created_at
 * @property int|null $opname_created_by
 * @property \Carbon\CarbonImmutable|null $opname_updated_at
 * @property int|null $opname_updated_by
 * @property int|null $opname_status
 * @property \Carbon\CarbonImmutable|null $opname_capture
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OpnameDetail> $hasDetail
 * @property-read int|null $has_detail_count
 * @property-read \App\Models\Rs|null $hasRs
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameCapture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameIdRs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opname whereOpnameUpdatedBy($value)
 */
	class Opname extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $opname_detail_id
 * @property int|null $opname_detail_id_opname
 * @property string|null $opname_detail_code
 * @property string|null $opname_detail_rfid
 * @property \Carbon\CarbonImmutable|null $opname_detail_waktu
 * @property string|null $opname_detail_transaksi
 * @property string|null $opname_detail_proses
 * @property string|null $opname_detail_hilang
 * @property int|null $opname_detail_ketemu
 * @property \Carbon\CarbonImmutable|null $opname_detail_created_at
 * @property string|null $opname_detail_updated_at
 * @property int|null $opname_detail_created_by
 * @property int|null $opname_detail_updated_by
 * @property int|null $opname_detail_register
 * @property string|null $opname_detail_hilang_at
 * @property string|null $opname_detail_pending_at
 * @property int|null $opname_detail_scan_rs
 * @property int|null $opname_detail_sync
 * @property string|null $opname_detail_reff
 * @property string|null $opname_detail_scan_by
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \App\Models\Opname|null $hasOpname
 * @property-read \App\Models\DetailLinen|null $hasView
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailHilang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailHilangAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailIdOpname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailKetemu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailPendingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailProses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailReff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailRegister($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailRfid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailScanBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailScanRs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailSync($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OpnameDetail whereOpnameDetailWaktu($value)
 */
	class OpnameDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $outstanding_rfid
 * @property string|null $outstanding_key
 * @property int|null $outstanding_rs_ori
 * @property int|null $outstanding_rs_scan
 * @property int|null $outstanding_id_ruangan
 * @property string|null $outstanding_status_transaksi
 * @property string|null $outstanding_status_hilang
 * @property string|null $outstanding_status_proses
 * @property string|null $outstanding_status_beda_rs
 * @property \Carbon\CarbonImmutable|null $outstanding_created_at
 * @property \Carbon\CarbonImmutable|null $outstanding_updated_at
 * @property int|null $outstanding_created_by
 * @property int|null $outstanding_updated_by
 * @property string|null $outstanding_pending_created_at
 * @property string|null $outstanding_pending_updated_at
 * @property string|null $outstanding_hilang_created_at
 * @property string|null $outstanding_hilang_updated_at
 * @property int|null $outstanding_id_warehouse
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingHilangCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingHilangUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingIdRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingIdWarehouse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingPendingCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingPendingUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingRfid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingRsOri($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingRsScan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingStatusBedaRs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingStatusHilang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingStatusProses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingStatusTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outstanding whereOutstandingUpdatedBy($value)
 */
	class Outstanding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $rs_id
 * @property int|null $rs_id_group
 * @property string|null $rs_nama
 * @property string|null $rs_alamat
 * @property string|null $rs_deskripsi
 * @property int|null $rs_harga_cuci
 * @property int|null $rs_harga_sewa
 * @property int|null $rs_aktif
 * @property string|null $rs_code
 * @property string|null $rs_status
 * @property string|null $rs_logo
 * @property-read mixed $field_alamat
 * @property-read mixed $field_code
 * @property-read mixed $field_description
 * @property-read mixed $field_group
 * @property-read mixed $field_harga_cuci
 * @property-read mixed $field_harga_sewa
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read mixed $field_status
 * @property-read \App\Models\GroupRs|null $hasGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JenisLinen> $hasJenis
 * @property-read int|null $has_jenis_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ruangan> $hasRuangan
 * @property-read int|null $has_ruangan_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsHargaCuci($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsHargaSewa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsIdGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rs whereRsStatus($value)
 */
	class Rs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $ruangan_id
 * @property string|null $ruangan_nama
 * @property string|null $ruangan_deskripsi
 * @property string|null $ruangan_code
 * @property-read mixed $field_code
 * @property-read mixed $field_description
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereRuanganCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereRuanganDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereRuanganId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereRuanganNama($value)
 */
	class Ruangan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $supplier_id
 * @property string|null $supplier_nama
 * @property string|null $supplier_alamat
 * @property string|null $supplier_phone
 * @property string|null $supplier_kontak
 * @property string|null $supplier_email
 * @property-read mixed $field_alamat
 * @property-read mixed $field_contact
 * @property-read mixed $field_email
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed $field_phone
 * @property-read mixed|null $field_primary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierPhone($value)
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $transaksi_id
 * @property string|null $transaksi_key
 * @property string|null $transaksi_status
 * @property string|null $transaksi_rfid
 * @property int|null $transaksi_rs_scan
 * @property int|null $transaksi_rs_ori
 * @property int|null $transaksi_id_ruangan
 * @property string|null $transaksi_beda_rs
 * @property \Carbon\CarbonImmutable|null $transaksi_created_at
 * @property \Carbon\CarbonImmutable|null $transaksi_updated_at
 * @property int|null $transaksi_created_by
 * @property int|null $transaksi_updated_by
 * @property string|null $transaksi_grouping_date
 * @property string|null $transaksi_report_date
 * @property string|null $transaksi_grouping
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read mixed $field_rfid
 * @property-read mixed $field_status
 * @property-read \App\Models\DetailLinen|null $hasDetail
 * @property-read \App\Models\Rs|null $hasRsOri
 * @property-read \App\Models\Rs|null $hasRsScan
 * @property-read \App\Models\Ruangan|null $hasRuangan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiBedaRs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiGrouping($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiGroupingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiIdRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiRfid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiRsOri($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiRsScan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaksi whereTransaksiUpdatedBy($value)
 */
	class Transaksi extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperUser
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property \Carbon\CarbonImmutable|null $verified_at
 * @property string $role
 * @property string|null $avatar
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string $avatar_url
 * @property-read mixed $field_email
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed $field_primary
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rs> $rsList
 * @property-read int|null $rs_list_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User rs(array|string $columns)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerifiedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $warehouse_id
 * @property string|null $warehouse_nama
 * @property-read mixed $field_key
 * @property-read mixed $field_name
 * @property-read mixed|null $field_primary
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Outstanding> $hasStock
 * @property-read int|null $has_stock_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse filter(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse filterBy(array|string $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse filterFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse renamedFilterFields(array $renamedFilterFields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse restrictedFilters(array|string $restrictedFilters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse sort(?array $params = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse sortFields(array|string $fields)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warehouse whereWarehouseNama($value)
 */
	class Warehouse extends \Eloquent {}
}

