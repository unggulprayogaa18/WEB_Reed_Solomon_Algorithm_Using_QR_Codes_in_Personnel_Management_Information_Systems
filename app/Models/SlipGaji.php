<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlipGaji extends Model
{
    use HasFactory;

    protected $table = 'slip_gaji';

    protected $primaryKey = 'id_slip_gaji';

   protected $fillable = [
        'id_gaji',
        'periode',
        'tipe_pembayaran',
        'gaji_berdasarkan_tipe',
        'tunjangan',
        'total_gaji',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data native.
     * Ini penting untuk memastikan nilai desimal ditangani sebagai angka, bukan string.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gaji_berdasarkan_tipe' => 'decimal:2',
        'tunjangan' => 'decimal:2',
        'total_gaji' => 'decimal:2',
    ];


    /**
     * Hubungan dengan model Gaji (slip gaji dimiliki oleh satu entri gaji).
     */
    public function gaji()
    {
        return $this->belongsTo(Gaji::class, 'id_gaji', 'id_gaji');
    }
}

