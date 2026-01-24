<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title ?? 'Trust Mora Bank'; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
    <link rel="icon" type="image/png"
        href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/staff/') !== false || strpos($_SERVER['PHP_SELF'], '/customer/') !== false) ? '../static/favicon.png' : 'static/favicon.png'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 500: '#3b82f6' },
                        success: { 600: '#059669' },
                        gold: { 400: '#fbbf24', 500: '#f59e0b' }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-light {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-image: linear-gradient(to right, #60a5fa, #34d399);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            overflow-x: hidden;
            position: relative;
        }

        @keyframes pulse-soft {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 0.7;
            }
        }

        .lively-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }

        .lively-glow:hover {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.4);
        }

        .dynamic-money {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Light Theme overrides */
        body.light-theme {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f8fafc 100%);
            color: #1e293b;
        }

        body.light-theme .glass-light {
            background: rgba(255, 255, 255, 0.8) !important;
            border: 1px solid rgba(59, 130, 246, 0.2) !important;
            color: #1e293b !important;
        }

        body.light-theme .text-gray-400,
        body.light-theme .text-gray-300,
        body.light-theme .text-gray-500 {
            color: #475569 !important;
        }

        body.light-theme .text-white {
            color: #0f172a !important;
        }

        body.light-theme .bg-slate-900\/50,
        body.light-theme .bg-slate-900,
        body.light-theme .bg-slate-900\/80,
        body.light-theme .bg-slate-900\/40,
        body.light-theme .bg-slate-950\/50,
        body.light-theme .bg-slate-950 {
            background: rgba(255, 255, 255, 0.7) !important;
            color: #0f172a !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }

        body.light-theme .text-indigo-200 {
            color: #4f46e5 !important;
            /* Indigo-600 */
            opacity: 1 !important;
        }

        body.light-theme .bg-gradient-to-br {
            /* Adjust gradients if needed, or keep them as accent headers */
        }

        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            header,
            nav,
            footer,
            .no-print,
            button,
            #mobile-menu-toggle,
            #theme-toggle {
                display: none !important;
            }

            .glass-light {
                background: transparent !important;
                border: 1px solid #ddd !important;
                backdrop-filter: none !important;
                color: black !important;
            }

            .gradient-text {
                background: none !important;
                color: black !important;
                -webkit-text-fill-color: initial !important;
            }

            .max-w-7xl {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>

<body class="text-gray-100 min-h-screen flex flex-col">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="fixed top-24 right-6 z-[100] animate-bounce">
            <div
                class="p-4 rounded-xl glass-light border <?php echo $_SESSION['flash']['type'] === 'success' ? 'border-success-500/50 text-success-400' : 'border-red-500/50 text-red-400'; ?> shadow-2xl">
                <p class="text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                    <i
                        class="fas <?php echo $_SESSION['flash']['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <?php echo $_SESSION['flash']['message']; ?>
                </p>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>