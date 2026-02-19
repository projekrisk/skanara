<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Sekolah - Skanara</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .glass-panel { 
            background: rgba(30, 41, 59, 0.4); 
            backdrop-filter: blur(16px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }

        .form-input {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #3b82f6;
            background-color: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
            outline: none;
        }
        .form-input::placeholder { color: #64748b; }
        
        input[type="file"]::file-selector-button {
            background-image: linear-gradient(to right, #2563eb, #3b82f6);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-right: 1rem;
        }
        input[type="file"]::file-selector-button:hover { opacity: 0.9; }

        .step-content {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-4 relative selection:bg-blue-500 selection:text-white overflow-y-auto">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] translate-x-1/3 translate-y-1/3"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
    </div>

    <div x-data="{ step: 1 }" class="w-full max-w-2xl relative z-10 my-8 animate-[fadeIn_0.5s_ease-out]">
        
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3 mb-4 group hover:opacity-80 transition">
                <div class="w-10 h-10 rounded-xl overflow-hidden shadow-lg border border-white/10">
                    <img src="{{ asset('favicon.png') }}" alt="Skanara Logo" class="w-full h-full object-cover">
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">SKANARA</span>
            </a>
            <h1 class="text-3xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">Registrasi Sekolah</h1>
            <p class="text-slate-400 mt-2 text-sm">Lengkapi data sekolah dan admin untuk memulai.</p>
        </div>

        <div class="glass-panel rounded-2xl p-6 sm:p-10">
            <div class="flex justify-center mb-8">
                <div class="flex items-center space-x-4">
                    <button @click="step = 1" :class="step === 1 ? 'bg-blue-600 text-white border-blue-600' : 'bg-transparent text-slate-400 border-slate-600'" class="w-10 h-10 rounded-full flex items-center justify-center border-2 font-bold transition-all duration-300">1</button>
                    <div :class="step === 2 ? 'bg-blue-600' : 'bg-slate-700'" class="w-16 h-1 rounded-full transition-all duration-300"></div>
                    <button :class="step === 2 ? 'bg-blue-600 text-white border-blue-600' : 'bg-transparent text-slate-400 border-slate-600'" class="w-10 h-10 rounded-full flex items-center justify-center border-2 font-bold transition-all duration-300 cursor-default">2</button>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-lg text-sm mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside space-y-0.5 opacity-90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('register.sekolah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-[-20px]" x-transition:enter-end="opacity-100 translate-x-0" class="step-content space-y-5">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-white">Data Sekolah</h3>
                        <p class="text-xs text-slate-400">Informasi dasar instansi sekolah Anda</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-400 ml-1">NPSN</label>
                            <input type="text" name="npsn" value="{{ old('npsn') }}" required class="form-input w-full px-4 py-2.5 rounded-xl" placeholder="12345678">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-400 ml-1">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah') }}" required class="form-input w-full px-4 py-2.5 rounded-xl" placeholder="Contoh: SMA Negeri 1 Maju">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-400 ml-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" class="form-input w-full px-4 py-2.5 rounded-xl resize-none" placeholder="Jalan, Kota, Provinsi">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-400 ml-1">Logo Sekolah</label>
                        <div class="relative">
                            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition cursor-pointer">
                        </div>
                        <p class="text-[10px] text-slate-500 ml-1">Format: JPG/PNG. Maksimal 2MB.</p>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" @click="step = 2" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl transition flex items-center gap-2">
                            Lanjut
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>

                <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-[20px]" x-transition:enter-end="opacity-100 translate-x-0" class="step-content space-y-5">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-white">Akun Administrator</h3>
                        <p class="text-xs text-slate-400">Data login untuk pengelola sekolah</p>
                    </div>

                    <div class="flex justify-center mb-4">
                        <div class="space-y-1.5 w-full max-w-xs text-center">
                            <label class="text-xs font-medium text-slate-400 block mb-2">Foto Profil</label>
                            <input type="file" name="foto_profil" accept="image/*" class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer mx-auto">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-400 ml-1">Nama Lengkap Admin</label>
                            <input type="text" name="nama_admin" value="{{ old('nama_admin') }}" required class="form-input w-full px-4 py-2.5 rounded-xl" placeholder="Nama Lengkap">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-400 ml-1">Email Login</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="form-input w-full px-4 py-2.5 rounded-xl" placeholder="admin@sekolah.sch.id">
                            <p class="text-[10px] text-amber-400 ml-1 flex items-center gap-1 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                Link aktivasi akun akan dikirim ke email ini. Pastikan aktif.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-400 ml-1">Password</label>
                            <input type="password" name="password" required class="form-input w-full px-4 py-2.5 rounded-xl" placeholder="********">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-400 ml-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="form-input w-full px-4 py-2.5 rounded-xl" placeholder="********">
                        </div>
                    </div>

                    <div class="pt-6 flex items-center justify-between gap-4">
                        <button type="button" @click="step = 1" class="text-slate-400 hover:text-white font-medium py-2.5 px-4 rounded-xl transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Kembali
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl transition flex items-center gap-2">
                            Daftar
                        </button>
                    </div>
                </div>

            </form>
        </div>
        
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-slate-400 hover:text-white transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>