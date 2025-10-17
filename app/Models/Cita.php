<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'id',
        'title',
        'description',
        'date',
        'user'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
