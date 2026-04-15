<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WordpressPost;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $headline = WordpressPost::where('post_status', 'publish')
                                  ->where('post_type', 'post')
                                   ->latest('post_date')
                                   ->first();

        $latestPosts = WordpressPost::where('post_status', 'publish')
                                    ->where('post_type', 'post')
                                    ->where('ID', '!=', $headline->ID ?? 0)
                                    ->latest('post_date')
                                    ->take(3)
                                    ->get();
                            
        
        $upcomingEvents = $upcomingEvents = Event::orderBy('start_date', 'desc')->first();

        $totalUsers = DB::connection('wordpress')
                        ->table('ism13qf_users')
                        ->count();

        return view('admin.home.home', compact('headline', 'latestPosts', 'upcomingEvents', 'totalUsers'));
    }
}