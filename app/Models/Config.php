<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'corporatename',
        'phone',
        'fax',
        'email',
        'facebook',
        'whatsapp',
        'about',
        'transmethod',
        'imglogo'
    ];
}
