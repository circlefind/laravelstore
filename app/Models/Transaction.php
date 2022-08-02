<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'datetr',
        'iduser',
        'idcourier',
        'weight',
        'receiptnumber',
        'couriercost',
        'idbank',
        'uniquecode',
        'transfer',
        'status'
    ];
}
