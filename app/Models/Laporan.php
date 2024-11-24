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
        'nomor_pengadu',
        'email',
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
        'sumber_pengaduan',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'tanggapan' => 'Laporan pengaduan Anda dalam proses verifikasi & penelaahan, sesuai ketentuan akan dilakukan dalam 14 (empat belas) hari kerja sejak laporan lengkap diterima.',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($laporan) {
            $laporan->deadline = now()->addDays(20)->format('Y-m-d'); // Tambahkan 20 hari dari tanggal saat ini
        });
    }

    public function getSisaHariAttribute()
    {
        $deadline = $this->created_at->addDays(20); // Deadline adalah 20 hari setelah created_at
        $hariTersisa = now()->diffInDays($deadline, false); // Hitung selisih dalam hari (bisa negatif)

        if ($hariTersisa > 0) {
            return "$hariTersisa hari lagi";
        } elseif ($hariTersisa === 0) {
            return "Hari ini";
        } else {
            return "Terlambat " . abs($hariTersisa) . " hari";
        }
    }
}
