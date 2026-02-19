<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f3f4f6;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .page-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        
        .card {
            width: 240px; 
            height: 380px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .card-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            height: 120px;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1;
        }
        
        .card-curve {
            position: absolute;
            top: 90px;
            left: 0;
            width: 100%;
            height: 40px;
            background: #fff;
            border-top-left-radius: 50%;
            border-top-right-radius: 50%;
            z-index: 2;
        }

        .card-content {
            position: relative;
            z-index: 3;
            padding: 0 15px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .school-logo-container {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            z-index: 4;
            display: flex;
            justify-content: center;
        }

        .school-logo {
            width: 40px;
        }

        .school-name {
            position: absolute;
            top: 70px;
            left: 0;
            width: -webkit-fill-available;
            z-index: 4;
            color: white;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            padding: 0 10px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .card-title {
            margin-top: 110px;
            font-size: 10px;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            width: 80%;
            margin-bottom: 20px;
        }

        .student-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            text-transform: uppercase;
            line-height: 1.2;
            width: 100%;
            word-wrap: break-word;
        }

        .qr-area {
            width: 140px;
            height: 140px;
            border-radius: 12px;
            border: 2px solid #3b82f6;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .qr-logo-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 3px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            z-index: 10;
        }
        
        .qr-logo-overlay img {
            width: 25px;
            height: 25px;
            object-fit: contain;
        }

        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 25px;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            z-index: 5;
        }

        @media print {
            body { background: none; padding: 0; }
            .no-print { display: none; }
            .card { border: 1px solid #ccc; box-shadow: none; margin: 2px; float: left; }
            .page-container { display: block; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">🖨️ Cetak Kartu</button>
    </div>

    <div class="page-container">
        @foreach($dataSiswa as $siswa)
        <div class="card">
            <div class="card-header"></div>
            
            <div class="school-logo-container">
                @if($siswa->sekolah && $siswa->sekolah->logo)
                    <img src="{{ asset('uploads/' . $siswa->sekolah->logo) }}" class="school-logo" alt="Logo">
                @else
                    <div class="school-logo" style="display:flex;align-items:center;justify-content:center;font-weight:bold;color:#1e40af;font-size:20px;">S</div>
                @endif
            </div>

            <div class="school-name">{{ $siswa->sekolah->nama_sekolah ?? 'Skanara School' }}</div>

            <div class="card-curve"></div>

            <div class="card-content">
                <div class="card-title">KARTU PRESENSI SISWA</div>

                <div class="student-name">{{ $siswa->nama_lengkap }}</div>

                <div class="qr-area">
                    {!! QrCode::size(125)->errorCorrection('H')->generate($siswa->kode_qr_hash ?? $siswa->nis) !!}
                    
                    @if($siswa->sekolah && $siswa->sekolah->logo)
                        <div class="qr-logo-overlay">
                            <img src="{{ asset('uploads/' . $siswa->sekolah->logo) }}" alt="Logo">
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer">Skanara App</div>
        </div>
        @endforeach
    </div>
</body>
</html>