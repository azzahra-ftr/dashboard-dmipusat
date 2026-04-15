@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/event.css') }}">
@endpush

@section('content')
<div class="all-post-container p-4"> 
    {{-- Header Area --}}
    <div class="header-wrapper">
        <div>
            <h4 class="fw-bold m-0 text-dark">ALL EVENT</h4>
        </div>
        <a href="#" class="btn btn-new-post">
            <i class="fas fa-plus me-2"></i> NEW EVENT
        </a>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">IMAGE</th>
                        <th>EVENT TITLE</th>
                        <th>DESCRIPTION</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td class="ps-4">
                            <div class="img-wrapper">
                                {{-- Placeholder logo DMI karena di tabel event belum ada kolom image spesifik --}}
                                <img src="{{ asset('admin-assets/img/logo dmi.png') }}" class="img-thumbnail-post" style="object-fit: contain; padding: 5px;">
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark d-block">{{ Str::limit($event->post->post_title ?? 'Untitled Event', 40) }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit(strip_tags($event->post->post_content ?? ''), 60) }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 13px;">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                            </div>
                            <small class="text-muted">s/d {{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}</small>
                        </td>
                        <td>
                            @php
                                $now = now();
                                $start = \Carbon\Carbon::parse($event->start_date);
                                $end = \Carbon\Carbon::parse($event->end_date);
                            @endphp

                            @if($now->between($start, $end))
                                <span class="status-badge status-publish">Berlangsung</span>
                            @elseif($now->lt($start))
                                <span class="status-badge status-draft">Upcoming</span>
                            @else
                                <span class="status-badge status-trash">Selesai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="#" class="btn-action edit" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form action="#" method="POST" class="d-inline">
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

    {{-- Pagination Footer --}}
    <div class="pagination-footer">
        <div class="pagination-info">
            Showing <b>{{ $events->firstItem() }}</b> to <b>{{ $events->lastItem() }}</b> of <b>{{ $events->total() }}</b> results
        </div>
        <nav class="custom-pagination">
            {{ $events->onEachSide(1)->links('pagination::bootstrap-4') }}
        </nav>
    </div>
</div>
@endsection