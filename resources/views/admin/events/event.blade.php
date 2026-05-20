@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/event.css') }}">
@endpush

@section('content')
<div class="all-post-container p-4">

    <div class="mobile-search">
        <i class='bx bx-search'></i>
        <input type="text" id="eventMobileSearch" 
               placeholder="Cari event..."
               value="{{ request('search') }}">
    </div>  

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flash-success">
            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash-error">
            <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="header-wrapper">
        <h4 class="fw-bold m-0 text-dark">ALL EVENT</h4>
        <button type="button" class="btn btn-new-post" data-bs-toggle="modal" data-bs-target="#createEventModal">
            <i class="fas fa-plus me-2"></i> NEW EVENT
        </button>
    </div>

    {{-- Table --}}
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
                                @php $imgUrl = $event->getImageUrl(); @endphp
                                <img src="{{ $imgUrl ?? asset('admin-assets/img/logo dmi.png') }}"
                                     class="img-thumbnail-post"
                                     style="object-fit: {{ $imgUrl ? 'cover' : 'contain' }}; padding: {{ $imgUrl ? '0' : '5px' }};">
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark d-block">
                                {{ Str::limit($event->post->post_title ?? 'Untitled Event', 40) }}
                            </span>
                            <small class="text-muted">
                                {{ $event->post->getMeta('_event_organizer') ? 'Oleh: ' . $event->post->getMeta('_event_organizer') : '' }}
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ Str::limit(strip_tags($event->post->post_content ?? ''), 60) }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}
                            </div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}
                                s/d {{ \Carbon\Carbon::parse($event->end_date)->format('H:i') }}
                            </small>
                        </td>
                        <td>
                            @php
                                $now   = now();
                                $start = \Carbon\Carbon::parse($event->start_date);
                                $end   = \Carbon\Carbon::parse($event->end_date);
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
                                {{-- Tombol Edit --}}
                                <button type="button" class="btn-action edit" title="Edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editEventModal"
                                    data-id="{{ $event->event_id }}"
                                    data-title="{{ $event->post->post_title ?? '' }}"
                                    data-description="{{ $event->post->post_content ?? '' }}"
                                    data-date="{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}"
                                    data-start="{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}"
                                    data-end="{{ \Carbon\Carbon::parse($event->end_date)->format('H:i') }}"
                                    data-organizer="{{ $event->post->getMeta('_event_organizer') ?? '' }}"
                                    data-tempat="{{ $event->post->getMeta('_event_venue') ?? '' }}"
                                    data-image="{{ $event->getImageUrl() ?? '' }}">
                                    <i class="bx bx-edit"></i>
                                </button>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('events.destroy', $event->event_id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus event ini?')">
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

    {{-- Pagination --}}
    <div class="pagination-footer">
        <div class="pagination-info">
            Showing <b>{{ $events->firstItem() }}</b> to <b>{{ $events->lastItem() }}</b>
            of <b>{{ $events->total() }}</b> results
        </div>
        <nav class="custom-pagination">
            {{ $events->onEachSide(1)->links('pagination::bootstrap-4') }}
        </nav>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL CREATE EVENT                                           --}}
{{-- ============================================================ --}}
<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-event">

            <div class="modal-event-title">
                <span>Add New Event</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">

                    {{-- Error validasi --}}
                    @if($errors->any() && old('_form') === 'create')
                        <div class="modal-alert-error">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="hidden" name="_form" value="create">

                    {{-- Section: Event --}}
                    <div class="modal-section">
                        <div class="modal-section-header">Event</div>
                        <div class="modal-section-body">
                            <div class="modal-form-row-2">
                                <div class="modal-form-group">
                                    <label>Judul Event :</label>
                                    <input type="text" name="title" class="modal-form-control"
                                        value="{{ old('title') }}" required>
                                </div>
                                <div class="modal-form-group">
                                    <label>Pilih Foto dari File :</label>
                                    <label for="create_image" class="modal-file-label" id="create_file_label">
                                        <i class="bx bx-image-add"></i>
                                        <span id="create_file_name">Pilih Foto dari File</span>
                                    </label>
                                    <input type="file" id="create_image" name="event_image"
                                        accept="image/*" style="display:none"
                                        onchange="updateFileName(this, 'create_file_name'); previewImage(this,'create_preview')">
                                    <div id="create_preview" class="modal-image-preview" style="display:none;">
                                        <img id="create_preview_img" src="" alt="Preview Foto">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>Isi Event :</label>
                                <textarea name="description" class="modal-form-control" rows="4"
                                    placeholder="Jelaskan detail kegiatan...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Detail Event --}}
                    <div class="modal-section">
                        <div class="modal-section-header">Detail Event</div>
                        <div class="modal-section-body">
                            <div class="modal-form-row-2">

                                {{-- Kolom Kiri --}}
                                <div>
                                    <div class="modal-form-group">
                                        <label>Date :</label>
                                        <input type="date" name="event_date" class="modal-form-control"
                                            value="{{ old('event_date') }}" required>
                                    </div>
                                    <div class="modal-start-end">
                                        <div class="modal-form-group">
                                            <label>Start :</label>
                                            <input type="time" name="start_time" class="modal-form-control"
                                                value="{{ old('start_time') }}" required>
                                        </div>
                                        <span class="modal-dash">—</span>
                                        <div class="modal-form-group">
                                            <label>End :</label>
                                            <input type="time" name="end_time" class="modal-form-control"
                                                value="{{ old('end_time') }}" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kolom Kanan --}}
                                <div>
                                    <div class="modal-form-group">
                                        <label>Organizer :</label>
                                        <input type="text" name="organizer" class="modal-form-control"
                                            value="{{ old('organizer') }}">
                                    </div>
                                    <div class="modal-form-group">
                                        <label>Tempat :</label>
                                        <textarea name="tempat" class="modal-form-control"
                                            rows="3">{{ old('tempat') }}</textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="submit" class="modal-btn-upload">Unggah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL EDIT EVENT                                             --}}
{{-- ============================================================ --}}
<div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-event modal-event-edit">

            <div class="modal-event-title modal-event-title-edit">
                <span>Edit Event</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="editEventForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-0">

                    @if($errors->any() && old('_form') === 'edit')
                        <div class="modal-alert-error">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="hidden" name="_form" value="edit">

                    {{-- Section: Event --}}
                    <div class="modal-section">
                        <div class="modal-section-header">Event</div>
                        <div class="modal-section-body">
                            <div class="modal-form-row-2">
                                <div class="modal-form-group">
                                    <label>Judul Event :</label>
                                    <input type="text" name="title" id="edit_title" class="modal-form-control" required>
                                </div>
                                <div class="modal-form-group">
                                    <label>Pilih Foto dari File :</label>
                                    <div id="edit_image_preview" class="modal-image-preview" style="display:none;">
                                        <img id="edit_image_preview_img" src="" alt="Preview">
                                    </div>
                                    <label for="edit_image" class="modal-file-label">
                                        <i class="bx bx-image-add"></i>
                                        <span id="edit_file_name">Ganti Foto</span>
                                    </label>
                                    <input type="file" id="edit_image" name="event_image"
                                        accept="image/*" style="display:none"
                                        onchange="updateFileName(this, 'edit_file_name'); previewImage(this,'edit_image_preview')">
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>Isi Event :</label>
                                <textarea name="description" id="edit_description" class="modal-form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Detail Event --}}
                    <div class="modal-section">
                        <div class="modal-section-header">Detail Event</div>
                        <div class="modal-section-body">
                            <div class="modal-form-row-2">

                                {{-- Kolom Kiri --}}
                                <div>
                                    <div class="modal-form-group">
                                        <label>Date :</label>
                                        <input type="date" name="event_date" id="edit_date" class="modal-form-control" required>
                                    </div>
                                    <div class="modal-start-end">
                                        <div class="modal-form-group">
                                            <label>Start :</label>
                                            <input type="time" name="start_time" id="edit_start" class="modal-form-control" required>
                                        </div>
                                        <span class="modal-dash">—</span>
                                        <div class="modal-form-group">
                                            <label>End :</label>
                                            <input type="time" name="end_time" id="edit_end" class="modal-form-control" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kolom Kanan --}}
                                <div>
                                    <div class="modal-form-group">
                                        <label>Organizer :</label>
                                        <input type="text" name="organizer" id="edit_organizer" class="modal-form-control">
                                    </div>
                                    <div class="modal-form-group">
                                        <label>Tempat :</label>
                                        <textarea name="tempat" id="edit_tempat" class="modal-form-control" rows="3"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="submit" class="modal-btn-upload modal-btn-update">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('after-script')
<script>

    // Update nama file yang dipilih
    function updateFileName(input, spanId) {
        const span = document.getElementById(spanId);
        span.textContent = input.files && input.files[0]
            ? input.files[0].name
            : (spanId === 'edit_file_name' ? 'Ganti Foto' : 'Pilih Foto dari File');
    }

    function previewImage(input, previewId) {
        const preview    = document.getElementById(previewId);
        const previewImg = document.getElementById(previewId + '_img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src        = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    // Isi data ke modal Edit saat tombol diklik
    const editModal = document.getElementById('editEventModal');
    editModal.addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;

        const id          = btn.getAttribute('data-id');
        const title       = btn.getAttribute('data-title');
        const description = btn.getAttribute('data-description');
        const date        = btn.getAttribute('data-date');
        const start       = btn.getAttribute('data-start');
        const end         = btn.getAttribute('data-end');
        const organizer   = btn.getAttribute('data-organizer');
        const tempat      = btn.getAttribute('data-tempat');
        const image       = btn.getAttribute('data-image');

        // Set action form
        document.getElementById('editEventForm').action = `/admin/events/${id}`;

        // Isi semua field
        document.getElementById('edit_title').value       = title;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_date').value        = date;
        document.getElementById('edit_start').value       = start;
        document.getElementById('edit_end').value         = end;
        document.getElementById('edit_organizer').value   = organizer;
        document.getElementById('edit_tempat').value      = tempat;

        // Tampilkan preview foto lama jika ada
        const preview    = document.getElementById('edit_image_preview');
        const previewImg = document.getElementById('edit_image_preview_img');
        if (image) {
            previewImg.src    = image;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }

        // Reset nama file
        document.getElementById('edit_file_name').textContent = 'Ganti Foto';
    });

    

    // Auto buka modal jika ada error validasi
    @if($errors->any() && old('_form') === 'create')
        new bootstrap.Modal(document.getElementById('createEventModal')).show();
    @endif

    @if($errors->any() && old('_form') === 'edit')
        new bootstrap.Modal(document.getElementById('editEventModal')).show();
    @endif

    

    document.addEventListener('DOMContentLoaded', function () {

        const mobileEventSearch = document.getElementById('eventMobileSearch');
        let mobileTimer;

        if (mobileEventSearch) {
            mobileEventSearch.addEventListener('keyup', function() {
                clearTimeout(mobileTimer);
                const keyword = this.value;

                mobileTimer = setTimeout(() => {
                    const url = new URL(window.location.href);

                    if (keyword.trim() !== '') {
                        url.searchParams.set('search', keyword);
                    } else {
                        url.searchParams.delete('search');
                    }

                    window.location.href = url.toString();
                }, 800);
            });
        }

        let desktopTimer;
        const desktopSearch = document.getElementById('desktopSearch');

        if (desktopSearch) {
            desktopSearch.addEventListener('keyup', function() {
                clearTimeout(desktopTimer);
                const keyword = this.value;

                desktopTimer = setTimeout(() => {
                    const url = new URL(window.location.href);

                    if (keyword.trim() !== '') {
                        url.searchParams.set('search', keyword);
                    } else {
                        url.searchParams.delete('search');
                    }

                    window.location.href = url.toString();
                }, 800);
            });
        }

    });
    </script>

@endpush    