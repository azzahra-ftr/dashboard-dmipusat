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
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js" defer></script>
</head>
<body>
    @php
        $unreadCount = class_exists(\App\Models\Notification::class)
            ? \App\Models\Notification::where('is_read', false)->count()
            : 0;
        $showSearch = request()->routeIs('posts.index') || request()->routeIs('events.index');
        $searchPlaceholder = request()->routeIs('events.*')
            ? 'Cari event atau kegiatan...'
            : 'Cari berita atau informasi...';
    @endphp

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

            @if($showSearch)
                {{-- Tengah: Search --}}
                <div class="navbar-search">
                    <i class='bx bx-search'></i>
                    <input type="text" id="desktopSearch" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}" autocomplete="off">
                    <button class="search-btn" type="button" id="desktopSearchBtn"><i class='bx bx-search'></i></button>
                </div>
            @else
                <div class="navbar-spacer"></div>
            @endif

            {{-- Kanan: Bell + Profil --}}
            <div class="nav-icons">
                <div class="notification" id="notifBell" onclick="toggleNotif()">
                    <i class='bx bx-bell'></i>
                    @if($unreadCount > 0)
                        <span class="notif-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
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

        {{-- Panel Notifikasi --}}
        <div class="notif-panel" id="notifPanel">
            <div class="notif-panel-header">
                <div>
                    <strong>Notifikasi</strong>
                    @if($unreadCount > 0)
                        <span class="notif-panel-count">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div class="notif-panel-actions">
                    <button type="button" onclick="markAllRead()">Tandai semua dibaca</button>
                    <button type="button" onclick="closeNotif()" aria-label="Tutup notifikasi">&times;</button>
                </div>
            </div>
            <div class="notif-panel-body">
                @php
                    $notifications = class_exists(\App\Models\Notification::class)
                        ? \App\Models\Notification::orderBy('created_at', 'desc')->take(30)->get()
                        : collect();
                @endphp

                @forelse($notifications->groupBy(fn ($n) => \Carbon\Carbon::parse($n->created_at)->format('d M Y')) as $date => $items)
                    <div class="notif-date-label">
                        {{ $date === now()->format('d M Y') ? 'Hari Ini' : ($date === now()->subDay()->format('d M Y') ? 'Kemarin' : $date) }}
                    </div>
                    @foreach($items as $notification)
                    @php
                        $notifClass = match ($notification->type) {
                            'post_published', 'post_auto_published', 'post_updated' => 'success',
                            'post_scheduled' => 'warning',
                            'post_deleted' => 'danger',
                            default => 'info',
                        };
                        $notifIcon = match ($notification->type) {
                            'post_published', 'post_auto_published', 'post_updated' => 'bx-check-circle',
                            'post_scheduled' => 'bx-time',
                            'post_deleted' => 'bx-trash',
                            default => 'bx-calendar-event',
                        };
                    @endphp
                    <div class="notif-item {{ $notification->is_read ? '' : 'unread' }} notif-{{ $notifClass }}">
                        <div class="notif-item-icon"><i class='bx {{ $notifIcon }}'></i></div>
                        <div>
                            <div class="notif-item-title">{{ $notification->title }}</div>
                            <div class="notif-item-message">{!! nl2br(e($notification->message)) !!}</div>
                            <div class="notif-item-time">{{ $notification->created_at?->diffForHumans() }}</div>
                        </div>
                        @if(!$notification->is_read)
                            <span class="notif-unread-dot"></span>
                        @endif
                    </div>
                    @endforeach
                @empty
                    <div class="notif-empty">Belum ada notifikasi</div>
                @endforelse
            </div>
        </div>

    </section>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        const btnMenu = document.getElementById('btn-menu');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (btnMenu) {
            btnMenu.addEventListener('click', function (event) {
                event.stopPropagation();
                if (window.innerWidth <= 767) {
                    const isOpen = sidebar.classList.toggle('active');
                    overlay.classList.toggle('active', isOpen);
                    document.body.classList.toggle('sidebar-open', isOpen);
                    return;
                }

                document.body.classList.toggle('sidebar-collapsed');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            });
        }

        document.addEventListener('click', function (event) {
            const isMobile = window.innerWidth <= 767;
            if (!isMobile || !sidebar.classList.contains('active')) return;
            if (!sidebar.contains(event.target) && !btnMenu.contains(event.target)) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
                closeNotif();
            }
        });

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

        function submitDesktopSearch() {
            const input = document.getElementById('desktopSearch');
            if (!input) return;
            const keyword = input.value;
                const url     = new URL(window.location.href);
                if (keyword.trim() !== '') {
                    url.searchParams.set('search', keyword);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
        }

        document.getElementById('desktopSearch')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                submitDesktopSearch();
            }
        });

        document.getElementById('desktopSearchBtn')?.addEventListener('click', submitDesktopSearch);

        function toggleNotif() {
            document.getElementById('notifPanel')?.classList.toggle('show');
        }

        function closeNotif() {
            document.getElementById('notifPanel')?.classList.remove('show');
        }

        function markAllRead() {
            fetch('{{ route('notifications.readAll') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(() => window.location.reload());
        }

        document.addEventListener('click', function (event) {
            const panel = document.getElementById('notifPanel');
            const bell = document.getElementById('notifBell');
            if (panel && bell && !panel.contains(event.target) && !bell.contains(event.target)) {
                closeNotif();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @stack('after-script')
</body>
</html>
