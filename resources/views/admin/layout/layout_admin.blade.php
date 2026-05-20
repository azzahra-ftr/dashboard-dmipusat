<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMI Dashboard</title>
    <link rel="shortcut icon" href="{{ asset('admin-assets/img/logo dmi.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/layout_admin.css') }}">
    @stack('after-style')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body>

    {{-- Sidebar --}}
    <aside class="sidebar">

        {{-- Brand / Logo --}}
        <div class="sidebar-brand">
            <img class="logo-img" src="{{ asset('admin-assets/img/logo.png')}}" alt="Logo DMI"/>
            <div class="brand-text">
                <span class="brand-name">Dewan Masjid Indonesia</span>
            </div>
        </div>

        {{-- Upload Berita --}}
        <div class="sidebar-upload">
            <a href="{{ route('posts.create') }}" class="btn-upload">
                <i class='bx bx-cloud-upload'></i>
                <span>Upload Berita</span>
            </a>
        </div>

        {{-- Nav List --}}
        <ul class="nav-list">
            <li class="{{ request()->routeIs('home.home') ? 'active' : '' }}">
                <a href="{{ route('home.home') }}">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Home</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('posts.*') ? 'active' : '' }}">
                <a href="{{ route('posts.index') }}">
                    <i class='bx bx-news'></i>
                    <span class="links_name">All Post</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('events.*') ? 'active' : '' }}">
                <a href="{{ route('events.index') }}">
                    <i class='bx bx-calendar-event'></i>
                    <span class="links_name">Event</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a href="#">
                    <i class='bx bx-user'></i>
                    <span class="links_name">User</span>
                </a>
            </li>
        </ul>

        {{-- Footer: Profil + Logout --}}
        <div class="sidebar-footer">

            {{-- Profil Admin --}}
            <div class="sidebar-profile">
                <div class="sidebar-profile-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->display_name ?? 'Admin') }}&background=2E7D32&color=fff&format=svg" alt="Avatar">
                </div>
                <div class="sidebar-profile-info">
                    <span class="sidebar-profile-name">{{ Auth::user()->display_name ?? 'Admin' }}</span>
                    <span class="sidebar-profile-email">{{ Auth::user()->user_email ?? '' }}</span>
                </div>
                <div class="sidebar-profile-dots">
                    <i class='bx bx-dots-vertical-rounded'></i>
                </div>
            </div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
            </form>
            <button class="sidebar-logout-btn-link" onclick="document.getElementById('logoutForm').submit()">
                <i class='bx bx-log-out'></i>
                <span>Logout</span>
            </button>

        </div>

    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Konten Utama --}}
    <section class="home-section">

        {{-- Navbar --}}
        <nav class="navbar">

            {{-- Kiri: Hamburger + Greeting --}}
            <div class="navbar-left">
                <button class="navbar-hamburger" id="btn-menu">
                    <i class='bx bx-menu'></i>
                </button>
                <div class="navbar-greeting">
                    <h1 class="greeting-title">
                        Selamat datang, {{ Auth::user()->display_name ?? 'Admin' }} 👋
                    </h1>
                    <p class="greeting-sub">Berikut ringkasan aktivitas website DMI hari ini.</p>
                </div>
            </div>

            {{-- Tengah: Search --}}
            <div class="navbar-search">
                <i class='bx bx-search'></i>
                <input type="text" id="desktopSearch" placeholder="Cari berita, event, atau kategori...">
                <button class="search-btn"><i class='bx bx-search'></i></button>
            </div>

            {{-- Kanan: Bell + Profil --}}
            <div class="nav-icons">
                <div class="notification">
                    <i class='bx bx-bell'></i>
                    <span class="notif-badge">3</span>
                </div>
                <div class="profile-details" onclick="toggleDropdown()">
                    <div class="profile-content">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->display_name ?? 'Admin') }}&background=2E7D32&color=fff&format=svg" alt="profileImg">
                    </div>
                    <div class="name-job desktop-only">
                        <div class="profile_name">{{ Auth::user()->display_name ?? 'Admin' }}</div>
                    </div>
                    <i class='bx bx-chevron-down desktop-only'></i>
                </div>

                {{-- Dropdown --}}
                <div class="dropdown-menu" id="profileDropdown">
                    <div class="dropdown-item">
                        <i class='bx bx-user'></i>
                        <span>{{ Auth::user()->display_name ?? 'Admin' }}</span>
                    </div>
                    <div style="padding: 0 15px 8px; font-size:11px; color:#999;">
                        {{ Auth::user()->user_email ?? '' }}
                    </div>
                    <hr style="margin: 5px 0;">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class='bx bx-log-out'></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>

        </nav>

        {{-- Konten halaman --}}
        <div class="main-content">
            @yield('content')
        </div>

    </section>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        const btnMenu = document.getElementById('btn-menu');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (btnMenu) {
            btnMenu.addEventListener('click', function () {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        function toggleDropdown() {
            document.getElementById('profileDropdown').classList.toggle('show');
        }

        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('profileDropdown');
            const profile  = document.querySelector('.profile-details');
            if (dropdown && profile && !profile.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        document.getElementById('desktopSearch')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const keyword = this.value;
                const url     = new URL(window.location.href);
                if (keyword.trim() !== '') {
                    url.searchParams.set('search', keyword);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('after-script')
</body>
</html>