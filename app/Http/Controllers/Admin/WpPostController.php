<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WordpressPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class WpPostController extends Controller
{
    public function home()
    {
        return view('admin.home.home');
    }
    public function index()
    {
        $posts = WordpressPost::with('author')
                    ->where('post_type', 'post')
                    ->orderBy('post_date', 'desc')
                    ->paginate(8);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Menyimpan berita baru ke tabel wp_posts.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        WordpressPost::create([
            'post_title'   => $request->title,
            'post_content' => $request->content,
            'post_status'  => 'publish', // Default status
            'post_type'    => 'post',
            'post_author'  => Auth::id(), // Otomatis mengambil ID admin yang login
            'post_date'    => now(),
            'post_name'    => Str::slug($request->title),
        ]);

        return redirect()->route('posts.index')->with('success', 'Berita berhasil diterbitkan!');
    }
     
    public function edit($id)
    {
        $post = WordpressPost::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post = WordpressPost::findOrFail($id);

        $post->update([
            'post_title'   => $request->title,
            'post_content' => $request->content,
            'post_name'    => Str::slug($request->title),
            // Jika ingin mengubah status (Publish/Draft/Deleted) bisa ditambahkan di sini
        ]);

        return redirect()->route('posts.index')->with('success', 'Berita berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $post = WordpressPost::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Berita berhasil dihapus!');
    }

    public function eventIndex()
    {   
        $events = \App\Models\Event::with('post')
                ->orderBy('start_date', 'desc')
                ->paginate(8);
        
        return view('admin.events.event', compact('events'));
    }
}

