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
 * @property string $detail_rfid
 * @property string|null $detail_lama
 * @property string|null $detail_pengantian_user
 * @property string|null $detail_pengantian_waktu
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

