<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WordpressPost;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $this->publishDueScheduledPosts();

        // ── STATISTIK KARTU ATAS ──

        // Total berita (publish saja)
        $totalBerita = WordpressPost::where('post_status', 'publish')
            ->where('post_type', 'post')
            ->count();

        // Total views semua postingan
        $totalViews = DB::connection('wordpress')
            ->table('ism13qf_postmeta')
            ->where('meta_key', 'trx_addons_post_views_count')
            ->sum('meta_value');

        // Upcoming post (terjadwal)
        $upcomingPost = WordpressPost::where('post_status', 'future')
            ->where('post_type', 'post')
            ->count();

        // Total event
        $totalEvent = Event::count();

        // Event berlangsung
        $eventBerlangsung = Event::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        // ── STATUS POSTINGAN ──
        $postPublish = WordpressPost::where('post_status', 'publish')
            ->where('post_type', 'post')
            ->count();

        $postTerjadwal = WordpressPost::where('post_status', 'future')
            ->where('post_type', 'post')
            ->count();

        $totalPostAll  = $postPublish + $postTerjadwal;
        $pctPublish    = $totalPostAll > 0 ? round($postPublish / $totalPostAll * 100) : 0;
        $pctTerjadwal  = $totalPostAll > 0 ? round($postTerjadwal / $totalPostAll * 100) : 0;

        // ── BERITA TERPOPULER ──
        $popularPosts = DB::connection('wordpress')
            ->table('ism13qf_postmeta as pm')
            ->join('ism13qf_posts as p', 'p.ID', '=', 'pm.post_id')
            ->where('pm.meta_key', 'trx_addons_post_views_count')
            ->where('p.post_type', 'post')
            ->where('p.post_status', 'publish')
            ->select('p.post_title', 'p.post_date', 'pm.meta_value as view_count')
            ->orderByRaw('CAST(pm.meta_value AS UNSIGNED) DESC')
            ->limit(5)
            ->get();

        // ── EVENT MENDATANG ──
        $upcomingEvents = Event::with('post')
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();

        // ── KONTEN LAMA ──
        $featuredPost = WordpressPost::where('post_status', 'publish')
            ->where('post_type', 'post')
            ->orderBy('post_date', 'desc')
            ->first();

        $latestPosts = WordpressPost::where('post_status', 'publish')
            ->where('post_type', 'post')
            ->orderBy('post_date', 'desc')
            ->skip(1)
            ->take(3)
            ->get();

        $events = Event::with(['post', 'post.meta'])
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get();

        return view('admin.home.home', compact(
            'totalBerita', 'totalViews', 'upcomingPost', 'totalEvent',
            'eventBerlangsung', 'postPublish', 'postTerjadwal',
            'pctPublish', 'pctTerjadwal', 'popularPosts', 'upcomingEvents',
            'featuredPost', 'latestPosts', 'events'
        ));
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
}
