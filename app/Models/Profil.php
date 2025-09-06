<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory;

    protected $table = 'profil';

    protected $primaryKey = 'id_profile';

    protected $fillable = [
        'id_user',
        'alamat',
        'tanggal_lahir',
        'detail_pekerjaan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Hubungan dengan model User (profil dimiliki oleh satu user).
     * Ini adalah sisi "belongsTo" untuk hubungan one-to-one.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

