<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'email', 'subject', 'message', 'ip_address', 'user_agent'])]
class ContactMessage extends BaseModel
{
    public static $filterColumns = ['name', 'email', 'subject'];

    public static $sortColumns = ['created_at', 'name'];

    public static function field_name(): string
    {
        return 'name';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ];
    }
}
