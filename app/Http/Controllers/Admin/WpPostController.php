<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WordpressPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WpPostController extends Controller
{
    public function home()
    {
        return view('admin.home.home');
    }
    public function index(Request $request)
{
    $query = WordpressPost::with('author')
                ->where('post_type', 'post');

    // Filter berdasarkan keyword search
    if ($request->has('search') && $request->search != '') {
        $query->where('post_title', 'like', '%' . $request->search . '%');
    }

    $posts = $query->orderBy('post_date', 'desc')->paginate(8);

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
        'judul'      => 'required|max:255',
        'penulis'    => 'required|string',
        'isi_berita' => 'required',
        'tags'       => 'nullable|array',
        'tags.*'     => 'nullable|string|max:100',
        'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'caption'    => 'nullable|string',
    ]);

    // Upload gambar jika ada
    $gambarPath = null;
    if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('berita', 'public');
    }

    // Simpan berita
$post = WordpressPost::create([
    'post_title'             => $request->judul,
    'post_content'           => $request->isi_berita,
    'post_excerpt'           => $request->caption ?? '',
    'post_status'            => 'publish',
    'post_type'              => 'post',
    'post_author'            => Auth::id() ?? 1,
    'post_date'              => now(),
    'post_date_gmt'          => now(),
    'post_modified'          => now(),
    'post_modified_gmt'      => now(),
    'post_name'              => Str::slug($request->judul),
    'guid'                   => url('/'),
    'to_ping'                => '',
    'pinged'                 => '',
    'post_content_filtered'  => '',
]);

// Simpan tagline
if ($request->tagline) {
    DB::table('ism13qf_postmeta')->insert([
        'post_id'    => $post->ID,
        'meta_key'   => 'tagline_berita',
        'meta_value' => $request->tagline,
    ]);
}

// Simpan nama penulis ke postmeta
DB::table('ism13qf_postmeta')->insert([
    'post_id'    => $post->ID,
    'meta_key'   => 'nama_penulis',
    'meta_value' => $request->penulis,
]);

// Simpan gambar ke postmeta
if ($request->hasFile('gambar')) {
    $gambarPath = $request->file('gambar')->store('berita', 'public');
    $gambarUrl  = asset('storage/' . $gambarPath);

    // Simpan attachment sebagai post baru
   $attachmentId = DB::table('ism13qf_posts')->insertGetId([
    'post_title'             => $request->file('gambar')->getClientOriginalName(),
    'post_content'           => '',
    'post_excerpt'           => '', // TAMBAHKAN INI
    'post_status'            => 'inherit',
    'post_type'              => 'attachment',
    'post_author'            => Auth::id() ?? 1,
    'post_date'              => now(),
    'post_date_gmt'          => now(),
    'post_modified'          => now(),
    'post_modified_gmt'      => now(),
    'post_name'              => Str::slug($request->file('gambar')->getClientOriginalName()),
    'post_parent'            => $post->ID,
    'guid'                   => $gambarUrl,
    'to_ping'                => '',
    'pinged'                 => '',
    'post_content_filtered'  => '',
    'post_mime_type'         => $request->file('gambar')->getMimeType(),
]);
    // Set sebagai thumbnail
    DB::table('ism13qf_postmeta')->insert([
        'post_id'    => $post->ID,
        'meta_key'   => '_thumbnail_id',
        'meta_value' => $attachmentId,
    ]);

    // Simpan URL gambar di postmeta attachment
    DB::table('ism13qf_postmeta')->insert([
        'post_id'    => $attachmentId,
        'meta_key'   => '_wp_attached_file',
        'meta_value' => $gambarPath,
    ]);
}
    // Simpan tags
    if ($request->has('tags')) {
        foreach ($request->tags as $tag) {
            if (!empty($tag)) {
                \App\Models\PostTag::create([
                    'post_id'  => $post->ID,
                    'tag_name' => $tag,
                ]);
            }
        }
    }

    return redirect()->route('posts.index')->with('success', 'Berita berhasil diterbitkan!');
}
     
    public function edit($id)
{
    $post = WordpressPost::findOrFail($id);
    
    $tags = \App\Models\PostTag::where('post_id', $id)->get();
    
    $namaPenulis = DB::table('ism13qf_postmeta')
        ->where('post_id', $id)
        ->where('meta_key', 'nama_penulis')
        ->value('meta_value');

    $tagline = DB::table('ism13qf_postmeta')
        ->where('post_id', $id)
        ->where('meta_key', 'tagline_berita')
        ->value('meta_value');

    return view('admin.posts.edit', compact('post', 'tags', 'namaPenulis', 'tagline'));
}

    public function update(Request $request, $id)
{
    $request->validate([
        'judul'      => 'required|max:255',
        'isi_berita' => 'required',
        'tags'       => 'nullable|array',
        'tags.*'     => 'nullable|string|max:100',
        'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'caption'    => 'nullable|string',
    ]);

    $post = WordpressPost::findOrFail($id);

    $post->update([
        'post_title'        => $request->judul,
        'post_content'      => $request->isi_berita,
        'post_excerpt'      => $request->caption ?? '',
        'post_name'         => Str::slug($request->judul),
        'post_modified'     => now(),
        'post_modified_gmt' => now(),
    ]);

    // Update nama penulis
    DB::table('ism13qf_postmeta')
        ->updateOrInsert(
            ['post_id' => $post->ID, 'meta_key' => 'nama_penulis'],
            ['meta_value' => $request->penulis]
        );

    // Update gambar jika ada
    if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('berita', 'public');
        $gambarUrl  = asset('storage/' . $gambarPath);

        $attachmentId = DB::table('ism13qf_posts')->insertGetId([
            'post_title'             => $request->file('gambar')->getClientOriginalName(),
            'post_content'           => '',
            'post_excerpt'           => '',
            'post_status'            => 'inherit',
            'post_type'              => 'attachment',
            'post_author'            => Auth::id() ?? 1,
            'post_date'              => now(),
            'post_date_gmt'          => now(),
            'post_modified'          => now(),
            'post_modified_gmt'      => now(),
            'post_name'              => Str::slug($request->file('gambar')->getClientOriginalName()),
            'post_parent'            => $post->ID,
            'guid'                   => $gambarUrl,
            'to_ping'                => '',
            'pinged'                 => '',
            'post_content_filtered'  => '',
            'post_mime_type'         => $request->file('gambar')->getMimeType(),
        ]);

        DB::table('ism13qf_postmeta')
            ->where('post_id', $post->ID)
            ->where('meta_key', '_thumbnail_id')
            ->delete();

        DB::table('ism13qf_postmeta')->insert([
            'post_id'    => $post->ID,
            'meta_key'   => '_thumbnail_id',
            'meta_value' => $attachmentId,
        ]);

        DB::table('ism13qf_postmeta')->insert([
            'post_id'    => $attachmentId,
            'meta_key'   => '_wp_attached_file',
            'meta_value' => $gambarPath,
        ]);
    }

    // Update tags
    \App\Models\PostTag::where('post_id', $post->ID)->delete();
    if ($request->has('tags')) {
        foreach ($request->tags as $tag) {
            if (!empty($tag)) {
                \App\Models\PostTag::create([
                    'post_id'  => $post->ID,
                    'tag_name' => $tag,
                ]);
            }
        }
    }

    // Update tagline
    DB::table('ism13qf_postmeta')
    ->updateOrInsert(
        ['post_id' => $post->ID, 'meta_key' => 'tagline_berita'],
        ['meta_value' => $request->tagline ?? '']
    );

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

