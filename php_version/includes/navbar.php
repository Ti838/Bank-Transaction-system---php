<nav class="glass-light border-b border-primary-500/20 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
        <?php
        $in_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/staff/') !== false || strpos($_SERVER['PHP_SELF'], '/customer/') !== false);
        $prefix = $in_subfolder ? '../' : '';
        ?>
        <a href="<?php echo $prefix; ?>index.php"
            class="flex items-center space-x-3 hover:opacity-80 transition-opacity cursor-pointer">
            <img src="<?php echo $prefix; ?>static/favicon.png" alt="Trust Mora Bank Logo"
                class="w-8 h-8 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.3)]">
            <span class="text-xl font-bold gradient-text">Trust Mora Bank</span>
        </a>

        <button id="mobile-menu-toggle" aria-label="Toggle Mobile Menu" class="lg:hidden text-white focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>

        <div id="nav-links" class="hidden lg:flex items-center space-x-6">

            <!-- Theme Toggle -->
            <button id="theme-toggle" aria-label="Toggle Dark/Light Theme"
                class="p-2 rounded-full hover:bg-white/10 transition-all text-gray-400 hover:text-primary-400">
                <i class="fas fa-moon text-lg translate-y-[-1px]"></i>
            </button>

            <?php if (is_logged_in()): ?>
                <div class="flex items-center space-x-6 mr-6 border-r border-white/10 pr-6">
                    <?php if ($_SESSION['role'] === 'Admin'): ?>
                        <a href="<?php echo $prefix; ?>admin/dashboard.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>">Nexus</a>
                        <a href="<?php echo $prefix; ?>admin/users.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'users.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>">Entities</a>
                        <a href="<?php echo $prefix; ?>admin/reports.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'reports.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>">Intelligence</a>
                        <a href="<?php echo $prefix; ?>admin/settings.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'settings.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>"><i
                                class="fas fa-cog"></i></a>
                    <?php elseif ($_SESSION['role'] === 'Staff'): ?>
                        <a href="<?php echo $prefix; ?>staff/dashboard.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>">Terminal</a>
                        <a href="<?php echo $prefix; ?>staff/assist.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'assist.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>">Ops
                            Assist</a>
                        <a href="<?php echo $prefix; ?>staff/reports.php"
                            class="text-[10px] font-black uppercase tracking-widest hover:text-primary-400 <?php echo strpos($_SERVER['PHP_SELF'], 'reports.php') !== false ? 'text-primary-400' : 'text-gray-400'; ?>">Logs</a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest leading-none mb-1">
                            <?php echo $_SESSION['role']; ?>
                        </p>
                        <p class="text-xs font-bold text-white"><?php echo $_SESSION['full_name']; ?></p>
                    </div>
                    <a href="<?php echo $prefix; ?>customer/profile.php" class="relative group">
                        <img src="<?php echo $prefix; ?>static/default_avatar.png" alt="User Avatar"
                            class="w-10 h-10 rounded-full border-2 border-primary-500/30 group-hover:border-primary-500 transition-all">
                        <div
                            class="absolute -bottom-1 -right-1 w-3 h-3 bg-success-500 border-2 border-[#0f172a] rounded-full">
                        </div>
                    </a>
                </div>
                <a href="<?php echo $prefix; ?>logout.php"
                    class="ml-4 bg-red-500/20 text-red-400 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-500/30 hover:bg-red-500/30 transition-all">Logout</a>
            <?php else: ?>
                <a href="<?php echo $prefix; ?>login.php" class="text-sm font-bold hover:text-primary-400">Sign In</a>
                <a href="<?php echo $prefix; ?>signup.php"
                    class="bg-primary-600 px-6 py-2 rounded-full text-xs font-bold hover:shadow-lg hover:shadow-primary-500/50 transition-all text-white uppercase tracking-widest">Join
                    Bank</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden glass-light border-t border-primary-500/10 px-6 py-8 space-y-4">
        <?php if (is_logged_in()): ?>
            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <a href="<?php echo $prefix; ?>admin/dashboard.php" class="block text-sm hover:text-primary-400">Admin Panel</a>
            <?php elseif ($_SESSION['role'] === 'Staff'): ?>
                <a href="<?php echo $prefix; ?>staff/dashboard.php" class="block text-sm hover:text-primary-400">Staff
                    Portal</a>
            <?php else: ?>
                <a href="<?php echo $prefix; ?>customer/dashboard.php"
                    class="block text-sm hover:text-primary-400">Dashboard</a>
                <a href="<?php echo $prefix; ?>customer/transactions.php"
                    class="block text-sm hover:text-primary-400">History</a>
                <a href="<?php echo $prefix; ?>customer/profile.php" class="block text-sm hover:text-primary-400">Profile</a>
            <?php endif; ?>
            <a href="<?php echo $prefix; ?>logout.php" class="block text-sm text-red-400">Logout</a>
        <?php else: ?>
            <a href="<?php echo $prefix; ?>login.php" class="block text-sm hover:text-primary-400">Sign In</a>
            <a href="<?php echo $prefix; ?>signup.php" class="block text-sm text-primary-400 font-bold">Open Account</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-toggle').addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    // Theme Toggle Logic
    const themeBtn = document.getElementById('theme-toggle');
    const icon = themeBtn.querySelector('i');

    themeBtn.addEventListener('click', () => {
        document.body.classList.toggle('light-theme');
        if (document.body.classList.contains('light-theme')) {
            icon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'light');
        } else {
            icon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Load saved theme
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-theme');
        icon.classList.replace('fa-moon', 'fa-sun');
    }
</script>