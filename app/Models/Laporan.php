<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'nomor_tiket',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'alamat_lengkap',
        'judul',
        'detail',
        'lokasi',
        'dokumen_pendukung',
        'tanggal_kejadian',
        'status',
        'tanggapan',
        'klasifikasi',
        'kategori',
        'disposisi',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
        'created_at' => 'datetime',
    ];
}
