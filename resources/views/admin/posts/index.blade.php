@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/all_post.css') }}">
@endpush

@section('content')
<div class="all-post-container">
    {{-- Header Area --}}
    <div class="header-wrapper">
        <div>
            <h4 class="fw-bold m-0 text-dark">ALL POST</h4>
        </div>
        <a href="{{ route('posts.create') }}" class="btn btn-new-post">
            <i class="fas fa-plus me-2"></i> NEW POST
        </a>
    </div>


    <!-- <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">ALL POST</h4>
        </div>
        <a href="{{ route('posts.create') }}" class="btn btn-new-post">
            <i class="fas fa-plus"></i> NEW POST
        </a>
    </div> -->

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">IMAGE</th>
                        <th>TITLE</th>
                        <th>PREVIEW</th>
                        <th>AUTHOR</th>
                        <th>TIME</th>
                        <th>STATUS</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                    <tr>
                        <td class="ps-4">
                            <div class="img-wrapper">
                                @php
                                    $actualImage = $post->getImageUrl();
                                @endphp

                                @if($actualImage)
                                    <img src="{{ $actualImage }}" class="img-thumbnail-post" alt="News Image">
                                @else
                                    <img src="{{ asset('admin-assets/img/logo dmi.png') }}" class="img-thumbnail-post" style="object-fit: contain; padding: 5px;">
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark d-block">{{ Str::limit($post->post_title, 40) }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit(strip_tags($post->post_content), 60) }}</small>
                        </td>
                        <td>
                            <span class="badge badge-author">{{ $post->author->user_nicename ?? 'Buchori' }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ date('d M Y, H:i', strtotime($post->post_date)) }}</small>
                        </td>
                        <td>
                            {{-- Badge Status Dinamis --}}
                            <span class="status-badge status-{{ strtolower($post->post_status) }}">
                                {{ ucfirst($post->post_status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="{{ route('posts.edit', $post->ID) }}" class="btn-action edit" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form action="{{ route('posts.delete', $post->ID) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus postingan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-footer">
    {{-- Teks Info Kiri --}}
    <div class="pagination-info">
        Showing <b>{{ $posts->firstItem() }}</b> to <b>{{ $posts->lastItem() }}</b> of <b>{{ $posts->total() }}</b> results
    </div>

    {{-- Navigasi Kanan --}}
    <nav class="custom-pagination">
        {{-- Gunakan simpleLinks atau links dengan onEachSide agar ringkas --}}
        {{ $posts->onEachSide(1)->links('pagination::bootstrap-4') }}
    </nav>
</div>
@endsection