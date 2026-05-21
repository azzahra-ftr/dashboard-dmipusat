<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'judul'      => 'required|string',
            'penulis'    => 'required|string',
            'isi_berita' => 'required|string',
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

        // Simpan berita ke tabel ism13qf_posts
        $postId = DB::connection('wordpress')->table('ism13qf_posts')->insertGetId([
            'post_title'         => $request->judul,
            'post_author'        => $request->penulis,
            'post_content'       => $request->isi_berita,
            'post_excerpt'       => $request->caption ?? '',
            'post_status'        => 'publish',
            'post_date'          => now(),
            'post_date_gmt'      => now(),
            'post_modified'      => now(),
            'post_modified_gmt'  => now(),
            'post_name'          => Str::slug($request->judul),
            'post_type'          => 'post',
            'comment_status'     => 'open',
            'ping_status'        => 'open',
            'guid'               => $gambarPath ?? '',
            'to_ping'            => '',
            'pinged'             => '',
            'post_content_filtered' => '',
        ]);

        // Simpan tags
        if ($request->has('tags')) {
            foreach ($request->tags as $tag) {
                if (!empty($tag)) {
                    PostTag::create([
                        'post_id'  => $postId,
                        'tag_name' => $tag,
                    ]);
                }
            }
        }

        return redirect()->route('posts.index')
                         ->with('success', 'Berita berhasil ditambahkan!');
    }
}
