<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** A submission from the contact / offerte form. Read-only in Filament. */
#[Fillable([
    'name', 'email', 'phone', 'company', 'subject', 'message', 'source', 'is_handled',
])]
class ContactRequest extends Model
{
    protected function casts(): array
    {
        return ['is_handled' => 'boolean'];
    }
}
