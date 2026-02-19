<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi - Skanara</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        h2 { margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 700; color: #1e293b; font-size: 1.25rem; }
        p, ul { margin-bottom: 1rem; color: #475569; line-height: 1.7; }
        ul { list-style-type: disc; padding-left: 1.5rem; }
        li { margin-bottom: 0.5rem; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <div class="w-full bg-white shadow-sm fixed top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <span class="font-bold text-xl text-blue-600">SKANARA</span>
                </div>
                <div>
                    <a href="/" class="text-slate-600 hover:text-blue-600 text-sm font-medium transition">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-12">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-2">Kebijakan Privasi</h1>
                <p class="text-slate-500 text-sm">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>

            <div class="prose max-w-none">
                <p>
                    Selamat datang di <strong>Skanara</strong> ("Aplikasi"). Kami menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi serta informasi sekolah yang Anda percayakan kepada kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat menggunakan layanan kami.
                </p>

                <h2>1. Informasi yang Kami Kumpulkan</h2>
                <p>Untuk memberikan layanan presensi yang akurat dan efisien, kami mengumpulkan jenis informasi berikut:</p>
                <ul>
                    <li><strong>Data Sekolah & Admin:</strong> Nama sekolah, NPSN, alamat, nama admin, dan alamat email untuk keperluan registrasi dan pengelolaan akun.</li>
                    <li><strong>Data Siswa & Guru:</strong> Nama lengkap, Nomor Induk Siswa (NIS/NISN), jenis kelamin, kelas, dan foto profil untuk keperluan identifikasi presensi.</li>
                    <li><strong>Data Perangkat (Kiosk):</strong> ID Perangkat (Device ID) digunakan untuk mengamankan dan memverifikasi perangkat yang diizinkan melakukan scanning di lingkungan sekolah.</li>
                    <li><strong>Data Log Presensi:</strong> Waktu masuk, waktu pulang, status kehadiran (Hadir, Sakit, Izin, Alpa), dan foto bukti kehadiran (jika fitur diaktifkan).</li>
                </ul>

                <h2>2. Izin Perangkat (Permissions)</h2>
                <p>Aplikasi Skanara (terutama versi Mobile/Android) memerlukan izin akses tertentu pada perangkat Anda agar dapat berfungsi dengan baik:</p>
                <ul>
                    <li><strong>Kamera:</strong> Diperlukan untuk memindai QR Code pada Kartu Siswa saat melakukan presensi di mode Kiosk.</li>
                    <li><strong>Penyimpanan (Storage):</strong> Diperlukan untuk menyimpan sementara data foto siswa saat sinkronisasi, serta untuk mengunduh laporan dalam format Excel/PDF.</li>
                    <li><strong>Internet:</strong> Diperlukan untuk sinkronisasi data antara aplikasi Kiosk/Guru dengan Server Pusat (Cloud).</li>
                    <li><strong>Cegah Tidur (Wake Lock):</strong> Diperlukan pada mode Kiosk agar layar perangkat tetap menyala selama jam operasional presensi.</li>
                </ul>

                <h2>3. Penggunaan Informasi</h2>
                <p>Kami menggunakan data yang dikumpulkan untuk tujuan:</p>
                <ul>
                    <li>Mencatat dan merekapitulasi kehadiran siswa dan guru secara realtime.</li>
                    <li>Menyediakan laporan harian dan bulanan kepada pihak sekolah dan wali kelas.</li>
                    <li>Memverifikasi identitas siswa melalui pemindaian QR Code.</li>
                    <li>Menghubungi sekolah terkait pembaruan sistem, tagihan layanan, atau masalah teknis.</li>
                </ul>

                <h2>4. Keamanan Data</h2>
                <p>
                    Kami menerapkan langkah-langkah keamanan teknis untuk melindungi data Anda dari akses yang tidak sah. Password akun disimpan menggunakan enkripsi (Hashing), dan komunikasi data antara aplikasi dan server dilindungi menggunakan protokol keamanan standar. Kami tidak akan menjual atau menyewakan data pribadi siswa atau sekolah kepada pihak ketiga mana pun.
                </p>

                <h2>5. Hak Pengguna</h2>
                <p>Sebagai pengguna (Pihak Sekolah), Anda memiliki hak untuk:</p>
                <ul>
                    <li>Mengakses, memperbarui, atau menghapus data siswa dan guru melalui Panel Admin Sekolah.</li>
                    <li>Mengekspor data laporan presensi kapan saja.</li>
                    <li>Mengajukan permohonan penghapusan akun sekolah secara permanen dengan menghubungi layanan pelanggan kami.</li>
                </ul>

                <h2>6. Perubahan Kebijakan Privasi</h2>
                <p>
                    Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Setiap perubahan akan diberitahukan melalui halaman ini atau melalui notifikasi di dalam aplikasi. Dengan terus menggunakan layanan kami setelah perubahan tersebut, Anda dianggap menyetujui kebijakan yang baru.
                </p>

                <h2>7. Hubungi Kami</h2>
                <p>
                    Jika Anda memiliki pertanyaan atau kekhawatiran mengenai Kebijakan Privasi ini, silakan hubungi kami melalui:
                </p>
                <p class="font-medium text-slate-900">
                    Email: <a href="mailto:admin@skanara.com" class="text-blue-600 hover:underline">admin@skanara.com</a><br>
                    Website: www.skanara.com
                </p>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-200 text-center">
                <a href="/" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                    Mengerti, Kembali ke Beranda
                </a>
            </div>

        </div>
        
        <div class="text-center mt-8 text-slate-400 text-sm">
            &copy; {{ date('Y') }} Skanara. All rights reserved.
        </div>
    </div>

</body>
</html>