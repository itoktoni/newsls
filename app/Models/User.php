<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use App\Notifications\ResetPasswordNotification;
use App\Properties\UserEntity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperUser
 */
#[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar', 'verified_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use DefaultEntity, Filterable, HasApiTokens, HasFactory, Notifiable, OptionTrait, Sortable, TwoFactorAuthenticatable, UserEntity;

    protected $table = 'users';

    protected $keyType = 'int';

    protected $primaryKey = 'id';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'role' => 'Role',
    ];

    public static $sortColumns = [
        'name',
        'email',
        'phone',
        'role',
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string',
            'role' => 'string',
            'password' => 'string',
            'avatar' => 'nullable|string|max:255',
        ];
    }

    public static function field_name(): string
    {
        return 'name';
    }

    /**
     * Get the user's avatar public URL. Empty string if none.
     */
    public function getAvatarUrlAttribute(): string
    {
        return fileUrl($this->avatar);
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * RS yang boleh diakses user (pivot rs_dan_user).
     */
    public function rsList()
    {
        return $this->belongsToMany(Rs::class, 'rs_dan_user', 'user_id', 'rs_id');
    }

    public static function rsIdsFor(int $userId): array
    {
        return DB::table('rs_dan_user')
            ->where('user_id', $userId)
            ->pluck('rs_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * Ganti daftar RS user (checkbox form user). Kosong = akses semua.
     */
    public static function syncRs(int $userId, array $rsIds): void
    {
        $rsIds = array_values(array_unique(array_filter(array_map(
            fn ($v) => is_numeric($v) ? (int) $v : null,
            $rsIds
        ))));
        $valid = Rs::whereIn('rs_id', $rsIds)->pluck('rs_id')->map(fn ($v) => (int) $v)->all();

        DB::transaction(function () use ($userId, $valid) {
            DB::table('rs_dan_user')->where('user_id', $userId)->delete();
            foreach ($valid as $rsId) {
                DB::table('rs_dan_user')->insert([
                    'rs_id' => $rsId,
                    'user_id' => $userId,
                ]);
            }
        });
    }

    /**
     * ID RS yang boleh dilihat user login. null = semua (tanpa mapping).
     */
    public static function allowedRsIds(): ?array
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }
        $ids = static::rsIdsFor((int) $user->id);

        return $ids === [] ? null : $ids;
    }

    /**
     * Opsi dropdown RS yang sudah disaring hak akses.
     */
    public static function rsOptions(): array
    {
        $query = Rs::orderBy('rs_nama');
        if (($ids = static::allowedRsIds()) !== null) {
            $query->whereIn('rs_id', $ids);
        }

        return $query->pluck('rs_nama', 'rs_id')->all();
    }

    /**
     * Batasi query ke RS yang boleh diakses (null = semua).
     * $columns: satu/daftar kolom qualified untuk whereIn (OR antar kolom).
     */
    public static function scopeRs($query, string|array $columns)
    {
        $ids = static::allowedRsIds();
        if ($ids === null) {
            return $query;
        }
        $columns = (array) $columns;

        return $query->where(function ($q) use ($columns, $ids) {
            foreach ($columns as $i => $col) {
                $i === 0 ? $q->whereIn($col, $ids) : $q->orWhereIn($col, $ids);
            }
        });
    }

    /**
     * Tolak RS di luar hak user (pivot rs_dan_user). Tanpa mapping = semua boleh.
     */
    public static function ensureRsAccess(int $rsId): void
    {
        $ids = static::allowedRsIds();
        if ($ids !== null && ! in_array($rsId, $ids, true)) {
            abort(403, 'Akses rumah sakit ditolak.');
        }
    }

    /**
     * Filter query ke satu RS bila $rsId diisi (sekaligus guard akses),
     * atau ke semua RS milik user bila kosong. Tanpa mapping = tanpa filter.
     * Mendukung satu kolom (where/whereIn) atau banyak kolom (OR whereIn),
     * karena transaksi/outstanding punya 2 kolom RS (ori + scan).
     */
    public static function applyRsFilter($query, string|array $columns, mixed $rsId = null)
    {
        $columns = (array) $columns;

        if ($rsId !== null && $rsId !== '') {
            static::ensureRsAccess((int) $rsId);

            if (count($columns) === 1) {
                return $query->where($columns[0], (int) $rsId);
            }

            return $query->where(function ($q) use ($columns, $rsId) {
                foreach ($columns as $i => $col) {
                    $i === 0 ? $q->where($col, (int) $rsId) : $q->orWhere($col, (int) $rsId);
                }
            });
        }

        return static::scopeRs($query, $columns);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
