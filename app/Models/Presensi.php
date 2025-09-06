<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'presensi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'id_activity',
        'status',        // 'masuk' atau 'keluar'
        'izin_sakit_id', // Foreign key ke tabel izin_sakits
        'created_at',    // Wajib ada karena kita set manual di controller
        'updated_at',    // Wajib ada karena kita set manual di controller
    ];

    /**
     * Get the user that owns the presensi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the activity associated with the presensi.
     */
    public function activity()
    {
        return $this->belongsTo(PegawaiActivity::class, 'id_activity', 'id_activity');
    }

    /**
     * Get the leave request that caused this presensi.
     */
    public function izinSakit()
    {
        return $this->belongsTo(IzinSakit::class, 'izin_sakit_id');
    }
}
