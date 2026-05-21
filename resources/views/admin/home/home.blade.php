@extends('admin.layout.layout_admin')

@push('after-style')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<div class="home-container">

    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow">Dashboard Overview</span>
            <h2>Monitoring Konten DMI</h2>
            <p>Pantau berita, event, jadwal publish, dan aktivitas konten dari satu layar kerja.</p>
        </div>
        <div class="dashboard-status">
            <span class="status-pulse"></span>
            Sistem Aktif
        </div>
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- SECTION STATISTIK                      --}}
    {{-- ═══════════════════════════════════════ --}}

    {{-- Kartu Ringkasan --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class='bx bx-news'></i></div>
            <p class="stat-label">Total Berita</p>
            <p class="stat-val">{{ number_format($totalBerita) }}</p>
            <p class="stat-sub">postingan aktif</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class='bx bx-show'></i></div>
            <p class="stat-label">Pengunjung</p>
            <p class="stat-val">{{ $totalViews >= 1000 ? number_format($totalViews / 1000, 1) . 'K' : number_format($totalViews) }}</p>
            <p class="stat-sub">semua postingan</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-amber"><i class='bx bx-time-five'></i></div>
            <p class="stat-label">Postingan Mendatang</p>
            <p class="stat-val">{{ number_format($upcomingPost) }}</p>
            <p class="stat-sub">terjadwal tayang</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class='bx bx-calendar-event'></i></div>
            <p class="stat-label">Total Event</p>
            <p class="stat-val">{{ number_format($totalEvent) }}</p>
            <p class="stat-sub">{{ $eventBerlangsung }} berlangsung</p>
        </div>
    </div>

    {{-- Berita Terpopuler & Status Postingan --}}
    <div class="dash-two-col">

        {{-- Berita Terpopuler --}}
        <div class="dash-card">
            <p class="dash-card-title">Berita terpopuler</p>
            @foreach($popularPosts as $i => $post)
            <div class="pop-row">
                <span class="pop-rank">{{ $i + 1 }}</span>
                <div class="pop-info">
                    <p class="pop-title">{{ Str::limit($post->post_title, 45) }}</p>
                    <p class="pop-meta">{{ \Carbon\Carbon::parse($post->post_date)->format('M d') }}</p>
                </div>
                <span class="pop-views">{{ number_format($post->view_count) }}</span>
            </div>
            @endforeach
        </div>

        {{-- Status Postingan --}}
        <div class="dash-card">
            <p class="dash-card-title">Status postingan</p>

            <div class="status-row">
                <div class="status-icon status-icon-green">
                    <i class='bx bx-check-circle'></i>
                </div>
                <div class="status-info">
                    <p class="status-name">Publish</p>
                    <p class="status-desc">Tayang di website</p>
                </div>
                <span class="status-count status-count-green">{{ number_format($postPublish) }}</span>
            </div>

            <div class="status-row">
                <div class="status-icon status-icon-blue">
                    <i class='bx bx-calendar-check'></i>
                </div>
                <div class="status-info">
                    <p class="status-name">Terjadwal</p>
                    <p class="status-desc">Akan tayang otomatis</p>
                </div>
                <span class="status-count status-count-blue">{{ number_format($postTerjadwal) }}</span>
            </div>

            {{-- Mini bar proporsi --}}
            <div class="status-bar">
                <div class="status-bar-fill status-bar-green" style="width: {{ $pctPublish }}%"></div>
                <div class="status-bar-fill status-bar-blue" style="width: {{ $pctTerjadwal }}%"></div>
            </div>
            <div class="status-legend">
                <div class="legend-item">
                    <div class="legend-dot legend-dot-green"></div>
                    <span>Publish ({{ $pctPublish }}%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot legend-dot-blue"></div>
                    <span>Terjadwal ({{ $pctTerjadwal }}%)</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Event Mendatang --}}
    <div class="dash-card dash-card-full">
        <p class="dash-card-title">Event mendatang</p>
        <div class="event-grid">
            @forelse($upcomingEvents as $event)
            @php
                $now   = now();
                $start = \Carbon\Carbon::parse($event->start_date);
                $end   = \Carbon\Carbon::parse($event->end_date);
                $isNow = $now->between($start, $end);
            @endphp
            <div class="ev-row">
                <div class="ev-dot {{ $isNow ? 'ev-dot-amber' : 'ev-dot-green' }}"></div>
                <div class="ev-info">
                    <p class="ev-name">{{ Str::limit($event->post->post_title ?? 'Untitled', 35) }}</p>
                    <p class="ev-date">{{ $start->format('d M Y') }}</p>
                </div>
                @if($isNow)
                    <span class="ev-badge ev-badge-amber">Berlangsung</span>
                @else
                    <span class="ev-badge ev-badge-green">Upcoming</span>
                @endif
            </div>
            @empty
            <p style="font-size:13px;color:#999;">Tidak ada event mendatang.</p>
            @endforelse
        </div>
    </div>

    <hr class="home-divider" style="margin-top: 28px;">

    {{-- ═══════════════════════════════ --}}
    {{-- SECTION BERITA                 --}}
    {{-- ═══════════════════════════════ --}}
    <section class="home-section-block">
        <h2 class="home-section-title">Berita</h2>
        <div class="berita-grid">
            <div class="berita-featured">
                <div class="berita-featured-img">
                    <img src="{{ $featuredPost->getImageUrl() ?? asset('admin-assets/img/placeholder.png') }}"
                         alt="{{ $featuredPost->post_title ?? '' }}">
                </div>
                <div class="berita-featured-body">
                    <h3 class="berita-featured-title">{{ Str::limit($featuredPost->post_title ?? 'Belum ada berita', 80) }}</h3>
                    @if($featuredPost)
                        <span class="berita-featured-date">{{ \Carbon\Carbon::parse($featuredPost->post_date)->format('d M Y, H:i') }}</span>
                    @endif
                    <p class="berita-featured-desc">
                        {{ Str::limit(strip_tags($featuredPost->post_content ?? ''), 120) }}
                    </p>
                </div>
            </div>
            <div class="berita-latest">
                <h3 class="berita-latest-title">Latest Post</h3>
                @foreach($latestPosts as $post)
                <div class="berita-latest-item">
                    <div class="berita-latest-img">
                        <img src="{{ $post->getImageUrl() ?? asset('admin-assets/img/placeholder.png') }}"
                             alt="{{ $post->post_title }}">
                    </div>
                    <div class="berita-latest-info">
                        <p class="berita-latest-name">{{ Str::limit($post->post_title, 60) }}</p>
                        <span class="berita-latest-meta">
                            {{ \Carbon\Carbon::parse($post->post_date)->format('M d') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="home-see-more">
            <a href="{{ route('posts.index') }}">Selengkapnya</a>
        </div>
    </section>

    <hr class="home-divider">

    {{-- ═══════════════════════════════ --}}
    {{-- SECTION KEGIATAN               --}}
    {{-- ═══════════════════════════════ --}}
    <section class="home-section-block">
        <h2 class="home-section-title">Kegiatan</h2>
        <div class="kegiatan-grid">
            @foreach($events as $event)
            <div class="kegiatan-card">
                <div class="kegiatan-card-img">
                    <img src="{{ $event->getImageUrl() ?? asset('admin-assets/img/placeholder.png') }}"
                         alt="{{ $event->post->post_title ?? '' }}">
                </div>
                <div class="kegiatan-card-body">
                    <h4 class="kegiatan-card-title">
                        {{ Str::limit($event->post->post_title ?? 'Untitled', 40) }}
                    </h4>
                    <p class="kegiatan-card-desc">
                        {{ Str::limit(strip_tags($event->post->post_content ?? ''), 100) }}
                    </p>
                    <div class="kegiatan-card-date">
                        <i class="bx bx-time-five"></i>
                        <span>
                            {{ \Carbon\Carbon::parse($event->start_date)->format('l, j F') }},
                            {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="home-see-more">
            <a href="{{ route('events.index') }}">Selengkapnya</a>
        </div>
    </section>

</div>
@endsection
