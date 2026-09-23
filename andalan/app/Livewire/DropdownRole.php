<?php

namespace App\Livewire;

use App\Dao\Enums\RoleType;
use App\Dao\Models\Rs;
use App\Dao\Models\SystemRole;
use Livewire\Component;

class DropdownRole extends Component
{
    public $role_id;
    public $rs_id;

    public $data_role;
    public $data_rs;

    public function change(){

    }

    public function render()
    {
        $hide = true;

        if ($this->role_id == RoleType::Customer) {
            $hide = false;
        }

        $this->data_role = SystemRole::getOptions();
        $this->data_rs = Rs::getOptions();

        return view('livewire.dropdown-role', [
            'hide' => $hide,
        ]);
    }
}
