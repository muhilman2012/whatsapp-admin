@extends('admin.layouts.panel')

@section('head')
    <title>LaporMasWapres! - Dashboard Admin</title>
@endsection

@section('pages')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row bg-white p-3 mb-3">
            <div>
                <p class="fs-4 fw-bold mb-0">Dashboard LaporMasWapres!</p>
                <p class="mb-0">Halo, {{auth('admin')->user()->username}} </p>
            </div>
        </div>
        <div class="d-block mb-3">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-newspaper fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $laporan }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Data Laporan Pengaduan</small>
                        </div>
                        <a href="{{ route('admin.laporan') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    
@endsection