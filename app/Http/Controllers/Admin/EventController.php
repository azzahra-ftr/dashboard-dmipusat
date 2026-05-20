<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\WordpressPost;
use App\Models\WordpressPostMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function home()
    {
        return view('admin.home.home');
    }

   public function index(Request $request)
    {
        $search = $request->search;

        $events = Event::with(['post', 'post.meta'])
            ->when($search, function ($query, $search) {
                $query->whereHas('post', function ($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%')
                    ->orWhere('post_content', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('start_date', 'desc')
            ->paginate(8)
            ->withQueryString();

    return view('admin.events.event', compact('events'));
    }

    // CREATE
    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'event_date'  => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'event_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        DB::connection('wordpress')->beginTransaction();

        try {
            $now  = now();
            $slug = Str::slug($request->title) . '-' . time();

            // 1. Simpan ke tabel posts
            $post = WordpressPost::create([
                'post_title'        => $request->title,
                'post_content'      => $request->description ?? '',
                'post_excerpt'      => '',
                'post_status'       => 'publish',
                'post_type'         => 'tribe_events',
                'post_author'       => 1,
                'post_date'         => $now,
                'post_date_gmt'     => $now,
                'post_modified'     => $now,
                'post_modified_gmt' => $now,
                'post_name'         => $slug,
                'comment_status'    => 'closed',
                'ping_status'       => 'closed',
                'guid'              => '',
            ]);

            // 2. Gabungkan tanggal + waktu
            $startDatetime = $request->event_date . ' ' . $request->start_time . ':00';
            $endDatetime   = $request->event_date . ' ' . $request->end_time . ':00';

            // 3. Simpan ke tabel tec_events
            Event::create([
                'post_id'    => $post->ID,
                'start_date' => $startDatetime,
                'end_date'   => $endDatetime,
                'timezone'   => 'Asia/Jakarta',
            ]);

            // 4. Siapkan postmeta
            $metas = [
                ['post_id' => $post->ID, 'meta_key' => '_event_organizer', 'meta_value' => $request->organizer ?? ''],
                ['post_id' => $post->ID, 'meta_key' => '_event_venue',     'meta_value' => $request->tempat ?? ''],
            ];

            // 5. Handle upload foto
            if ($request->hasFile('event_image')) {
                $file     = $request->file('event_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/events'), $filename);
                $imageUrl = asset('uploads/events/' . $filename);

                $metas[] = ['post_id' => $post->ID, 'meta_key' => '_event_image', 'meta_value' => $imageUrl];
            }

            // 6. Insert semua meta sekaligus
            WordpressPostMeta::insert($metas);

            DB::connection('wordpress')->commit();
            return redirect()->route('events.index')
                             ->with('success', 'Event berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::connection('wordpress')->rollBack();
            return redirect()->back()
                             ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // UPDATE
    public function edit($id)
    {
        $event = Event::with(['post', 'post.meta'])->findOrFail($id);
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'event_date'  => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'event_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $event = Event::with(['post', 'post.meta'])->findOrFail($id);

        DB::connection('wordpress')->beginTransaction();

        try {
            $event->post->update([
                'post_title'        => $request->title,
                'post_content'      => $request->description ?? '',
                'post_modified'     => now(),
                'post_modified_gmt' => now(),
            ]);

            $startDatetime = $request->event_date . ' ' . $request->start_time . ':00';
            $endDatetime   = $request->event_date . ' ' . $request->end_time . ':00';

            $event->update([
                'start_date' => $startDatetime,
                'end_date'   => $endDatetime,
            ]);

            $event->post->setMeta('_event_organizer', $request->organizer ?? '');
            $event->post->setMeta('_event_venue', $request->tempat ?? '');

            if ($request->hasFile('event_image')) {
                $file     = $request->file('event_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/events'), $filename);
                $imageUrl = asset('uploads/events/' . $filename);

                $event->post->setMeta('_event_image', $imageUrl);
            }

            DB::connection('wordpress')->commit();
            return redirect()->route('events.index')
                             ->with('success', 'Event berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::connection('wordpress')->rollBack();
            return redirect()->back()
                             ->with('error', 'Update gagal: ' . $e->getMessage());
        }
    }

    // DELETE
    public function destroy($id)
    {
        DB::connection('wordpress')->beginTransaction();

        try {
            $event = Event::findOrFail($id);

            WordpressPostMeta::where('post_id', $event->post_id)->delete();

            WordpressPost::where('ID', $event->post_id)->delete();

            $event->delete();

            DB::connection('wordpress')->commit();
            return redirect()->route('events.index')
                             ->with('success', 'Event berhasil dihapus!');

        } catch (\Exception $e) {
            DB::connection('wordpress')->rollBack();
            return redirect()->back()
                             ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}