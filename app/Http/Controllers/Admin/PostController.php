<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WordpressPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
{
    // Mengambil data dari database (asumsi nama modelnya Post)
    $posts = WordpressPost::latest()->paginate(10); 

    // Mengirim data ke view index.blade.php
    return view('admin.posts.index', compact('posts'));
}
}
