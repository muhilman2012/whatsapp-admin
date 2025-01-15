@extends('admin.layouts.panel')

@section('head')
<title>Edit User</title>
@endsection

@section('pages')
<div class="container-fluid">
    <h1 class="mt-4">Edit User</h1>

    <form action="{{ route('admin.user_management.update', $user->id_admins) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $user->nama }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
        <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="-- Pilih Role --" {{ $user->role == '-- Pilih Role --' ? 'selected' : '' }}>-- Pilih Role --</option>
                <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="deputi_1" {{ $user->role == 'deputi_1' ? 'selected' : '' }}>Deputi 1</option>
                <option value="deputi_2" {{ $user->role == 'deputi_2' ? 'selected' : '' }}>Deputi 2</option>
                <option value="deputi_3" {{ $user->role == 'deputi_3' ? 'selected' : '' }}>Deputi 3</option>
                <option value="deputi_4" {{ $user->role == 'deputi_4' ? 'selected' : '' }}>Deputi 4</option>
                <option value="asdep" {{ $user->role == 'asdep' ? 'selected' : '' }}>Asdep / Karo</option>
                <option value="analis" {{ $user->role == 'analis' ? 'selected' : '' }}>Analis</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ $user->jabatan }}" required>
        </div>
        <div class="mb-3">
            <label for="deputi" class="form-label">Deputi</label>
            <select class="form-select" id="deputi" name="deputi" required>
                <option selected disabled>-- Pilih Deputi --</option>
                <option value="Admin" {{ $user->deputi == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital" {{ $user->deputi == 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital' ? 'selected' : '' }}>Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital</option>
                <option value="Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia" {{ $user->deputi == 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia' ? 'selected' : '' }}>Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia</option>
                <option value="Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan" {{ $user->deputi == 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan' ? 'selected' : '' }}>Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan</option>
                <option value="Deputi Bidang Administrasi" {{ $user->deputi == 'Deputi Bidang Administrasi' ? 'selected' : '' }}>Deputi Bidang Administrasi</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="unit" class="form-label">Unit</label>
            <select class="form-select" id="unit" name="unit" required>
                <option selected disabled>-- Pilih Unit --</option>
                <option value="Admin" {{ $user->deputi == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Asisten Deputi Ekonomi, Keuangan, dan Transformasi Digital" {{ $user->unit == 'Asisten Deputi Ekonomi, Keuangan, dan Transformasi Digital' ? 'selected' : '' }}>Asisten Deputi Ekonomi, Keuangan, dan Transformasi Digital</option>
                <option value="Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif" {{ $user->unit == 'Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif' ? 'selected' : '' }}>Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif</option>
                <option value="Asisten Deputi Infrastruktur, Sumber Daya Alam, dan Pembangunan Kewilayahan" {{ $user->unit == 'Asisten Deputi Infrastruktur, Sumber Daya Alam, dan Pembangunan Kewilayahan' ? 'selected' : '' }}>Asisten Deputi Infrastruktur, Sumber Daya Alam, dan Pembangunan Kewilayahan</option>
                <option value="Asisten Deputi Pengentasan Kemiskinan dan Pembangunan Desa" {{ $user->unit == 'Asisten Deputi Pengentasan Kemiskinan dan Pembangunan Desa' ? 'selected' : '' }}>Asisten Deputi Pengentasan Kemiskinan dan Pembangunan Desa</option>
                <option value="Asisten Deputi Kesehatan, Gizi, dan Pembangunan Keluarga" {{ $user->unit == 'Asisten Deputi Kesehatan, Gizi, dan Pembangunan Keluarga' ? 'selected' : '' }}>Asisten Deputi Kesehatan, Gizi, dan Pembangunan Keluarga</option>
                <option value="Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana" {{ $user->unit == 'Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana' ? 'selected' : '' }}>Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana</option>
                <option value="Asisten Deputi Pendidikan, Agama, Kebudayaan, Pemuda, dan Olahraga" {{ $user->unit == 'Asisten Deputi Pendidikan, Agama, Kebudayaan, Pemuda, dan Olahraga' ? 'selected' : '' }}>Asisten Deputi Pendidikan, Agama, Kebudayaan, Pemuda, dan Olahraga</option>
                <option value="Asisten Deputi Hubungan Luar Negeri dan Pertahanan" {{ $user->unit == 'Asisten Deputi Hubungan Luar Negeri dan Pertahanan' ? 'selected' : '' }}>Asisten Deputi Hubungan Luar Negeri dan Pertahanan</option>
                <option value="Asisten Deputi Politik, Keamanan, Hukum, dan Hak Asasi Manusia" {{ $user->unit == 'Asisten Deputi Politik, Keamanan, Hukum, dan Hak Asasi Manusia' ? 'selected' : '' }}>Asisten Deputi Politik, Keamanan, Hukum, dan Hak Asasi Manusia</option>
                <option value="Asisten Deputi Tata Kelola Pemerintahan dan Percepatan Pembangunan Daerah" {{ $user->unit == 'Asisten Deputi Tata Kelola Pemerintahan dan Percepatan Pembangunan Daerah' ? 'selected' : '' }}>Asisten Deputi Tata Kelola Pemerintahan dan Percepatan Pembangunan Daerah</option>
                <option value="Biro Tata Usaha dan Sumber Daya Manusia" {{ $user->unit == 'Biro Tata Usaha dan Sumber Daya Manusia' ? 'selected' : '' }}>Biro Tata Usaha dan Sumber Daya Manusia</option>
                <option value="Biro Perencanaan dan Keuangan" {{ $user->unit == 'Biro Perencanaan dan Keuangan' ? 'selected' : '' }}>Biro Perencanaan dan Keuangan</option>
                <option value="Biro Umum" {{ $user->unit == 'Biro Umum' ? 'selected' : '' }}>Biro Umum</option>
                <option value="Biro Protokol dan Kerumahtanggaan" {{ $user->unit == 'Biro Protokol dan Kerumahtanggaan' ? 'selected' : '' }}>Biro Protokol dan Kerumahtanggaan</option>
                <option value="Biro Pers, Media, dan Informasi" {{ $user->unit == 'Biro Pers, Media, dan Informasi' ? 'selected' : '' }}>Biro Pers, Media, dan Informasi</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
