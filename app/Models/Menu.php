<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Menu extends Model
{
    protected $guarded = [];

    // 1. Sembunyikan Es Teh Manis secara otomatis dari sistem
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('hideEsTehManis', function (Builder $builder) {
            $builder->where('name', 'NOT LIKE', '%Es Teh Manis%');
        });
    }

    // 2. Ubah nama "Es Jeruk Peras" jadi "Nutrisari" secara otomatis saat dipanggil
    public function getNameAttribute($value)
    {
        return Str::contains(Str::lower($value), 'es jeruk peras') ? 'Nutrisari' : $value;
    }

    // 3. Sesuaikan deskripsinya juga
    public function getDescriptionAttribute($value)
    {
        return Str::contains(Str::lower($this->attributes['name'] ?? ''), 'es jeruk peras') 
               ? 'Minuman buah segar instan kaya vitamin C.' 
               : $value;
    }
}