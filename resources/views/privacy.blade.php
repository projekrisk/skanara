<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Skanara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<style>
        .bg-grid {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
        .no-scroll { overflow: hidden; }
    </style>
    
<body class="bg-gray-50 text-gray-800">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <!-- Ganti src ini dengan path logo Anda jika ada, atau gunakan text saja -->
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold text-lg shadow-[0_0_15px_rgba(59,130,246,0.5)]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                <span class="font-bold text-xl tracking-wide">Skan<span class="text-cyan-400">ara</span></span>
            </div>
            <a href="/" class="text-sm text-gray-500 hover:text-blue-600 transition">Kembali ke Beranda</a>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold mb-2">Kebijakan Privasi</h1>
        <p class="text-gray-500 mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
            
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Pendahuluan</h2>
                <p class="leading-relaxed text-gray-600">
                    Selamat datang di <strong>Skanara</strong> ("Aplikasi"). Kami menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi yang Anda bagikan kepada kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat menggunakan aplikasi mobile Skanara (Android) dan layanan web kami.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Informasi yang Kami Kumpulkan</h2>
                <p class="leading-relaxed text-gray-600 mb-2">
                    Agar Aplikasi dapat berfungsi dengan baik untuk keperluan manajemen sekolah dan presensi, kami mengumpulkan jenis informasi berikut:
                </p>
                <ul class="list-disc list-outside ml-5 space-y-2 text-gray-600">
                    <li><strong>Informasi Perangkat (Device ID):</strong> Kami mengumpulkan ID unik perangkat (Android ID) khusus untuk fitur "Mode Kiosk". Hal ini diperlukan untuk memverifikasi keamanan dan memastikan bahwa presensi hanya dilakukan dari perangkat sekolah yang terdaftar dan sah.</li>
                    <li><strong>Kamera:</strong> Aplikasi meminta izin akses kamera untuk memindai QR Code pada kartu pelajar guna mencatat kehadiran siswa.</li>
                    <li><strong>Penyimpanan (Storage):</strong> Kami mungkin mengakses penyimpanan untuk menyimpan foto profil siswa atau data cache sementara untuk mendukung fitur offline.</li>
                    <li><strong>Data Pribadi:</strong> Nama, NISN, Kelas, dan Foto Siswa yang didaftarkan oleh Admin Sekolah.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. Penggunaan Informasi</h2>
                <p class="leading-relaxed text-gray-600">
                    Informasi yang kami kumpulkan digunakan semata-mata untuk:
                </p>
                <ul class="list-disc list-outside ml-5 space-y-2 text-gray-600 mt-2">
                    <li>Memproses pencatatan kehadiran siswa secara real-time.</li>
                    <li>Memvalidasi perangkat kios untuk mencegah kecurangan absensi.</li>
                    <li>Menyediakan laporan kehadiran kepada guru dan admin sekolah.</li>
                    <li>Meningkatkan kinerja dan stabilitas aplikasi.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Keamanan Data</h2>
                <p class="leading-relaxed text-gray-600">
                    Kami menerapkan langkah-langkah keamanan teknis yang wajar untuk melindungi data Anda dari akses yang tidak sah. Semua komunikasi data antara Aplikasi dan Server dienkripsi menggunakan protokol SSL/HTTPS.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">5. Berbagi Data pada Pihak Ketiga</h2>
                <p class="leading-relaxed text-gray-600">
                    Skanara <strong>tidak menjual, memperdagangkan, atau menyewakan</strong> informasi identifikasi pribadi pengguna kepada pihak lain. Data hanya dapat diakses oleh pihak sekolah yang berwenang (Admin Sekolah dan Guru) yang memiliki hak akses.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">6. Privasi Anak-Anak</h2>
                <p class="leading-relaxed text-gray-600">
                    Meskipun aplikasi ini mencatat data siswa, pengguna utama aplikasi (yang mengoperasikan) ditujukan untuk Guru, Staf, dan Administrator Sekolah. Kami tidak secara sadar mengumpulkan data pribadi dari anak-anak di bawah usia 13 tahun secara langsung tanpa persetujuan dari sekolah atau wali.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">7. Hubungi Kami</h2>
                <p class="leading-relaxed text-gray-600">
                    Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami melalui:<br>
                    Email: <strong>support@skanara.com</strong> (Ganti dengan email asli Anda)
                </p>
            </section>

        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-8 text-center">
        <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Skanara. All rights reserved.</p>
    </footer>

</body>
</html>