@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Export Laporan</title>
@endsection

@section('pages')
@if (session('success'))
    <div class="alert alert-success d-flex justify-content-between align-items-center">
        <div>
            {{ session('success') }}
            @if (session('download_url'))
                <br>
                <a href="{{ session('download_url') }}" target="_blank" class="btn btn-sm btn-success mt-2">
                    Download File
                </a>
            @endif
        </div>
    </div>
@endif
<div class="container-fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <p class="fs-4 fw-bold mb-0">Ekspor Laporan LaporMasWapres!</p>
        </div>
        <div class="d-block p-3">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('admin.laporan.export.filtered.excel') }}" method="GET">
                        <div class="row">
                            <div class="mb-3 col-md-3">
                                <label class="form-label fw-bold" for="from_date">Dari Tanggal:</label>
                                <input type="date" id="from_date" name="from_date" class="form-control">
                            </div>
                            <div class="mb-3 col-md-3">
                                <label class="form-label fw-bold" for="to_date">Sampai Tanggal:</label>
                                <input type="date" id="to_date" name="to_date" class="form-control">
                            </div>
                            <div class="mb-3 col-md-3">
                                <label class="form-label fw-bold" for="sumber_pengaduan">Sumber Pengaduan:</label>
                                <select name="sumber_pengaduan" id="sumber_pengaduan" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="tatap muka">Tatap Muka</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="surat fisik">Surat Fisik</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Ekspor Excel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection