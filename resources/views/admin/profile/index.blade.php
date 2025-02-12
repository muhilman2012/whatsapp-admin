@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Profile Admin</title>
<link rel="stylesheet" href="{{ url('/assets/dist/css/admin/profile.css') }}">
@endsection

@section('pages')
<div class="mb-3">
    @livewire('admin.profile.data')
</div>
<div class="mb-3">
    @livewire('admin.profile.password')
</div>
<div class="container-fluid mb-3">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom">
            <p class="fs-4 fw-bold mb-0">Log Login Terbaru</p>
        </div>
        <div class="d-block p-3">
            @if ($logs->isEmpty())
                <p>Tidak ada log login terbaru.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal Login</th>
                                <th>IP Address</th>
                                <th>Browser / Perangkat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>{{ $log->user_agent }}</td>  <!-- Menampilkan User Agent -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection