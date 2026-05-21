@extends('admin.layout.layout_admin')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('css/berita.css') }}">
    <style>
        .form-page { max-width: 900px; margin: 0 auto; }
        .form-section { background: white; border-radius: 20px; border: 1px solid #e6ece8; margin-bottom: 20px; }
        .form-section-header { background: #f7faf8; padding: 18px 24px; border-bottom: 1px solid #e6ece8; border-radius: 20px 20px 0 0; }
        .form-section-header h3 { font-size: 16px; font-weight: 700; color: #17231b; margin: 0; }
        .form-section-body { padding: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group:last-child { margin-bottom: 0; }
        .form-group label { display: block; font-size: 14px; font-weight: 700; color: #374151; margin-bottom: 8px; }
        .form-group label .required { color: #e53935; }
        .form-control { width: 100%; padding: 12px 16px; border: 1.5px solid #e6ece8; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-full { grid-column: 1 / -1; }
        
        .trix-wrapper { border: 1.5px solid #e6ece8; border-radius: 12px; overflow: hidden; }
        trix-editor { min-height: 250px; }
        trix-toolbar { background: #f7faf8; border-bottom: 1px solid #e6ece8; padding: 8px 0; }
        
        .tags-container { display: flex; flex-direction: column; gap: 10px; }
        .tag-input { display: flex; gap: 8px; align-items: stretch; }
        .tag-input input { flex: 1; }
        .tag-input button { min-width: 44px; padding: 0; background: #2E7D32; color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; }
        .tag-input button:hover { background: #1b5e20; }
        .tag-input .btn-delete { background: #d32f2f; }
        .tag-input .btn-delete:hover { background: #b71c1c; }
        
        .upload-box { border: 2px dashed #cbd8cf; border-radius: 16px; padding: 32px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fafbfa; }
        .upload-box:hover { border-color: #2E7D32; background: #f0f7f1; }
        .upload-box.drag-over { border-color: #2E7D32; background: #f0f7f1; }
        .upload-icon { font-size: 48px; margin-bottom: 12px; }
        .upload-text { font-size: 14px; color: #666; margin: 0; }
        .upload-browse { color: #2E7D32; font-weight: 600; cursor: pointer; }
        
        .preview-box { position: relative; }
        .preview-img { width: 100%; max-width: 300px; height: 200px; object-fit: cover; border-radius: 12px; border: 1px solid #e6ece8; }
        .preview-actions { display: flex; gap: 10px; margin-top: 12px; }
        .preview-actions button { padding: 8px 16px; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-change { background: #e3f2fd; color: #1565c0; }
        .btn-change:hover { background: #bbdefb; }
        
        .form-actions { display: flex; gap: 12px; justify-content: center; padding: 24px; }
        .btn { padding: 12px 32px; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #f7b84b 0%, #efaa31 100%); color: white; box-shadow: 0 4px 12px rgba(247, 184, 75, 0.2); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(247, 184, 75, 0.3); }
        .btn-secondary { background: #e6ece8; color: #374151; }
        .btn-secondary:hover { background: #d8e5dc; }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .form-section-body { padding: 16px; }
            .upload-box { padding: 20px; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
@endpush

@section('content')
<div class="form-page">
    <h2 class="judul-halaman">Edit Berita</h2>

    <form id="formBerita" action="{{ route('posts.update', $post->ID) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- BASIC INFO -->
        <div class="form-section">
            <div class="form-section-header">
                <h3>📝 Informasi Dasar</h3>
            </div>
            <div class="form-section-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Judul <span class="required">*</span></label>
                        <input type="text" name="judul" class="form-control" required autocomplete="off" placeholder="Masukkan judul berita..." value="{{ old('judul', $post->post_title) }}">
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="required">*</span></label>
                        <input type="text" name="tagline" class="form-control" autocomplete="off" placeholder="Contoh: Teknologi, Bisnis..." value="{{ old('tagline', $tagline ?? '') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Penulis <span class="required">*</span></label>
                    <input type="text" name="penulis" class="form-control" required autocomplete="off" placeholder="Nama penulis..." value="{{ old('penulis', $namaPenulis ?? '') }}">
                </div>
            </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="form-section">
            <div class="form-section-header">
                <h3>📄 Deskripsi Lengkap</h3>
            </div>
            <div class="form-section-body">
                <div class="form-group form-full">
                    <label>Konten Berita <span class="required">*</span></label>
                    <div class="trix-wrapper">
                        <input type="hidden" name="isi_berita" id="isi_berita" value="{{ old('isi_berita', $post->post_content) }}">
                        <trix-editor input="isi_berita" class="custom-trix"></trix-editor>
                    </div>
                </div>
            </div>
        </div>

        <!-- KEYWORDS -->
        <div class="form-section">
            <div class="form-section-header">
                <h3>🏷️ Kata Kunci</h3>
            </div>
            <div class="form-section-body">
                <div class="tags-container" id="tagsContainer">
                    @if($tags->count() > 0)
                        @foreach($tags as $tag)
                            <div class="tag-input">
                                <input type="text" name="tags[]" class="form-control" autocomplete="off" value="{{ $tag->tag_name }}">
                                <button type="button" class="btn-delete">−</button>
                            </div>
                        @endforeach
                    @else
                        <div class="tag-input">
                            <input type="text" name="tags[]" class="form-control" autocomplete="off" placeholder="Masukkan kata kunci...">
                            <button type="button" class="btn-add-tag">+</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- IMAGE & CAPTION -->
        <div class="form-section">
            <div class="form-section-header">
                <h3>🖼️ Foto & Caption</h3>
            </div>
            <div class="form-section-body">
                <div class="form-group form-full">
                    <label>Gambar Utama <span class="required">*</span></label>
                    <input type="file" name="gambar" id="inputGambar" accept="image/*" style="display:none;">
                    
                    @if($post->getImageUrl())
                        <div class="upload-box" id="dropZone" style="position:relative; padding:20px;">
                            <div id="previewBox" style="text-align:center;">
                                <img id="previewImg" class="preview-img" src="{{ $post->getImageUrl() }}" alt="Current">
                                <div class="preview-actions" style="justify-content:center; margin-top:12px;">
                                    <button type="button" class="btn-change" onclick="document.getElementById('inputGambar').click()">📁 Pilih Gambar</button>
                                </div>
                                <p style="font-size:12px; color:#999; margin-top:8px;">Atau drag & drop gambar baru ke area ini</p>
                            </div>
                        </div>
                        <div id="progressBox" style="display:none; margin-top:12px;">
                            <div style="background:#e6ece8; border-radius:8px; height:8px; overflow:hidden;">
                                <div id="progressBar" style="height:100%; width:0%; background:linear-gradient(90deg,#2E7D32,#4CAF50); border-radius:8px; transition:width 0.3s;"></div>
                            </div>
                            <p id="progressText" style="font-size:12px; color:#666; margin:6px 0 0;">Mengupload...</p>
                        </div>
                    @else
                        <div class="upload-box" id="dropZone">
                            <div class="upload-icon">📤</div>
                            <p class="upload-text">Drag & drop atau <span class="upload-browse">pilih gambar</span></p>
                        </div>
                        <div id="progressBox" style="display:none; margin-top:12px;">
                            <div style="background:#e6ece8; border-radius:8px; height:8px; overflow:hidden;">
                                <div id="progressBar" style="height:100%; width:0%; background:linear-gradient(90deg,#2E7D32,#4CAF50); border-radius:8px; transition:width 0.3s;"></div>
                            </div>
                            <p id="progressText" style="font-size:12px; color:#666; margin:6px 0 0;">Mengupload...</p>
                        </div>
                        <div id="previewBox" style="display:none; text-align:center;">
                            <img id="previewImg" class="preview-img" alt="Preview">
                            <div class="preview-actions" style="justify-content:center; margin-top:12px;">
                                <button type="button" class="btn-change" onclick="document.getElementById('inputGambar').click()">📁 Pilih Gambar</button>
                            </div>
                            <p style="font-size:12px; color:#999; margin-top:8px;">Atau drag & drop gambar baru ke area ini</p>
                        </div>
                    @endif
                </div>

                <div class="form-group form-full">
                    <label>Caption Foto <span class="required">*</span></label>
                    <input type="text" name="caption" class="form-control" autocomplete="off" placeholder="Deskripsi singkat gambar..." value="{{ old('caption', $post->post_excerpt) }}">
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="form-actions">
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">← Batal</a>
            <button type="submit" class="btn btn-primary">✓ Simpan Perubahan</button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
@if ($errors->any())
<script>
    const errorMessages = @json($errors->all());
    const errorModal = document.createElement('div');
    errorModal.innerHTML = `
        <div id="errorOverlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9998;"></div>
        <div id="errorModal" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; border-radius:20px; padding:30px; max-width:400px; width:90%; text-align:center; z-index:9999; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <h5 style="font-weight:700; font-size:18px; margin-bottom:10px; color:#e53935;">⚠️ Oops!</h5>
            <ul style="text-align:left; color:#666; font-size:14px; margin-bottom:20px; padding-left:20px;">
                ${errorMessages.map(msg => `<li>${msg}</li>`).join('')}
            </ul>
            <button onclick="document.getElementById('errorOverlay').remove(); document.getElementById('errorModal').remove();" style="background:#f7b84b; color:white; border:none; padding:10px 40px; border-radius:12px; font-weight:600; cursor:pointer; font-size:14px;">OK</button>
        </div>
    `;
    document.body.appendChild(errorModal);
</script>
@endif

<script>
    document.addEventListener('trix-file-accept', e => e.preventDefault());

document.addEventListener('DOMContentLoaded', function() {
    const trixEditor = document.querySelector('trix-editor');
    if (!trixEditor) return;

    trixEditor.scrollIntoView = function() {};

    let lastFocusedElement = null;

    document.addEventListener('focusin', function(e) {
        if (e.target !== trixEditor) {
            lastFocusedElement = e.target;
        }
    });

    trixEditor.addEventListener('focus', function() {
        if (lastFocusedElement && lastFocusedElement !== trixEditor) {
            const restore = lastFocusedElement;
            requestAnimationFrame(function() {
                trixEditor.blur();
                restore.focus();
            });
        }
    });

    trixEditor.addEventListener('mousedown', function() {
        lastFocusedElement = null;
    });
});

    // Tags management
    const tagsContainer = document.getElementById('tagsContainer');
    const dropZone = document.getElementById('dropZone');
    const inputGambar = document.getElementById('inputGambar');
    const previewBox = document.getElementById('previewBox');
    const previewImg = document.getElementById('previewImg');

    // Add/remove tags
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-add-tag')) {
            const newTag = document.createElement('div');
            newTag.className = 'tag-input';
            newTag.innerHTML = `
                <input type="text" name="tags[]" class="form-control" autocomplete="off" placeholder="Masukkan kata kunci...">
                <button type="button" class="btn-delete">−</button>
            `;
            tagsContainer.appendChild(newTag);
        }

        if (e.target.classList.contains('btn-delete')) {
            e.target.closest('.tag-input').remove();
        }
    });

    // Drag & drop on dropZone card
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file?.type.startsWith('image/')) {
            inputGambar.files = e.dataTransfer.files;
            showPreview(file);
        }
    });

    inputGambar.addEventListener('change', e => {
        const file = e.target.files[0];
        if (file) showPreview(file);
    });

    function showPreview(file) {
        const progressBox = document.getElementById('progressBox');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        dropZone.style.display = 'none';
        previewBox.style.display = 'none';
        progressBox.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = 'Mengupload... 0%';

        const reader = new FileReader();
        reader.onprogress = e => {
            if (e.lengthComputable) {
                const pct = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = pct + '%';
                progressText.textContent = 'Mengupload... ' + pct + '%';
            }
        };
        reader.onload = e => {
            progressBar.style.width = '100%';
            progressText.textContent = 'Selesai!';
            setTimeout(() => {
                progressBox.style.display = 'none';
                previewImg.src = e.target.result;
                dropZone.style.display = 'block';
                previewBox.style.display = 'block';
            }, 400);
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('formBerita').addEventListener('submit', function() {
        document.querySelectorAll('button[type="submit"]').forEach(btn => btn.disabled = true);
    });
</script>
@endpush
