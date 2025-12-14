<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denomination extends Model
{
    protected $fillable = ['value', 'available_count'];

    public function transactions(): HasMany
    {
        return $this->hasMany(DenominationTransaction::class);
    }
}
