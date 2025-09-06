<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk UUID

class PegawaiActivity extends Model
{
    use HasFactory;

    protected $table = 'pegawai_activities';

    protected $primaryKey = 'id_activity';


    protected $fillable = [
        'nama_aktivitas',
        'deskripsi',
        'jam_mulai',
        'jam_selesai',
        'uuid',
        'qrcode_path',
    ];

    // Atribut yang harus dikonversi ke tipe data native
    protected $casts = [
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    /**
     * Boot the model.
     * Atur event listener untuk secara otomatis menghasilkan UUID saat model dibuat.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Hubungan dengan model Presensi (jika Anda ingin mengambil presensi terkait aktivitas ini)
     */
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_activity', 'id_activity');
    }
}
