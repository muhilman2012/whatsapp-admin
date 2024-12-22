@extends('admin.layouts.panel')

@section('head')
<title>Tambah User</title>
@endsection

@section('pages')
<div class="container-fluid">
    <h1 class="mt-4">Tambah User</h1>

    <form action="{{ route('admin.user_management.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option disabled selected>-- Pilih Role --</option>
                <option value="deputi_1">Deputi 1</option>
                <option value="deputi_2">Deputi 2</option>
                <option value="deputi_3">Deputi 3</option>
                <option value="deputi_4">Deputi 4</option>
                <option value="analis">Analis</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" class="form-control" id="jabatan" name="jabatan" required>
        </div>
        <div class="mb-3">
            <label for="deputi" class="form-label">Deputi</label>
            <select class="form-select" id="deputi" name="deputi" required>
                <option disabled selected>-- Pilih Deputi --</option>
                <option value="Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital">Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital</option>
                <option value="Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia">Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia</option>
                <option value="Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan">Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan</option>
                <option value="Deputi Bidang Administrasi">Deputi Bidang Administrasi</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <select class="form-select" id="unit" name="unit" required>
                <option disabled selected>-- Pilih Unit --</option>
                <option value="Asisten Deputi Ekonomi dan Keuangan">Asisten Deputi Ekonomi dan Keuangan</option>
                <option value="Asisten Deputi Infrastruktur, Ketahanan Energi, dan Sumber Daya Alam">Asisten Deputi Infrastruktur, Ketahanan Energi, dan Sumber Daya Alam</option>
                <option value="Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif">Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif</option>
                <option value="Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana">Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana</option>
                <option value="Asisten Deputi Pembangunan Sumber Daya Manusia">Asisten Deputi Pembangunan Sumber Daya Manusia</option>
                <option value="Asisten Deputi Penanggulangan Kemiskinan">Asisten Deputi Penanggulangan Kemiskinan</option>
                <option value="Asisten Deputi Tata Kelola Pemerintahan">Asisten Deputi Tata Kelola Pemerintahan</option>
                <option value="Asisten Deputi Wawasan Kebangsaan, Pertahanan, dan Keamanan">Asisten Deputi Wawasan Kebangsaan, Pertahanan, dan Keamanan</option>
                <option value="Asisten Deputi Politik, Hukum, dan Otonomi Daerah">Asisten Deputi Politik, Hukum, dan Otonomi Daerah</option>
                <option value="Asisten Deputi Hubungan Luar Negeri">Asisten Deputi Hubungan Luar Negeri</option>
                <option value="Biro Perencanaan dan Keuangan">Biro Perencanaan dan Keuangan</option>
                <option value="Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian">Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian</option>
                <option value="Biro Pers, Media, dan Informasi">Biro Pers, Media, dan Informasi</option>
                <option value="Biro Protokol dan Kerumahtanggaan">Biro Protokol dan Kerumahtanggaan</option>
                <option value="Biro Umum">Biro Umum</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
