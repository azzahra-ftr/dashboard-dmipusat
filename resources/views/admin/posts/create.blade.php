<h2>Tambah Post</h2>

<form action="{{ route('posts.store') }}" method="POST" onsubmit="return confirm ('Yakin ingin menambahkan data ini?')">
    @csrf

    <input type="text" name="title" placeholder="Judul"><br><br>

    <textarea name="content" placeholder="Isi Konten"></textarea><br><br>

    <button type="submit">Simpan</button>
</form>