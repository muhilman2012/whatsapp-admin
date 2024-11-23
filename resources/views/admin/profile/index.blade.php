@extends('admin.layouts.panel')

@section('head')
<title>DISKOMINFO Kab. Nias Utara - Profile Admin</title>
<link rel="stylesheet" href="{{ url('/assets/dist/css/admin/profile.css') }}">
@endsection

@section('pages')
<div class="mb-3">
    @livewire('admin.profile.data')
</div>
<div class="mb-3">
    @livewire('admin.profile.password')
</div>
@endsection

@section('script')

@endsection