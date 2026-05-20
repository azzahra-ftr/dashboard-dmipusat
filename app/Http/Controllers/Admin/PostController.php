<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WordpressPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
{
    $posts = WordpressPost::latest()->paginate(10); 

    return view('admin.posts.index', compact('posts'));
}
}

// ini udah sama sama yang iseh
