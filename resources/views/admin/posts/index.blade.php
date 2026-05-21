@php
    use Illuminate\Support\Facades\DB;
@endphp

@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/all_post.css') }}">
@endpush

@section('content')
<div class="mobile-search">
    <i class='bx bx-search'></i>
    <input type="text" name="search" id="mobileSearch"
           value="{{ request('search') }}"
           placeholder="Cari berita atau informasi..."
           autocomplete="off">
</div>

    {{-- Header Area --}}
    <div class="header-wrapper">
        <div class="page-heading">
            <span class="eyebrow">Manajemen Konten</span>
            <h4 class="m-0 text-dark">Semua Berita</h4>
        </div>
<!-- Button Hapus Terpilih -->
        <div class="btn-two">
            <div class="bulk-action-wrapper" id="bulkActionWrapper" style="display: none;">
                <span class="selected-count" id="selectedCount">0 berita dipilih</span>
                <button type="button" id="btnBulkDelete" 
                        onclick="confirmBulkDelete()"
                        class="btn btn-hapus-terpilih">
                    <i class='bx bx-trash'></i> Hapus Terpilih
                </button>
            </div>
            <a href="{{ route('posts.create') }}" class="btn btn-new-post">
                <i class="fas fa-plus me-2"></i> NEW POST
            </a>
        </div>
    </div>


    <!-- <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">ALL POST</h4>
        </div>
        <a href="{{ route('posts.create') }}" class="btn btn-new-post">
            <i class="fas fa-plus"></i> NEW POST
        </a>
    </div> -->

    <!-- Form wrapper delete di notification pop up -->
    <form id="bulkForm" action="{{ route('posts.bulkDelete') }}" method="POST">
    @csrf
    @method('DELETE')

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table">
                <thead>
                    <tr>
                        <th class="ps-4"><input type="checkbox" id="checkAll"></th><!--Baris Checkbox Hapus Terpilih ini-->
                        <th class="ps-4">IMAGE</th>
                        <th>TITLE</th>
                        <th>CATEGORIES</th>
                        <th>PREVIEW</th>
                        <th>AUTHOR</th>
                        <th>TIME</th>
                        <th>STATUS</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
               <tbody>
                    @foreach($posts as $post)
                    @if($post->post_status === 'future')
                        <tr data-scheduled="{{ \Carbon\Carbon::parse($post->post_date)->toIso8601String() }}">
                    @else
                        <tr>
                    @endif
                        <!-- Checkbox -->
                        <td class="ps-4">
                            <input type="checkbox" name="ids[]" value="{{ $post->ID }}" class="check-item">
                        </td>

                        <!-- Image -->
                        <td class="ps-4">
                            <div class="img-wrapper">
                                @php $actualImage = $post->getImageUrl(); @endphp
                                @if($actualImage)
                                    <img src="{{ $actualImage }}" class="img-thumbnail-post" alt="News Image">
                                @else
                                    <img src="{{ asset('admin-assets/img/logo dmi.png') }}" class="img-thumbnail-post" style="object-fit: contain; padding: 5px;">
                                @endif
                            </div>
                        </td>

                        <!-- Title -->
                        <td>
                            <span class="fw-bold text-dark d-block">{{ Str::limit($post->post_title, 40) }}</span>
                        </td>

                        <!-- Categories -->
                        <td>
                            @php
                                $categoryMeta = DB::connection('wordpress')
                                    ->table('ism13qf_postmeta')
                                    ->where('post_id', $post->ID)
                                    ->where('meta_key', 'tagline_berita')
                                    ->value('meta_value');
                                $postTags = \App\Models\PostTag::where('post_id', $post->ID)->pluck('tag_name');
                            @endphp
                            <small class="text-muted">{{ $categoryMeta ? Str::limit($categoryMeta, 30) : ($postTags->isNotEmpty() ? Str::limit($postTags->implode(', '), 30) : '-') }}</small>
                        </td>

                        <!-- Preview -->
                        <td>
                            <small class="text-muted">{{ Str::limit(strip_tags($post->post_content), 60) }}</small>
                        </td>

                        <!-- Author -->
                        <td>
                            <span class="badge badge-author">
                                @php
                                    $namaPenulis = DB::connection('wordpress')
                                        ->table('ism13qf_postmeta')
                                        ->where('post_id', $post->ID)
                                        ->where('meta_key', 'nama_penulis')
                                        ->value('meta_value');
                                @endphp
                                {{ $namaPenulis ?? $post->author->user_nicename ?? 'Admin' }}
                            </span>
                        </td>

                        <!-- Time -->
                        <td>
                            <small class="text-muted">{{ date('d M Y, H:i', strtotime($post->post_date)) }}</small>
                        </td>

                        <!-- Status -->
                        <td>
                            @if($post->post_status === 'future')
                                <span style="background:#ffc107; color:#333; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">Terjadwal</span>
                            @elseif($post->post_status === 'publish')
                                <span style="background:#198754; color:white; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">Published</span>
                            @else
                                <span style="background:#6c757d; color:white; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">{{ ucfirst($post->post_status) }}</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="{{ route('posts.edit', $post->ID) }}" class="btn-action edit" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" 
                                    class="btn-action delete btn-delete-single" 
                                    title="Hapus"
                                    data-action="{{ route('posts.delete', $post->ID) }}"
                                    data-title="{{ $post->post_title }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
<!--Alert Notification Pop Up -->
    <!-- Modal Konfirmasi Hapus --> <!-- ini -->
        <div id="deleteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:white; border-radius:20px; padding:40px 30px; max-width:400px; width:90%; text-align:center; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
                <!-- Icon -->
                <div style="width:60px; height:60px; background:#ffe5e5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                    <i class='bx bx-error' style="font-size:30px; color:#e53935;"></i>
                </div>
                <!-- Title -->
                <h5 style="font-weight:700; font-size:20px; margin-bottom:10px;" id="deleteModalTitle">Hapus Berita</h5>
                <!-- Message -->
                <p style="color:#666; font-size:14px; margin-bottom:30px;" id="deleteModalMessage">Apakah Anda yakin ingin menghapus berita ini?</p>
                <!-- Buttons -->
                <div style="display:flex; gap:15px; justify-content:center;">
                    <button type="button" onclick="closeDeleteModal()" 
                            style="flex:1; padding:12px; border-radius:10px; border:none; background:#f0f0f0; color:#333; font-weight:600; cursor:pointer; font-size:14px;">
                        Batal
                    </button>
                    <button id="confirmDeleteBtn"
                            style="flex:1; padding:12px; border-radius:10px; border:none; background:#e53935; color:white; font-weight:600; cursor:pointer; font-size:14px;">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

    {{-- Navigasi Kanan --}}
    <nav class="custom-pagination">
        {{-- Gunakan simpleLinks atau links dengan onEachSide agar ringkas --}}
        {{ $posts->onEachSide(0)->links('pagination::bootstrap-4') }}
    </nav>

    <div class="pagination-footer">
    {{-- Teks Info Kiri --}}
    <div class="pagination-info">
        Showing <b>{{ $posts->firstItem() }}</b> to <b>{{ $posts->lastItem() }}</b> of <b>{{ $posts->total() }}</b> results
    </div>
</div>
@endsection


@push('scripts')
<script>
  // Mobile search - real time
    var mobileTimer;
    var mobileSearchInput = document.getElementById('mobileSearch');
    if (mobileSearchInput) {
        ['keyup', 'input'].forEach(function(event) {
            mobileSearchInput.addEventListener(event, function() {
                clearTimeout(mobileTimer);
                var keyword = this.value;
                mobileTimer = setTimeout(() => {
                    window.location.href = '{{ route('posts.index') }}?search=' + encodeURIComponent(keyword);
                }, 500);
            });
        });
    }

    //ini
//Hapus Terpilih
    // Check all
document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
    toggleBulkDelete();
});

// Toggle tombol hapus terpilih
document.querySelectorAll('.check-item').forEach(function(cb) {
    cb.addEventListener('change', toggleBulkDelete);
});

function toggleBulkDelete() {
    const checked = document.querySelectorAll('.check-item:checked').length;
    const bulkActionWrapper = document.getElementById('bulkActionWrapper');
    const selectedCount = document.getElementById('selectedCount');

    bulkActionWrapper.style.display = checked > 0 ? 'flex' : 'none';
    selectedCount.textContent = checked + ' berita dipilih';
}

function confirmBulkDelete() {
    const checked = document.querySelectorAll('.check-item:checked').length;
    if (confirm('Yakin ingin menghapus ' + checked + ' berita yang dipilih?')) {
        document.getElementById('bulkForm').submit();
    }
}
//sampai sini Hapus Terpilih

//Alert notification pop up
// Modal functions
function openDeleteModal(title, message, onConfirm) {
    document.getElementById('deleteModalTitle').textContent = title;
    document.getElementById('deleteModalMessage').textContent = message;
    document.getElementById('deleteModal').style.display = 'flex';
    document.getElementById('confirmDeleteBtn').onclick = function() {
        closeDeleteModal();
        onConfirm();
    };
}

function closeDeleteModal() {
    
    document.getElementById('deleteModal').style.display = 'none';
}

// Ganti confirmBulkDelete
function confirmBulkDelete() {
    const checked = document.querySelectorAll('.check-item:checked').length;
    openDeleteModal(
        'Hapus Berita',
        'Apakah Anda yakin ingin menghapus ' + checked + ' berita yang dipilih?',
        function() {
            document.getElementById('bulkForm').submit();
        }
    );
}

// Tutup modal jika klik di luar
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
//sampai sini alert notification pop up

// Hapus single
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-delete-single').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const title = this.getAttribute('data-title');
            openDeleteModal(
                'Hapus Berita',
                'Apakah Anda yakin ingin menghapus "' + title + '"?',
                function() {
                    fetch(action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: '_method=DELETE&_token={{ csrf_token() }}'
                    }).then(function() {
                        window.location.reload();
                    });
                }
            );
        });
    });
});

// Cek setiap 30 detik apakah ada berita scheduled yang sudah waktunya
setInterval(function() {
    const now = new Date();
    let adaYangWaktunya = false;

    document.querySelectorAll('[data-scheduled]').forEach(function(el) {
        const scheduledAt = new Date(el.getAttribute('data-scheduled'));
        if (scheduledAt <= now) {
            adaYangWaktunya = true;
        }
    });

    if (adaYangWaktunya) {
        fetch('{{ route('posts.publishScheduledDue') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(function() {
            window.location.reload();
        });
    }
}, 30000); // 30 detik
</script>
@endpush
