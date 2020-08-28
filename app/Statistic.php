<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'ip',
        'session_id',
        'user_id',
        'status_code',
        'uri',
        'method',
        'server',
        'input',
        'created_at',
        'updated_at',
    ];


    protected $casts = [
        'ip' => 'string',
        'session_id' => 'string',
        'user_id' => 'integer',
        'status_code' => 'integer',
        'uri' => 'string',
        'method' => 'string',
        'server' => 'json',
        'input' => 'json',
        'created_at' => 'datetime',
    ];




    protected $rules = [
        'ip' => 'required|string',
        'session_id' => 'required|string',
        'user_id' => 'nullable|integer',
        'status_code' => 'required|integer',
        'uri' => 'required|string',
        'method' => 'required|string',
        'server' => 'required|array',
        'input' => 'nullable|array',
        'created_at' => 'required|date',
    ];

}
