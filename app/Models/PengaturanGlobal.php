<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanGlobal extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_globals';
    protected $guarded = ['id'];
}
