<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ config('app.name', 'Skanara') }} - Sistem Presensi Modern</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-card { background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05)); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); }
        .text-glow { text-shadow: 0 0 30px rgba(56, 189, 248, 0.4); }
        
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 7s ease-in-out infinite; animation-delay: 2s; }
        
        .fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-950 text-white antialiased selection:bg-blue-500 selection:text-white h-screen overflow-hidden flex flex-col">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-[50vh] h-[50vh] bg-blue-600/10 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-[50vh] h-[50vh] bg-indigo-600/10 rounded-full blur-[80px] translate-y-1/3 -translate-x-1/3"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
    </div>

    <header class="w-full z-50 glass-nav flex-none">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition overflow-hidden">
                        <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="text-lg sm:text-xl font-bold tracking-tight text-white group-hover:text-blue-200 transition">SKANARA</span>
                </a>
                
                @auth
                    <a href="{{ auth()->user()->peran === 'super_admin' ? '/admin' : '/sekolah' }}" class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 focus:ring-offset-slate-900 shadow-lg shadow-blue-500/30">
                        Dasbor
                    </a>
                @else
                    <a href="/sekolah/login" class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold text-white transition-all duration-200 bg-white/10 border border-white/10 rounded-lg hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 focus:ring-offset-slate-900">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="relative z-10 flex-1 flex items-center justify-center w-full px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center h-full max-h-[800px]">
            <div class="text-center lg:text-left fade-in-up flex flex-col justify-center h-full pt-4 lg:pt-0">
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold tracking-tight mb-4 sm:mb-6 leading-[1.1]">
                    Kelola Presensi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 text-glow">Lebih Cerdas.</span>
                </h1>
                <p class="text-base sm:text-lg text-slate-400 mb-6 sm:mb-8 leading-relaxed max-w-lg mx-auto lg:mx-0">
                    Platform manajemen kehadiran siswa berbasis <strong>QR Code</strong> dan <strong>aplikasi Android</strong>. Data real-time, laporan akurat, tanpa ribet.
                </p>
                <div class="flex justify-center lg:justify-start">
                    @auth
                        <a href="{{ auth()->user()->peran === 'super_admin' ? '/admin' : '/sekolah' }}" class="w-40 inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-xl hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 shadow-[0_0_20px_rgba(37,99,235,0.3)] hover:shadow-[0_0_30px_rgba(37,99,235,0.5)]">
                            Dasbor
                            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('register.sekolah') }}" class="w-40 inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-xl hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 shadow-[0_0_20px_rgba(37,99,235,0.3)] hover:shadow-[0_0_30px_rgba(37,99,235,0.5)]">
                            Mulai
                            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @endauth
                </div>
                <div class="mt-8 sm:mt-10 pt-6 sm:pt-8 border-t border-slate-800 flex items-center justify-center lg:justify-start gap-6 sm:gap-8 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition duration-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span class="text-xs sm:text-sm font-semibold">Mobile App</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-xs sm:text-sm font-semibold">Support Offline</span>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-[300px] sm:h-[400px] lg:h-[500px] flex items-center justify-center fade-in-up lg:delay-200">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[250px] h-[250px] lg:w-[450px] lg:h-[450px] bg-gradient-to-br from-indigo-500/20 to-purple-600/20 rounded-full blur-[60px] animate-pulse"></div>
                <div class="absolute top-[5%] right-[5%] lg:right-[10%] glass-card p-4 rounded-2xl w-48 sm:w-56 lg:w-64 animate-float z-20">
                    <div class="flex justify-between items-center mb-3">
                        <div class="text-[10px] sm:text-xs text-gray-400 font-semibold uppercase">Kehadiran</div>
                        <div class="text-emerald-400 bg-emerald-400/10 px-1.5 py-0.5 rounded text-[10px] font-bold">+12%</div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-white mb-2">98.5%</div>
                    <div class="h-1.5 w-full bg-gray-700/50 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-cyan-400 w-[98%]"></div>
                    </div>
                </div>
                <div class="absolute bottom-[5%] left-[5%] lg:left-[10%] glass-card p-4 rounded-2xl w-52 sm:w-64 lg:w-72 animate-float-delayed z-30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-sm lg:text-lg font-bold shadow-lg">JD</div>
                        <div>
                            <div class="text-sm font-bold text-white">John Doe</div>
                            <div class="text-[10px] sm:text-xs text-gray-400">Kelas XII IPA 1</div>
                        </div>
                    </div>
                    <div class="mt-4 bg-white/5 border border-white/5 p-2 rounded-lg flex items-center justify-between px-3">
                         <span class="text-[10px] text-gray-300">Status</span>
                         <span class="text-[10px] sm:text-xs font-bold text-green-400 tracking-wide">HADIR • 06:45</span>
                    </div>
                </div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-24 sm:w-32 sm:h-32 lg:w-40 lg:h-40 glass-card rounded-full flex items-center justify-center z-10">
                    <div class="w-16 h-16 sm:w-24 sm:h-24 bg-indigo-500/30 rounded-full blur-xl absolute"></div>
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 lg:w-20 lg:h-20 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
            </div>

        </div>
    </main>

    <footer class="w-full py-4 text-center border-t border-white/5 bg-slate-950 text-slate-600 text-[10px] sm:text-xs flex-none z-20">
        <p>&copy; {{ date('Y') }} Skanara Digital.</p>
    </footer>

</body>
</html>