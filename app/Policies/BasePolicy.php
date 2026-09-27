<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class BasePolicy
{
    protected $module;

    protected $restrict;

    public function __construct()
    {
        $this->module = request()->route()?->getAction('name');
        $this->restrict = config('permision');
    }

    /**
     * ponytail: central gate — jalan SEBELUM method ability mana pun.
     * Modul yang terdaftar di config/permision.php (allowlist role) dikunci
     * di sini: role di luar daftar = deny untuk SEMUA ability modul itu
     * (termasuk ability tanpa method policy seperti print/exportexcel).
     * Modul tak terdaftar = null = perilaku lama (logika per-method).
     */
    public function before(?User $user, string $ability): ?Response
    {
        $name = request()->route()?->getName();

        if (! is_string($name) || $name === '') {
            return null;
        }

        $module = explode('.', $name, 2)[0];
        $allowed = config('permision.'.$module);

        if ($allowed === null) {
            return null;
        }

        if ($user && in_array($user->role ?? 'guest', (array) $allowed, true)) {
            return Response::allow();
        }

        return Response::deny('Modul '.$module.' khusus role: '.implode(', ', (array) $allowed).'.');
    }

    private function accessProtected($user, $permision)
    {
        $role = $user->role ?? 'guest';

        if (isset($this->restrict[$role][$this->module])) {

            if (in_array($permision, $this->restrict[$role][$this->module])) {
                return true;
            }
        }

        return false;
    }

    public function save(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function create(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function update(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function table(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function delete(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function show(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function parstock(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function prepare(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function prepareSo(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function storeship(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function sync(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }

    public function capture(User $user): Response
    {
        return $this->accessProtected($user, __FUNCTION__) ? Response::deny() : Response::allow();
    }
}
