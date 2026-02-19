<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketLangganan extends Model
{
    use HasFactory;

    protected $table = 'paket_langganan';
    protected $guarded = ['id'];
    protected $casts = [
        'fitur' => 'array',
        'harga' => 'decimal:2',
    ];
}
