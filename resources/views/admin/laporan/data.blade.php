@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - {{ $pageTitle }}</title>
@endsection

@section('pages')
<div class="container-fluid">
    <!-- Header Halaman -->
    <div class="d-block rounded bg-white shadow p-3 mb-3">
        <p class="fs-4 fw-bold mb-0">{{ $pageTitle }}</p>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
        </ol>
    </div>

    <!-- Konten Halaman -->
    <div class="d-block rounded bg-white shadow p-3 mb-3">
        @if ($type === 'pelimpahan')
            @livewire('admin.laporan.pelimpahan', ['data' => $data])
        @elseif ($type === 'rejected')
            @livewire('admin.laporan.rejected', ['data' => $data])
        @elseif ($type === 'approved')
            @livewire('admin.laporan.approved', ['data' => $data])
        @else
            @livewire('admin.laporan.data', ['data' => $data])
        @endif
    </div>
</div>
@endsection

@section('script')

@endsection