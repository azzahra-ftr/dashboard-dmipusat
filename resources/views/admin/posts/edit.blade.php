<h2>Edit Post</h2>

<form action="{{ route('posts.update', $post->ID) }}" method="POST" onsubmit="return confirm('Yakin ingin mengedit data ini?')">
    @csrf

    <input type="text" name="title" value="{{ $post->post_title }}"><br><br>

    <textarea name="content">{{ $post->post_content }}</textarea><br><br>

    <button type="submit">Update</button>
</form>