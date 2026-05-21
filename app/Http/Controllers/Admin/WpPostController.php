<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PostTag;
use App\Models\WordpressPost;
use Carbon\Carbon;
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
    $this->publishDueScheduledPosts();

    $query = WordpressPost::with('author')
                ->where('post_type', 'post');

    // Filter berdasarkan keyword search
    if ($request->has('search') && $request->search != '') {
        $query->where('post_title', 'like', '%' . $request->search . '%');
    }

    $posts = $query->orderBy('post_date', 'desc')->paginate(8);

    return view('admin.posts.index', compact('posts'));
}

    public function publishScheduledDue()
    {
        $publishedCount = $this->publishDueScheduledPosts();

        return response()->json([
            'success' => true,
            'published_count' => $publishedCount,
        ]);
    }

    private function publishDueScheduledPosts(): int
    {
        $posts = WordpressPost::where('post_type', 'post')
            ->where('post_status', 'future')
            ->where('post_date', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $post->update([
                'post_status' => 'publish',
                'post_modified' => now(),
                'post_modified_gmt' => now(),
            ]);

            Notification::create([
                'type' => 'post_auto_published',
                'title' => 'Berita Otomatis Dipublish',
                'message' => 'Judul: ' . $post->post_title . "\nTanggal: " . now()->format('d M Y, H:i'),
            ]);
        }

        return $posts->count();
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
        'judul'      => 'required|max:255|unique:wordpress.ism13qf_posts,post_title',
        'penulis'    => 'required|string',
        'isi_berita' => 'required',
        'tags'       => 'nullable|array',
        'tags.*'     => 'nullable|string|max:100',
        'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'caption'    => 'nullable|string',
        'scheduled_at' => 'required_if:status,future|nullable|date|after:now',
    ], [
        'judul.unique' => 'Judul berita sudah digunakan, silakan gunakan judul lain.',
    ]);

    $status = $request->input('status', 'publish');
    $publishDate = $status === 'future' && $request->scheduled_at
        ? Carbon::parse($request->scheduled_at)
        : now();

    // Simpan berita
$post = WordpressPost::create([
    'post_title'             => $request->judul,
    'post_content'           => $request->isi_berita,
    'post_excerpt'           => $request->caption ?? '',
    'post_status'            => $status,
    'post_type'              => 'post',
    'post_author'            => Auth::id() ?? 1,
    'post_date'              => $publishDate,
    'post_date_gmt'          => $publishDate,
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
    DB::connection('wordpress')->table('ism13qf_postmeta')->insert([
        'post_id'    => $post->ID,
        'meta_key'   => 'tagline_berita',
        'meta_value' => $request->tagline,
    ]);
}

// Simpan nama penulis ke postmeta
DB::connection('wordpress')->table('ism13qf_postmeta')->insert([
    'post_id'    => $post->ID,
    'meta_key'   => 'nama_penulis',
    'meta_value' => $request->penulis,
]);

// Simpan gambar ke postmeta
if ($request->hasFile('gambar')) {
    $gambarPath = $request->file('gambar')->store('berita', 'public');
    $gambarUrl  = asset('storage/' . $gambarPath);

    // Simpan attachment sebagai post baru
   $attachmentId = DB::connection('wordpress')->table('ism13qf_posts')->insertGetId([
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
    DB::connection('wordpress')->table('ism13qf_postmeta')->insert([
        'post_id'    => $post->ID,
        'meta_key'   => '_thumbnail_id',
        'meta_value' => $attachmentId,
    ]);

    // Simpan URL gambar di postmeta attachment
    DB::connection('wordpress')->table('ism13qf_postmeta')->insert([
        'post_id'    => $attachmentId,
        'meta_key'   => '_wp_attached_file',
        'meta_value' => $gambarPath,
    ]);
}
    // Simpan tags
    if ($request->has('tags')) {
        foreach ($request->tags as $tag) {
            if (!empty($tag)) {
                PostTag::create([
                    'post_id'  => $post->ID,
                    'tag_name' => $tag,
                ]);
            }
        }
    }

    if ($status === 'publish') {
        Notification::create([
            'type' => 'post_published',
            'title' => 'Berita Baru Dipublish',
            'message' => 'Oleh: ' . $request->penulis . "\nJudul: " . $request->judul . "\nTanggal: " . now()->format('d M Y, H:i'),
        ]);
    }

    if ($status === 'future') {
        Notification::create([
            'type' => 'post_scheduled',
            'title' => 'Berita Dijadwalkan',
            'message' => 'Oleh: ' . $request->penulis . "\nJudul: " . $request->judul . "\nDijadwalkan: " . $publishDate->format('d M Y, H:i'),
        ]);
    }

    $message = $status === 'future'
        ? 'Berita dijadwalkan pada ' . $publishDate->translatedFormat('d F Y, H:i') . ' WIB'
        : 'Berita berhasil diterbitkan!';

    return redirect()->route('posts.index')->with('success', $message);
}
     
    public function edit($id)
{
    $post = WordpressPost::findOrFail($id);
    
    $tags = PostTag::where('post_id', $id)->get();
    
    $namaPenulis = DB::connection('wordpress')->table('ism13qf_postmeta')
        ->where('post_id', $id)
        ->where('meta_key', 'nama_penulis')
        ->value('meta_value');

    $tagline = DB::connection('wordpress')->table('ism13qf_postmeta')
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
    DB::connection('wordpress')->table('ism13qf_postmeta')
        ->updateOrInsert(
            ['post_id' => $post->ID, 'meta_key' => 'nama_penulis'],
            ['meta_value' => $request->penulis]
        );

    // Update gambar jika ada
    if ($request->hasFile('gambar')) {
        $gambarPath = $request->file('gambar')->store('berita', 'public');
        $gambarUrl  = asset('storage/' . $gambarPath);

        $attachmentId = DB::connection('wordpress')->table('ism13qf_posts')->insertGetId([
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

        DB::connection('wordpress')->table('ism13qf_postmeta')
            ->where('post_id', $post->ID)
            ->where('meta_key', '_thumbnail_id')
            ->delete();

        DB::connection('wordpress')->table('ism13qf_postmeta')->insert([
            'post_id'    => $post->ID,
            'meta_key'   => '_thumbnail_id',
            'meta_value' => $attachmentId,
        ]);

        DB::connection('wordpress')->table('ism13qf_postmeta')->insert([
            'post_id'    => $attachmentId,
            'meta_key'   => '_wp_attached_file',
            'meta_value' => $gambarPath,
        ]);
    }

    // Update tags
    PostTag::where('post_id', $post->ID)->delete();
    if ($request->has('tags')) {
        foreach ($request->tags as $tag) {
            if (!empty($tag)) {
                PostTag::create([
                    'post_id'  => $post->ID,
                    'tag_name' => $tag,
                ]);
            }
        }
    }

    // Update tagline
    DB::connection('wordpress')->table('ism13qf_postmeta')
    ->updateOrInsert(
        ['post_id' => $post->ID, 'meta_key' => 'tagline_berita'],
        ['meta_value' => $request->tagline ?? '']
    );

    Notification::create([
        'type' => 'post_updated',
        'title' => 'Berita Diperbarui',
        'message' => 'Oleh: ' . $request->penulis . "\nJudul: " . $request->judul . "\nTanggal: " . now()->format('d M Y, H:i'),
    ]);

    return redirect()->route('posts.index')->with('success', 'Berita berhasil diperbarui!');
}


    public function destroy($id)
    {
        $post = WordpressPost::findOrFail($id);
        $judulBerita = $post->post_title;
        $namaPenulis = DB::connection('wordpress')->table('ism13qf_postmeta')
            ->where('post_id', $id)
            ->where('meta_key', 'nama_penulis')
            ->value('meta_value');

        DB::connection('wordpress')->table('ism13qf_posts')
            ->where('post_parent', $id)
            ->where('post_type', 'attachment')
            ->delete();

        PostTag::where('post_id', $id)->delete();
        DB::connection('wordpress')->table('ism13qf_postmeta')->where('post_id', $id)->delete();
        $post->delete();

        Notification::create([
            'type' => 'post_deleted',
            'title' => 'Berita Dihapus',
            'message' => 'Oleh: ' . ($namaPenulis ?? 'Admin') . "\nJudul: " . $judulBerita . "\nTanggal: " . now()->format('d M Y, H:i'),
        ]);

        return redirect()->route('posts.index')->with('success', 'Berita berhasil dihapus!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('posts.index')->with('error', 'Tidak ada berita yang dipilih!');
        }

        $posts = WordpressPost::whereIn('ID', $ids)->pluck('post_title', 'ID');

        DB::connection('wordpress')->table('ism13qf_posts')
            ->whereIn('post_parent', $ids)
            ->where('post_type', 'attachment')
            ->delete();

        PostTag::whereIn('post_id', $ids)->delete();
        DB::connection('wordpress')->table('ism13qf_postmeta')->whereIn('post_id', $ids)->delete();
        WordpressPost::whereIn('ID', $ids)->delete();

        Notification::create([
            'type' => 'post_deleted',
            'title' => 'Berita Dihapus Massal',
            'message' => count($ids) . " berita dihapus:\n" . $posts->implode("\n"),
        ]);

        return redirect()->route('posts.index')->with('success', count($ids) . ' berita berhasil dihapus!');
    }

    public function eventIndex()
    {   
        $events = \App\Models\Event::with('post')
                ->orderBy('start_date', 'desc')
                ->paginate(8);
        
        return view('admin.events.event', compact('events'));
    }
}
