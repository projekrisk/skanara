<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aktivasi Akun - Skanara</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .glass-panel { 
            background: rgba(30, 41, 59, 0.4); 
            backdrop-filter: blur(16px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-4 relative selection:bg-blue-500 selection:text-white">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] translate-x-1/3 translate-y-1/3"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <div class="w-full max-w-md relative z-10 animate-[fadeIn_0.5s_ease-out]">
        
        <div class="glass-panel rounded-2xl p-8 text-center">
            
            <div class="mb-6 flex justify-center">
                <div class="w-20 h-20 bg-blue-600/20 rounded-full flex items-center justify-center border border-blue-500/30 relative">
                    <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-xl"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-400 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-white mb-3">Cek Email Anda</h2>
            
            <p class="text-slate-400 text-sm leading-relaxed mb-6">
                Kami telah mengirimkan tautan aktivasi ke alamat email:
                <br>
                <span class="text-blue-400 font-semibold bg-blue-500/10 px-3 py-1 rounded-full mt-2 inline-block border border-blue-500/20">
                    {{ session('email') ?? 'email Anda' }}
                </span>
            </p>

            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 mb-8 text-left flex gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div class="text-xs text-amber-200/90">
                    <strong class="text-amber-400 block mb-1">Penting:</strong>
                    Akun Anda belum aktif. Mohon klik tombol aktivasi di email sebelum mencoba login.
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('filament.sekolah.auth.login') }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg transform hover:scale-[1.02] transition duration-200">
                    Ke Halaman Login
                </a>
                
                <a href="/" class="block w-full py-3 px-4 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition text-sm font-medium">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        <p class="text-center text-slate-600 text-xs mt-8">
            &copy; {{ date('Y') }} Skanara. All rights reserved.
        </p>
    </div>

</body>
</html>