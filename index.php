<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'taruna') {
        header("Location: taruna_dashboard.php");
    } else if ($_SESSION['role'] == 'pembina') {
        header("Location: pembina_dashboard.php");
    } else if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'petugas') {
        header("Location: admin_dashboard.php");
    } else {
        session_destroy();
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELTAR - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#0f172a',
                            900: '#0b1120',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-white">

    <div class="glass-panel rounded-2xl shadow-2xl w-full max-w-md p-8 relative overflow-hidden">
        <!-- Decorative Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-32 bg-blue-500 rounded-full blur-3xl opacity-20"></div>

        <div class="text-center mb-8 relative z-10">
            <h1 class="text-3xl font-bold tracking-wider text-blue-400 mb-2">SIPELTAR</h1>
            <p class="text-gray-400 text-sm uppercase tracking-widest">Sistem Pendataan Elektronik Taruna</p>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded mb-6 text-sm">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST" class="space-y-6 relative z-10">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username / NIT</label>
                <input type="text" id="username" name="username" required 
                    class="w-full bg-navy-900/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Masukkan Username atau NIT">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full bg-navy-900/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                LOGIN SYSTEM
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-gray-500 relative z-10">
            <p>&copy; <?php echo date('Y'); ?> SIPELTAR Academy Monitoring. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
