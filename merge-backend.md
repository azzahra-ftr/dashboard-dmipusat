# Merge Backend

## Ringkasan

Folder hasil merge dibuat di `C:\xampp\php84\my project\merged-dashboard-dmi`.

Base project yang digunakan adalah `dashboard-dmipusat` karena sidebar, event management, route auth, dan model WordPress/Event lebih lengkap. Project `DASHBOARD_DMI` digunakan sebagai sumber fitur tambahan.

## Project Sumber

- Base: `C:\xampp\php84\my project\merge-backend\dashboard-dmipusat`
- Tambahan: `C:\xampp\php84\my project\merge-backend\DASHBOARD_DMI`

## File Yang Diubah atau Ditambahkan

- `routes/web.php`
- `app/Http/Controllers/Admin/WpPostController.php`
- `app/Models/Notification.php`
- `database/migrations/2026_04_21_145444_create_post_tags_table.php`
- `database/migrations/2026_05_11_165014_create_notifications_table.php`
- `resources/views/admin/layout/layout_admin.blade.php`
- `public/css/layout_admin.css`
- `merge-backend.md`

## Perubahan Route

- Menambahkan route `posts.bulkDelete` untuk hapus berita massal.
- Menambahkan route `notifications.readAll` di dalam group `admin` dan middleware `auth`.
- Mempertahankan route event dari `dashboard-dmipusat` dengan nama `events.index`.
- Mempertahankan redirect `/` ke halaman login.

## Perubahan Controller

`WpPostController` digabung dari kedua aplikasi.

- Pagination post tetap `8` dari `dashboard-dmipusat`.
- Menambahkan validasi judul unik dari fitur `DASHBOARD_DMI`.
- Menambahkan dukungan jadwal publish dengan field `scheduled_at`.
- Menambahkan notifikasi saat berita publish, dijadwalkan, diperbarui, dihapus, dan dihapus massal.
- Menambahkan function `bulkDestroy`.
- Memperbaiki bug `bulkDestroy` dari `DASHBOARD_DMI` yang memakai variabel tidak terdefinisi.
- Menghapus proses upload gambar redundant dari base, sehingga upload hanya diproses satu kali.
- Mengarahkan query tabel WordPress memakai koneksi `wordpress` untuk konsisten dengan model base.

## Perubahan Model dan Migration

- Menambahkan model `Notification`.
- Menambahkan migration `notifications`.
- Menambahkan migration `post_tags`.
- Model `WordpressPost` dan `Event` tetap menggunakan versi `dashboard-dmipusat` karena sudah punya relasi meta dan koneksi `wordpress`.

## Sidebar dan Responsif

- Sidebar utama tetap menggunakan versi `dashboard-dmipusat`.
- Menambahkan panel notifikasi ke layout sidebar tersebut.
- Menambahkan badge jumlah notifikasi belum dibaca.
- Menambahkan tombol tandai semua notifikasi sebagai dibaca.
- Menambahkan behavior responsif sidebar:
  - hamburger toggle
  - overlay
  - klik luar untuk menutup sidebar di mobile
  - tombol Escape untuk menutup sidebar dan panel notifikasi
  - body class `sidebar-open` untuk mengunci scroll saat sidebar mobile terbuka
- Memperbaiki CSS menu aktif yang sebelumnya tidak lengkap.

## Catatan Database

Database dinyatakan sama. Untuk tabel WordPress seperti `ism13qf_posts` dan `ism13qf_postmeta`, project hasil merge mengikuti pendekatan base `dashboard-dmipusat`, yaitu koneksi `wordpress`.

Tabel lokal yang ditambahkan:

- `notifications`
- `post_tags`

## Validasi

Update perbaikan runtime:

- Menambahkan Composer lokal `composer.phar` versi 2.9.8 karena Composer global 2.8.8 gagal pada PHP CLI 8.5.5.
- Dependency Laravel berhasil dipasang melalui `php composer.phar install` dan `php composer.phar dump-autoload`.
- Menambahkan file `.env` untuk environment lokal.
- Menjalankan `php artisan key:generate`.
- Menjalankan `php artisan config:clear`.
- Menjalankan `php artisan storage:link`.
- Memperbaiki namespace `app/Http/Controllers/Admin/BeritaController.php` dari `App\Http\Controllers` menjadi `App\Http\Controllers\Admin` agar sesuai PSR-4.
- Mengubah query insert `BeritaController` ke koneksi `wordpress`.
- Mengubah `DB_PORT` di `.env` dari `3307` ke `3306` mengikuti konfigurasi project sumber `DASHBOARD_DMI`.
- Mengubah koneksi `wordpress` di `config/database.php` agar membaca nilai dari `.env`, bukan hardcoded.
- Memperbaiki `database/seeders/DatabaseSeeder.php` agar mengisi kolom WordPress user (`user_login`, `user_nicename`, `user_email`, `user_pass`, `display_name`) bukan kolom default Laravel (`name`, `email`, `password`).
- Memperbaiki `database/factories/UserFactory.php` agar sesuai struktur tabel `ism13qf_users`.

Validasi syntax PHP berhasil untuk file berikut:

- `app/Http/Controllers/Admin/WpPostController.php`
- `app/Http/Controllers/Admin/EventController.php`
- `app/Http/Controllers/Admin/BeritaController.php`
- `app/Models/Notification.php`
- `routes/web.php`
- `database/migrations/2026_04_21_145444_create_post_tags_table.php`
- `database/migrations/2026_05_11_165014_create_notifications_table.php`
- `database/seeders/DatabaseSeeder.php`
- `database/factories/UserFactory.php`

Validasi `php artisan route:list` berhasil dan menampilkan 21 route.

Validasi `php artisan db:seed` berhasil setelah perbaikan port database dan struktur field user WordPress.

## Update UI Dashboard dan Post Management

- Memperbaiki hamburger desktop agar sidebar hilang penuh ke kiri dan konten/navbar menjadi full width melalui class `sidebar-collapsed`.
- Mempertahankan sidebar drawer mobile dengan overlay.
- Searchbar navbar sekarang hanya tampil pada halaman `posts.*` dan `events.*`.
- Searchbar desktop mengirim query `search` ke halaman aktif, sedangkan searchbar mobile tetap ditangani halaman masing-masing.
- Panel notifikasi dibuat lebih dekat dengan `DASHBOARD_DMI`: notifikasi dikelompokkan per tanggal, icon dan warna berbeda berdasarkan tipe notifikasi.
- Hover sidebar diperbaiki agar icon dan text ikut berubah warna saat hover/active.
- Halaman All Post disesuaikan dengan `DASHBOARD_DMI`, termasuk checkbox bulk delete, kolom category/tag, modal delete, status scheduled, dan reload otomatis untuk scheduled post.
- Query meta penulis pada All Post diarahkan ke koneksi `wordpress`.
- Form Create Post disesuaikan dengan `DASHBOARD_DMI`, termasuk Trix editor, drag/drop upload, preview/progress upload, dan schedule publish dari calendar/time picker.
- Duplikasi ID preview upload dari sumber `DASHBOARD_DMI` dihapus agar JavaScript tidak bentrok.
- Dashboard tetap menggunakan data dinamis hasil merge, lalu dipoles dengan hero, stat card bericon, shadow, spacing, dan responsive grid yang lebih modern.

## Update Create/Edit Post

- Menambahkan asset Trix editor di layout global agar field deskripsi pada create/edit tampil sebagai editor rich text.
- Menghapus dependency visual Summernote dari layout karena create/edit post sekarang menggunakan Trix.
- Searchbar navbar sekarang hanya muncul pada `posts.index` dan `events.index`, sehingga halaman create/edit tidak menampilkan searchbar.
- Menghapus override navbar khusus dari `berita.css` supaya posisi navbar create/edit konsisten dengan halaman lain.
- Halaman edit post disamakan dengan desain create post: Trix editor, drag/drop image upload, preview/progress upload, tag row modern, tombol batal/simpan, dan error modal.

## Update Scheduled Post dan Home

- Menambahkan endpoint `posts.publishScheduledDue` untuk mem-publish post scheduled yang waktunya sudah lewat.
- Halaman All Post sekarang memanggil endpoint publish scheduled sebelum reload otomatis, sehingga status berubah dari `future` ke `publish` saat waktu schedule tiba.
- Dashboard juga menjalankan publish scheduled due sebelum mengambil data, sehingga berita scheduled yang sudah waktunya langsung muncul sebagai publish.
- Form create/edit diperbaiki agar input kata kunci, caption foto, dan tanggal/jam schedule tidak memindahkan fokus ke Trix editor.
- Upload area sekarang hanya membuka file picker saat area upload diklik, bukan saat field lain diketik/diklik.
- Bagian berita di Home menampilkan judul dan tanggal featured post terbaru agar upload berita baru terlihat jelas.

## Update Form, Image, Categories, dan Latest Post

- Struktur form create/edit dirapikan agar field kata kunci, caption foto, dan schedule tidak tertangkap area Trix/upload.
- CSS Trix dibatasi ke `.trix-field` supaya tidak memengaruhi input lain.
- Event input kata kunci, caption, dan schedule diberi isolasi click/focus agar tidak lompat ke deskripsi.
- `WordpressPost::getImageUrl()` ditambah fallback ke meta `_wp_attached_file` jika attachment `guid` kosong.
- `php artisan storage:link` dicek; link `public/storage` sudah ada.
- Kolom Categories di All Post sekarang mengambil meta `tagline_berita` dari form Categories, lalu fallback ke `PostTag`.
- Latest Post di Home kembali memakai `skip(1)->take(3)`, sehingga berita paling baru hanya tampil di card utama.

## Update Responsive 3 Device

- Layout global diperkuat untuk desktop, tablet, dan mobile: navbar, sidebar, home section, profile, notification panel, dan greeting dibuat aman pada layar sempit.
- Dashboard Home ditambah responsive tablet/mobile untuk hero, statistik, grid berita, latest post, dan kegiatan.
- Halaman All Post tetap menampilkan semua kolom dengan horizontal scroll yang lebih rapi pada tablet/mobile.
- Form Create/Edit Post dibuat stack penuh di mobile, tombol submit/draft/schedule/batal full-width, Trix toolbar scroll horizontal, upload area dan tag input menyesuaikan lebar layar.
- Halaman Event dibuat horizontal scroll pada tabel, header dan tombol add event stack di mobile, serta modal event menyesuaikan layar tablet/mobile.
- Halaman Login diperbaiki agar tidak terpotong di mobile dengan `min-height` dan padding responsive.

Smoke test `php artisan serve --host=127.0.0.1 --port=8001` berhasil menampilkan server berjalan. Proses dihentikan otomatis oleh timeout terminal setelah validasi.

Catatan: jika menjalankan Composer, gunakan Composer lokal berikut karena Composer global di mesin ini bermasalah dengan PHP CLI aktif:

```bash
php composer.phar install
php composer.phar dump-autoload
```
