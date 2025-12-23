<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
        <title>@yield('title')</title>

        <style>
            :root {
                --primary: #EF874B;
                --primary-dark: #D94313;
                --primary-light: #FCDA7B;
                --primary-rgb: 239, 135, 75;
                --text: #2c3e50;
                --text-light: #7f8c8d;
                --bg: #FFFBDF;
                --bg-offset: #ffffff;
                --border: #e8e3c3;
                --accent-red: #dc2626;
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --primary: #EF874B;
                    --primary-dark: #D94313;
                    --primary-light: #FCDA7B;
                    --primary-rgb: 239, 135, 75;
                    --text: #f1f5f9;
                    --text-light: #cbd5e0;
                    --bg: #1a1a1a;
                    --bg-offset: #2d2d2d;
                    --border: #3a3a3a;
                    --accent-red: #ef4444;
                }
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background-color: var(--bg);
                color: var(--text);
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 2rem;
                overflow-x: hidden;
            }

            .error-container {
                position: relative;
                max-width: 600px;
                width: 100%;
                z-index: 10;
            }

            .error-box {
                background-color: var(--bg-offset);
                padding: 2.5rem;
                border-radius: 1rem;
                box-shadow: 0 8px 15px -5px rgba(0, 0, 0, 0.05);
                position: relative;
                overflow: hidden;
            }

            .error-pattern {
                position: absolute;
                top: -50%;
                right: -50%;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, var(--primary) 25%, transparent 25%, transparent 50%, var(--primary) 50%, var(--primary) 75%, transparent 75%, transparent);
                background-size: 20px 20px;
                opacity: 0.05;
                transform: scale(5) rotate(15deg);
                z-index: -1;
                border-radius: 20%;
                aspect-ratio: 1;
            }

            .error-code {
                font-size: 7rem;
                font-weight: 900;
                line-height: 1;
                color: var(--primary);
                margin-bottom: 1.5rem;
                position: relative;
                display: inline-block;
                overflow: visible;
                text-shadow: 2px 2px 0px rgba(var(--primary-rgb), 0.2);
            }
            
            .error-code::before {
                content: '';
                position: absolute;
                width: 120%;
                height: 120%;
                left: -10%;
                top: -10%;
                background: radial-gradient(circle at center, rgba(var(--primary-rgb), 0.15), transparent 70%);
                z-index: -1;
                border-radius: 50%;
            }

            .error-code::after {
                content: '@yield('code')';
                position: absolute;
                left: 0.25rem;
                top: 0.25rem;
                color: var(--text);
                opacity: 0.1;
                z-index: -1;
            }

            .error-emoji {
                display: none;
            }

            .error-message {
                font-size: 1.25rem;
                font-weight: 600;
                color: var(--text);
                margin-bottom: 1.5rem;
                max-width: 80%;
            }
            
            .divider {
                height: 1px;
                background-color: var(--border);
                margin: 1.5rem 0;
                width: 100%;
            }

            .error-description {
                color: var(--text-light);
                margin-bottom: 2rem;
                line-height: 1.6;
            }

            .action-button {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background-color: transparent;
                color: var(--primary);
                font-weight: 600;
                padding: 0;
                text-decoration: none;
                transition: all 0.2s;
                position: relative;
                border: none;
            }
            
            .action-button::after {
                content: '';
                position: absolute;
                left: 0;
                bottom: -2px;
                width: 100%;
                height: 2px;
                background-color: var(--primary);
                transform-origin: right;
                transform: scaleX(0);
                transition: transform 0.3s ease;
            }

            .action-button:hover {
                background-color: transparent;
                border-color: transparent;
                transform: translateY(0);
                box-shadow: none;
            }
            
            .action-button:hover::after {
                transform-origin: left;
                transform: scaleX(1);
            }

            .action-button svg {
                transition: transform 0.2s;
            }

            .action-button:hover svg {
                transform: translateX(-3px);
            }

            .action-button::before {
                content: none;
            }

            .shape {
                position: absolute;
                opacity: 0.1;
                border-radius: 50%;
                background-color: var(--primary);
                z-index: -1;
            }

            .shape-1 {
                width: 150px;
                height: 150px;
                top: -75px;
                left: -75px;
            }

            .shape-2 {
                width: 100px;
                height: 100px;
                bottom: -20px;
                right: 40%;
            }

            .error-icon {
                position: absolute;
                top: 2rem;
                right: 2rem;
                width: 3rem;
                height: 3rem;
                color: var(--primary);
                opacity: 0.6;
            }

            @media (max-width: 640px) {
                .error-code {
                    font-size: 5rem;
                }

                .error-message {
                    font-size: 1.25rem;
                    max-width: 100%;
                }

                .error-box {
                    padding: 2rem;
                }
                
                .error-icon {
                    width: 2.5rem;
                    height: 2.5rem;
                    top: 1.5rem;
                    right: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            
            <div class="error-box">
                <div class="error-pattern"></div>
                
                @if(isset($exception))
                    <div class="error-icon">
                        @if($exception->getStatusCode() == 404)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        @elseif($exception->getStatusCode() == 403)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @elseif($exception->getStatusCode() == 500)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        @elseif($exception->getStatusCode() == 503)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($exception->getStatusCode() == 429)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($exception->getStatusCode() == 401)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @endif
                    </div>
                @endif
                
                <div class="error-code">@yield('code')</div>
                <h1 class="error-message">@yield('message')</h1>
                
                <div class="divider"></div>
                
                <p class="error-description">
                    @if(isset($exception))
                        @if($exception->getStatusCode() == 401)
                            Anda perlu login untuk mengakses halaman ini. Silahkan login terlebih dahulu.
                        @elseif($exception->getStatusCode() == 402)
                            Pembayaran diperlukan untuk mengakses sumber daya ini.
                        @elseif($exception->getStatusCode() == 403)
                            Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika Anda merasa ini adalah kesalahan.
                        @elseif($exception->getStatusCode() == 404)
                            Halaman yang Anda cari tidak ditemukan. Mungkin telah dihapus atau URL tidak sesuai.
                        @elseif($exception->getStatusCode() == 419)
                            Sesi Anda telah berakhir. Silahkan muat ulang halaman dan coba lagi.
                        @elseif($exception->getStatusCode() == 429)
                            Anda telah membuat terlalu banyak permintaan. Mohon tunggu beberapa saat sebelum mencoba lagi.
                        @elseif($exception->getStatusCode() == 500)
                            Terjadi kesalahan pada server. Tim kami sedang mengatasi masalah ini.
                        @elseif($exception->getStatusCode() == 503)
                            Server sedang dalam pemeliharaan. Silahkan coba lagi nanti.
                        @else
                            Terjadi kesalahan saat memproses permintaan Anda. Silahkan coba lagi atau kembali ke beranda.
                        @endif
                    @else
                        Terjadi kesalahan saat memproses permintaan Anda. Silahkan coba lagi atau kembali ke beranda.
                    @endif
                </p>
                <a href="/" class="action-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </body>
</html>


