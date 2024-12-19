<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Muhammad Hilman">
    <meta name="msapplication-navbutton-color" content="#ffffff" />
    <meta name="apple-mobile-web-app-status-bar-style" content="#ffffff" />
    <link rel="icon" type="image/png" href="{{asset('/images/logo/LaporMasWapres.png')}}" />
    @yield('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/assets/app/css/app.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/icons/css/all.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/dist/css/color.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/dist/css/animated.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/dist/css/admin/panel.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/dist/css/admin/color.css') }}">
    @livewireStyles
</head>

<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-md navbar-light bg-light py-1">
            <div class="container-fluid">
                <button class="btn btn-default" id="btn-slider" type="button">
                    <i class="fa fa-bars fa-lg" aria-hidden="true"></i>
                </button>
                <a class="navbar-brand me-auto text-danger" href="#">Dash<span class="text-warning">board</span></a>
                <ul class="nav ms-auto">
                    <li class="nav-item dropstart">
                        <a class="nav-link text-dark ps-3 pe-1" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            @if (auth('admin')->user()->avatar == 'sample-images.png')
                            <img src="{{ url('images/avatar/' . auth('admin')->user()->avatar) }}"
                                alt="{{auth('admin')->user()->username}}" class="img-user" width="64px" height="64px">
                            @else
                            <img src="{{ url('images/avatar/admin/' . auth('admin')->user()->avatar) }}"
                                alt="{{auth('admin')->user()->username}}" class="img-user" width="64px" height="64px">
                            @endif
                        </a>
                        <div class="dropdown-menu mt-2 pt-0" aria-labelledby="navbarDropdown">
                            <div class="d-flex p-3 border-bottom mb-2">
                                @if (auth('admin')->user()->avatar == 'sample-images.png')
                                <img src="{{ url('images/avatar/' . auth('admin')->user()->avatar) }}"
                                    alt="{{auth('admin')->user()->username}}" class="img-user me-2" width="64px"
                                    height="64px">
                                @else
                                <img src="{{ url('images/avatar/admin/' . auth('admin')->user()->avatar) }}"
                                    alt="{{auth('admin')->user()->username}}" class="img-user me-2" width="64px"
                                    height="64px">
                                @endif
                                <div class="d-block mt-1">
                                    <p class="fw-bold m-0 lh-1">{{auth('admin')->user()->username}}</p>
                                    <small>{{auth('admin')->user()->email}}</small>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="fa fa-user fa-lg me-3" aria-hidden="true"></i>Profile
                            </a>
                            <hr class="dropdown-divider">
                            <a class="btnLogout dropdown-item" href="#">
                                <i class="fa fa-sign-out fa-lg me-2" aria-hidden="true"></i>Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="slider slider-theme" id="sliders">
            <div class="slider-head">
                <div class="d-block p-3">
                    @if (auth('admin')->user()->avatar == 'sample-images.png')
                    <img src="{{ url('images/avatar/' . auth('admin')->user()->avatar) }}"
                        alt="{{auth('admin')->user()->username}}" class="slider-img-user mb-2" width="64px"
                        height="64px">
                    @else
                    <img src="{{ url('images/avatar/admin/' . auth('admin')->user()->avatar) }}"
                        alt="{{auth('admin')->user()->username}}" class="slider-img-user mb-2" width="64px"
                        height="64px">
                    @endif
                    <p class="fw-bold mb-0 lh-1 text-white">{{auth('admin')->user()->username}}</p>
                    <small class="text-white">{{auth('admin')->user()->email}}</small>
                </div>
            </div>
            <div class="slider-body px-1 pb-4">
                <nav class="nav flex-column" id="nav-acordion" role="tablist" aria-multiselectable="true">
                    <a class="nav-link px-3 active" href="{{ route('admin.index') }}">
                        <i class="fa fa-home box-icon" aria-hidden="true"></i>Home
                    </a>
                    <a class="nav-link px-3" href="{{ route('admin.profile') }}">
                        <i class="fas fa-user box-icon" aria-hidden="true"></i>Profile
                    </a>
                    <hr class="soft my-1 text-white">
                    <a class="nav-link collapsed" href="#laporan" type="button" data-bs-toggle="collapse"
                        data-bs-target="#laporan">
                        <i class="fas fa-newspaper box-icon"></i>Kelola Pengaduan
                        <span class="indications">
                            <i class="fas fa-angle-up fa-sm fa-fw"></i>
                        </span>
                    </a>
                    <div id="laporan" class="accordion-collapse collapse" data-bs-parent="#nav-accordion">
                        <a class="nav-link nav-link-child" href="{{ route('admin.laporan', ['type' => 'all']) }}">
                            <i class="fas fa-newspaper box-icon text-center"></i>Semua Pengaduan
                        </a>
                        <a class="nav-link nav-link-child" href="{{ route('admin.laporan.create') }}">
                            <i class="fas fa-newspaper box-icon text-center fa-fw "></i>Input Pengaduan
                        </a>
                        <a class="nav-link nav-link-child" href="{{ route('admin.laporan', ['type' => 'pelimpahan']) }}">
                            <i class="fas fa-newspaper box-icon text-center fa-fw "></i>Pelimpahan
                        </a>
                        <a class="nav-link nav-link-child" href="{{ route('admin.laporan', ['type' => 'pending']) }}">
                            <i class="fas fa-newspaper box-icon text-center fa-fw "></i>Laporan Pending
                        </a>
                        <a class="nav-link nav-link-child" href="{{ route('admin.laporan', ['type' => 'rejected']) }}">
                            <i class="fas fa-newspaper box-icon text-center fa-fw "></i>Laporan Revised
                        </a>
                        <a class="nav-link nav-link-child" href="{{ route('admin.laporan', ['type' => 'approved']) }}">
                            <i class="fas fa-newspaper box-icon text-center fa-fw "></i>Laporan Approved
                        </a>
                    </div>
                    <hr class="soft my-1 text-white">
                    <a class="btnLogout nav-link px-3" href="#">
                        <i class="fas fa-sign-out-alt box-icon"></i>Logout
                    </a>
                </nav>
            </div>
        </div>

        <div class="main-pages">
            @yield('pages')
        </div>
    </div>

    <div class="slider-background" id="sliders-background"></div>
    <script src="{{ url('/assets/dist/js/jquery.js') }}"></script>
    <script src="{{ url('/assets/dist/js/popper.js') }}"></script>
    <script src="{{ url('/assets/app/js/app.js') }}"></script>
    <script src="{{ url('/assets/dist/js/alert.js') }}"></script>
    <script src="{{ url('/assets/dist/js/admin/panel.js') }}"></script>
    <script src="{{ asset('/assets/ckeditor4/ckeditor.js') }}"></script>
    <script src="{{ asset('/assets/owl/owl.carousel.min.js') }}"></script>
    @livewireScripts
    @yield('script')

    @if(session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Good Jobs!',
            text: '{{ session()->get("success") }}',
        })
    </script>
    @elseif(session()->has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Opps...!',
            text: '{{ session()->get("error") }}',
        })
    </script>
    @endif
</body>

</html>