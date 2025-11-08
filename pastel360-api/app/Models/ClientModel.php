<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientModel extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'mail',
        'phone',
        'birthdate',
        'place',
        'number',
        'zipcode',
        'district',
        'complement'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
}
