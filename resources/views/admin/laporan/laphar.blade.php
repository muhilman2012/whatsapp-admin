@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Laporan Harian</title>
@endsection

@section('pages')
<div class="container-fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <p class="fs-4 fw-bold mb-0">Laporan Harian LaporMasWapres!</p>
        </div>
        <div class="d-block p-3">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.laporan.laphar.exportSingle') }}" method="POST">
                        @csrf
                        <div class="mb-3 col-md-4">
                            <label class="form-label fw-bold" for="date">Tanggal:</label>
                            <input type="date" id="date" name="date" required class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Ekspor PDF per Tanggal</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.laporan.laphar.exportRange') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="from_date">Dari Tanggal:</label>
                                <input type="date" id="from_date" name="from_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="to_date">Sampai Tanggal:</label>
                                <input type="date" id="to_date" name="to_date" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Ekspor PDF Rentang Tanggal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection