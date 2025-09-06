<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinSakit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'izin_sakits';

    /**
     * The attributes that are mass assignable.
     *
     * These are the fields that can be filled in when creating a new record
     * using the IzinSakit::create() method.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'detail_pekerjaan',
        'aktivitas',
        'jenis_pembayaran',
        'tanggal_izin',
        'keterangan',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * This ensures that the 'tanggal_izin' field is always treated as a Carbon date object.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_izin' => 'date',
    ];

    /**
     * Get the user that owns the leave request.
     *
     * This defines the inverse of a one-to-many relationship.
     * Each leave request belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
