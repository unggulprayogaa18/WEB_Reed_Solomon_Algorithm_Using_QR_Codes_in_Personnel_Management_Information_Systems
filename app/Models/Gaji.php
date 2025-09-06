<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'gaji';

    /**
     * Nama primary key dari tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_gaji';

    /**
     * Menunjukkan apakah primary key adalah auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true; // id_gaji diasumsikan auto-increment

    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'int'; // Karena id_gaji adalah integer

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'total_jam',
        'tanggal_penggajian',
        'status_persetujuan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_penggajian' => 'date', 
        'total_jam' => 'integer',
        'status_persetujuan' => 'string',
    ];

    /**
     * Hubungan dengan model User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

     public function slipGaji()
    {
        return $this->hasOne(SlipGaji::class, 'id_gaji', 'id_gaji');
    }
}