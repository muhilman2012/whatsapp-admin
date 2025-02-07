<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Muhammad Hilman">
    <meta name="msapplication-navbutton-color" content="#ffffff" />
    <meta name="apple-mobile-web-app-status-bar-style" content="#ffffff" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <a class="nav-link text-dark ps-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">  
                            <i class="fa fa-bell fa-lg py-2" aria-hidden="true"></i>  
                            <span class="badge bg-danger" id="unread-count">0</span>  
                        </a>  
                        <div class="dropdown-menu mt-2 pt-0" aria-labelledby="navbarDropdown" id="notification-dropdown">  
                            <div class="d-flex p-3 border-bottom align-items-center mb-2">
                                <i class="fa fa-bell me-3" aria-hidden="true"></i>  
                                <span class="fw-bold lh-1">Notifikasi</span>  
                            </div>  
                            <div id="notification-list" style="max-height: 300px; overflow-y: auto;">  
                                <!-- Notifications will be loaded here -->  
                                <div id="no-notifications" class="text-center" style="display: none;">
                                    <p>Belum ada notifikasi baru.</p>
                                </div>
                            </div>  
                        </div>  
                    </li>
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
                        <i class="fa fa-home box-icon" aria-hidden="true"></i>Beranda
                    </a>
                    <a class="nav-link px-3" href="{{ route('admin.profile') }}">
                        <i class="fas fa-user box-icon" aria-hidden="true"></i>Profile
                    </a>
                    <hr class="soft my-1 text-white">
                    <a class="nav-link px-3" href="{{ route('admin.laporan', ['type' => 'all']) }}">
                        <i class="fas fa-newspaper box-icon text-center"></i>Kelola Pengaduan
                    </a>
                    <a class="nav-link px-3" href="{{ route('admin.laporan', ['type' => 'pelimpahan']) }}">
                        <i class="fas fa-bookmark box-icon text-center fa-fw "></i>Pelimpahan
                    </a>
                    <a class="nav-link px-3" href="{{ route('admin.laporan.create') }}">
                        <i class="fas fa-newspaper box-icon" aria-hidden="true"></i>Tambah Pengaduan
                    </a>
                    @if (auth('admin')->user()->role === 'superadmin')
                    <hr class="soft my-1 text-white">
                    <a class="nav-link px-3" href="{{ route('admin.user_management.index') }}">
                        <i class="fa fa-users box-icon" aria-hidden="true"></i>Manajemen User
                    </a>
                    @endif
                    <hr class="soft my-1 text-white">
                    <a class="btnLogout nav-link px-3" href="#">
                        <i class="fas fa-sign-out-alt box-icon"></i>Keluar
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
            title: 'Sukses!',
            text: '{{ session()->get("success") }}',
        })
    </script>
    @elseif(session()->has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Maaf..',
            text: '{{ session()->get("error") }}',
        })
    </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            updateNotifications();

            async function updateNotifications() {
                try {
                    const analystData = await fetchNotificationsForAnalyst();
                    const roleData = await fetchNotificationsForRole();

                    const totalUnread = analystData.unreadCount + roleData.unreadCount;
                    const unreadCountElement = document.getElementById('unread-count');
                    const noNotificationsElement = document.getElementById('no-notifications');
                    const notificationList = document.getElementById('notification-list');

                    // Tampilkan jumlah notifikasi atau kosongkan jika tidak ada
                    unreadCountElement.innerText = totalUnread > 0 ? totalUnread : '0';

                    // Kosongkan daftar notifikasi sebelum menambahkan yang baru
                    notificationList.innerHTML = '';

                    // Periksa jika ada notifikasi
                    if (totalUnread === 0) {
                        noNotificationsElement.style.display = 'block';  // Tampilkan pesan "Belum ada notifikasi"
                    } else {
                        noNotificationsElement.style.display = 'none';   // Sembunyikan pesan "Belum ada notifikasi"
                        renderNotifications(analystData.notifications, 'Nomor Pengaduan');
                        renderNotifications(roleData.notifications, 'Pelimpahan Laporan');
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            }

            async function fetchNotificationsForAnalyst() {
                try {
                    const response = await fetch('/admin/dashboard/notifications/analyst');
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching analyst notifications:', error);
                    return { notifications: [], unreadCount: 0 };
                }
            }

            async function fetchNotificationsForRole() {
                try {
                    const response = await fetch('/admin/dashboard/notifications/role-based');
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching role-based notifications:', error);
                    return { notifications: [], unreadCount: 0 };
                }
            }

            function renderNotifications(notifications, titlePrefix) {
                const notificationList = document.getElementById('notification-list');

                if (notifications.length === 0) return;

                notifications.forEach(notification => {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item py-2 overflow-hidden text-truncate bg-light';

                    item.innerHTML = `
                        <a href="/admin/dashboard/laporan/${notification.laporan.nomor_tiket}" class="d-block text-decoration-none text-dark">
                            <p class="lh-1 mb-0 fw-bold">${titlePrefix} #${notification.laporan.nomor_tiket}</p>
                            <small class="content-text">${notification.message}</small><br>
                            <small class="content-text">Dari: ${notification.assigner.nama}</small>
                        </a>
                        <small class="text-primary d-block mt-1 tandai-sudah-dibaca" style="cursor: pointer;" data-id="${notification.id}">Tandai sudah dibaca</small>
                    `;

                    const markAsReadButton = item.querySelector('.tandai-sudah-dibaca');
                    markAsReadButton.addEventListener('click', async function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        await markNotificationAsRead(notification.id);
                        updateNotifications(); // Refresh setelah menandai sebagai dibaca
                    });

                    notificationList.appendChild(item);
                });
            }

            async function markNotificationAsRead(notificationId) {
                try {
                    const response = await fetch('/admin/dashboard/notifications/mark-as-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id: notificationId })
                    });

                    const result = await response.json();

                    if (result.success) {
                        console.log('Notifikasi berhasil ditandai sebagai dibaca');
                    } else {
                        console.error('Gagal menandai notifikasi:', result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        });
    </script>
</body>

</html>