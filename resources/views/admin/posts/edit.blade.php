@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/berita.css') }}">
@endpush

@section('content')
    <div class="container">
        <h2 class="judul-halaman">Edit Berita</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('posts.update', $post->ID) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card card-details-berita">
                <div class="card-header custom-header">Details Berita</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label>Judul Berita :</label>
                            <input type="text" name="judul" class="form-control custom-input"
                                   value="{{ $post->post_title }}" required placeholder="Masukkan judul berita...">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Tagline Berita :</label>
                            <input type="text" name="tagline" class="form-control custom-input"
                                value="{{ $tagline ?? '' }}"
                                placeholder="Masukkan tagline berita...">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Nama Penulis :</label>
                            <input type="text" name="penulis" class="form-control custom-input"
                                   value="{{ $namaPenulis ?? '' }}" placeholder="Masukkan nama penulis...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Isi Berita :</label>
                        <textarea id="summernote" name="isi_berita" class="form-control">{{ $post->post_content }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label>Kata Kunci Berita :</label>
                        <div id="tags-container">
                            @forelse($tags as $tag)
                                <div class="input-group mb-2 tag-row">
                                    <input type="text" name="tags[]" class="form-control"
                                           value="{{ $tag->tag_name }}" placeholder="Masukkan kata kunci...">
                                    <button class="btn btn-danger btn-hapus-tag" type="button">−</button>
                                </div>
                            @empty
                                <div class="input-group mb-2 tag-row">
                                    <input type="text" name="tags[]" class="form-control" placeholder="Masukkan kata kunci...">
                                    <button class="btn btn-add btn-tambah-tag" type="button">+</button>
                                </div>
                            @endforelse
                            <button class="btn btn-add btn-tambah-tag mt-2" type="button">+ Tambah Kata Kunci</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-upload-gambar">
                <div class="card-header custom-header">Upload Gambar</div>
                <div class="card-body">
                    @if($post->getImageUrl())
                        <div class="mb-3">
                            <label>Gambar Saat Ini :</label><br>
                            <img src="{{ $post->getImageUrl() }}" alt="Gambar"
                                 style="max-width:300px; border-radius:8px; border:1px solid #ddd;">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label>Ganti Gambar (opsional) :</label>
                        <input type="file" name="gambar" class="form-control" id="inputGambar" accept="image/*">
                    </div>

                    <div class="mb-3" id="previewContainer" style="display:none;">
                        <label>Preview Gambar Baru :</label><br>
                        <img id="previewGambar" src="" alt="Preview"
                             style="max-width:300px; border-radius:8px; border:1px solid #ddd;">
                    </div>

                    <div class="mb-3">
                        <label>Caption Foto :</label>
                        <input type="text" name="caption" class="form-control custom-input"
                               value="{{ $post->post_excerpt }}">
                    </div>
                </div>
            </div>

            <div class="mt-3 mb-4" style="position: relative; text-align: center;">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary" 
                style="position: absolute; left: 0;">Batal</a>
                <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('inputGambar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewGambar').src = e.target.result;
                document.getElementById('previewContainer').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('previewContainer').style.display = 'none';
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-tambah-tag')) {
            const container = document.getElementById('tags-container');
            const btn = document.querySelector('.btn-tambah-tag:last-child');
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-2', 'tag-row');
            newRow.innerHTML = `
                <input type="text" name="tags[]" class="form-control" placeholder="Masukkan kata kunci...">
                <button class="btn btn-danger btn-hapus-tag" type="button">−</button>
            `;
            container.insertBefore(newRow, btn);
        }

        if (e.target.classList.contains('btn-hapus-tag')) {
            e.target.closest('.tag-row').remove();
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Tulis isi berita di sini...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['view', ['codeview', 'help']]
            ]
        });
    });
</script>
@endpush