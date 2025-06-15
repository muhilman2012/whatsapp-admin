<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanForwarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_id',
        'institution_id',
        'reason',
        'status',
        'complaint_id',
        'is_anonymous',
        'error_message',
        'scheduled_at',
        'sent_at',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }
}