@extends('auth.layouts.panel')

@section('head')
    <title>DISKOMINFO KAB. NIAS UTARA - Login Pages</title>
@endsection

@section('pages')
    <div class="box-auth">
        <div class="box-head">
            <h3 class="text-orange fw-bold">MASUK</h3>
            <p class="mb-0">Selamat Datang di Panel Admin <br> DISKOMINFO Kab. Nias Utara</p>
        </div>
        <div class="box-body pb-5">
            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="mb-2">
                    <label for="Email" class="form-label">Email</label>
                    <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="Email">
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label for="Password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" id="Password">
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                    @enderror
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Remember Me</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary form-control">LOGIN</button>
            </form>
        </div>
    </div>    
@endsection

@section('script')
    
@endsection