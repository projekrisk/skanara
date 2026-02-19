<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Aktivasi Akun Sekolah Skanara</title>
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            vertical-align: top;
        }

        .body {
            background-color: #f1f5f9;
            width: 100%;
        }

        .container {
            display: block;
            margin: 0 auto !important;
            max-width: 600px;
            padding: 20px;
            width: 600px;
        }

        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 600px;
            padding: 10px;
        }

        .main {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 30px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .header {
            padding: 20px 0;
            text-align: center;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: #3b82f6;
            text-decoration: none;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h1, h2, h3, h4 {
            color: #1e293b;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: 700;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }
        
        h2 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        p, ul, ol {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 15px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
            color: #475569;
        }

        .btn {
            box-sizing: border-box;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .btn > tbody > tr > td {
            padding-bottom: 15px;
        }

        .btn table {
            width: 100%;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3b82f6;
            border-radius: 8px;
            box-sizing: border-box;
            color: #3b82f6;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #3b82f6;
        }

        .btn-primary a {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #ffffff;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }
        
        .link-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            font-family: monospace;
            font-size: 12px;
            color: #64748b;
            word-break: break-all;
            text-align: center;
        }
        
        .hr-line {
            border: 0;
            border-bottom: 1px solid #e2e8f0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">

                    <div class="header">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="align-center">
                                    <a href="{{ config('app.url') }}" class="logo-text">SKANARA</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <h2>Aktivasi Akun Sekolah</h2>
                                            
                                            <p>Halo <strong>{{ $user->name }}</strong>,</p>
                                            <p>Terima kasih telah mendaftarkan sekolah Anda di Skanara. Langkah terakhir untuk mulai menggunakan aplikasi adalah mengaktifkan akun Anda.</p>
                                            
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                <tbody>
                                                    <tr>
                                                        <td align="center">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td> 
                                                                            <a href="{{ route('register.verify', ['token' => $user->activation_token]) }}" target="_blank">Aktifkan Akun Saya</a> 
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                            <p>Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:</p>
                                            
                                            <div class="link-box">
                                                {{ route('register.verify', ['token' => $user->activation_token]) }}
                                            </div>
                                            
                                            <div class="hr-line"></div>
                                            
                                            <p style="font-size: 13px; color: #94a3b8;">Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.</p>
                                            
                                            <br>
                                            <p>Salam hangat,<br><strong>Tim Skanara</strong></p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">Skanara - Sistem Presensi Sekolah Digital</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="content-block powered-by">
                                    &copy; {{ date('Y') }} Skanara. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</body>
</html>