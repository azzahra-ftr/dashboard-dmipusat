@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<div class="all-post-container">
    {{-- HEADER DENGAN STATUS AKTIF --}}
    <div class="header-wrapper">
        <div>
            <h4 class="fw-bold m-0 text-dark">DASHBOARD OVERVIEW</h4>
            <small class="text-muted">Selamat datang, Admin! Berikut ringkasan performa Sistem DMI.</small>
        </div>
        
        <div class="status-indicator">
            <span class="dot-active"></span>
            <span>AKTIF</span>
        </div>
    </div>

    {{-- 4 KARTU STATISTIK DENGAN AKSEN --}}
    <div class="row g-4 mb-4">
        {{-- KARTU 1: Posts --}}
        <div class="col-md-3">
            <div class="stat-card card-posts">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="stat-label text-uppercase">Total Posts</span>
                        <h1 class="fw-bold my-1">417</h1>
                    </div>
                    <div class="stat-icon bg-posts">
                        <i class='bx bx-news'></i>
                    </div>
                </div>
                <div class="stat-desc mt-2">12 Berita Baru (Minggu ini)</div>
            </div>
        </div>

        {{-- KARTU 2: Draft --}}
        <div class="col-md-3">
            <div class="stat-card card-drafts">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="stat-label text-uppercase">Berita Draft</span>
                        <h1 class="fw-bold my-1">12</h1>
                    </div>
                    <div class="stat-icon bg-drafts">
                        <i class='bx bx-edit-alt'></i>
                    </div>
                </div>
                <div class="stat-desc mt-2">Perlu direview sebelum diterbitkan.</div>
            </div>
        </div>

        {{-- KARTU 3: Kategori --}}
        <div class="col-md-3">
            <div class="stat-card card-categories">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="stat-label text-uppercase">Kategori</span>
                        <h1 class="fw-bold my-1">5</h1>
                    </div>
                    <div class="stat-icon bg-categories">
                        <i class='bx bx-folder'></i>
                    </div>
                </div>
                <div class="stat-desc mt-2">Kajian, Rapat, Kegiatan Sosial, dll.</div>
            </div>
        </div>

        {{-- KARTU 4: Tags --}}
        <div class="col-md-3">
            <div class="stat-card card-tags">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="stat-label text-uppercase">Tags</span>
                        <h1 class="fw-bold my-1">23</h1>
                    </div>
                    <div class="stat-icon bg-tags">
                        <i class='bx bx-purchase-tag-alt'></i>
                    </div>
                </div>
                <div class="stat-desc mt-2">Kata kunci untuk pencarian berita.</div>
            </div>
        </div>
    </div>

    {{-- LOG AKTIVITAS TERBARU --}}
    <div class="activity-card shadow-sm">
        <h6 class="fw-bold mb-4 text-dark">Log Aktivitas Terbaru (Hari ini)</h6>
        <ul class="activity-list">
            <li class="activity-item">
                <i class='bx bx-megaphone fs-5 text-success'></i>
                <span>Berita <strong>"Tabligh Akbar Nasional"</strong> berhasil diterbitkan (Oleh: Buchori) - 14:02</span>
            </li>
            <li class="activity-item">
                <i class='bx bx-trash fs-5 text-danger'></i>
                <span>Draft <strong>"Rapat Rutin"</strong> dihapus - 11:30</span>
            </li>
            <li class="activity-item">
                <i class='bx bx-folder-plus fs-5 text-primary'></i>
                <span>Kategori <strong>"Sosial"</strong> ditambahkan - 09:15</span>
            </li>
        </ul>
    </div>
</div>

{{-- SECTION PREVIEW BERITA --}}
<div class="news-section-row">
    {{-- BERITA UTAMA (Kiri) --}}
    <div class="col-lg-8">
    <div class="section-title-wrapper">
        <h5 class="fw-bold text-dark">Berita Terbaru</h5>
        <hr class="title-line">
    </div>
    @if($headline)
    <div class="stat-card p-0 overflow-hidden mb-4">
        <div class="main-news-placeholder">
            <img src="{{ asset('storage/' . $headline->image) }}" class="w-100 h-100 object-fit-cover">
            <div class="main-news-overlay">
                <span class="badge bg-success mb-2">{{ $headline->category->name ?? 'Berita' }}</span>
                <h4 class="fw-bold text-white">{{ $headline->post_title }}</h4>
            </div>
        </div>
    </div>
    @else
    <p class="text-muted">Belum ada berita utama.</p>
    @endif
</div>

    {{-- SECTION LATEST POST (BERITA) --}}
    <!--  -->
    <div class="latest-post-list">
    @foreach($latestPosts as $post)
    <div class="stat-card p-2 mb-3 shadow-sm border-0">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $headline->getImageUrl() }}" class="news-thumb-small rounded object-fit-cover" class="w-100 h-100 object-fit-cover">
            <div class="news-info-small">
                <h6 class="fw-bold mb-1 small-title">{{ Str::limit($post->title, 40) }}</h6>
                <small class="text-muted" style="font-size: 10px;">
                </small>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- SECTION EVENT --}}
<div class="row mt-2">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark m-0">Event Terbaru</h5>
            <a href="{{ route('events.event') }}" class="text-success fw-bold small text-decoration-none">Selengkapnya</a>
        </div>
    </div>
    
    <div class="col-md-6"> 
            <div class="stat-card p-0 overflow-hidden border-0 shadow-sm event-card">
                <div class="p-3">
                    <h6 class="fw-bold mb-2">{{ $latestEvent->post_title ?? 'Kegiatan DMI' }}</h6>
                    <p class="text-muted mb-3" style="font-size: 12px;">
                        Silakan klik selengkapnya untuk melihat detail kegiatan masjid.
                    </p>
                    <a href="{{ route('events.event') }}" class="btn btn-sm btn-outline-success">Selengkapnya</a>
                </div>
            </div>
    </div>
</div>
@endsection