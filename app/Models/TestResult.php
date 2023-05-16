<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;
    protected $fillable = ['Testas', 'Rezultatas', 'Laikas', 'Testo_parametrai','Sukurtas_serveris', 'Zinute'];
    protected $casts = [
        'Testo_parametrai' => 'array',
        'Sukurtas_serveris' => 'array'
    ];
}
