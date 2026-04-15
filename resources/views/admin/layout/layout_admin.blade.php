<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMI Dashboard</title>
    <link rel="shortcut icon" href="{{ asset('admin-assets/img/logo dmi.png') }}" type="image/x-icon">
     <link rel="stylesheet" href=" {{ asset('css/layout_admin.css') }}">
     @stack('after-style')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta name="referrer" content="no-referrer">
</head>
<body>
    <div class="brand-container">
        <img src="{{ asset('admin-assets/img/logo.png') }}" alt="Logo DMI" class="logo-img">
        <h1 class="brand-name">Dewan Masjid Indonesia</h1>
    </div>
    
    <aside class="sidebar">
        <ul class="nav-list">
            <li>
                <button class="btn-upload">
                    <i class='bx bx-cloud-upload'></i>
                    <span class="links_name">Upload Berita</span>
                </button>
            </li>
            <li class="{{ request()->routeIs('home.home') ? 'active' : '' }}">
                <a href="{{ route('home.home') }}" class="{{ request()->routeIs('home.home') ? 'active' : '' }}">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Home</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('posts.index') ? 'active' : '' }}">
                <a href="{{ route('posts.index') }}" class="{{ request()->routeIs('posts.index') ? 'active' : '' }}">
                    <i class='bx bx-news'></i>
                    <span class="links_name">All Post</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('events.event') ? 'active' : '' }}">
                <a href="{{ route('events.event') }}" class="{{ request()->routeIs('events.event') ? 'active' : '' }}">
                    <i class='bx bx-calendar-event'></i>
                    <span class="links_name">Event</span>
                </a>
            </li>
        </ul>
    </aside>

    <section class="home-section">
        <nav class="navbar">
            <div class="search-box">
                <i class='bx bx-search'></i>
                <input type="text" placeholder="Cari berita atau informasi...">
            </div>
            <div class="nav-icons">
                <div class="notification">
                    <i class='bx bx-bell'></i>
                    <span class="dot"></span>
                </div>
                <div class="profile-details">
                    <div class="profile-content">
                        <img src="https://ui-avatars.com/api/?name=Admin+DMI&background=2E7D32&color=fff" alt="profileImg">
                    </div>
                    <div class="name-job">
                        <div class="profile_name">Admin</div>
                    </div>
                    <i class='bx bx-chevron-down'></i>
                </div>
            </div>
        </nav>
        <div class="main-content">
        @yield('content') 
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>