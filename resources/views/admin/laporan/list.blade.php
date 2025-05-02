@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Daftar Pengadu</title>
@endsection

@section('pages')
<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="d-block rounded bg-white shadow p-3 mb-3">
        <p class="fs-4 fw-bold mb-0">Daftar Pengadu</p>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daftar Pengadu</li>
        </ol>
    </div>

    <!-- Konten Halaman -->
    <div class="d-block rounded bg-white shadow p-3 mb-3">
        @livewire('admin.identitas.list-pengadu')
    </div>
</div>
@endsection

@section('script')

@endsection