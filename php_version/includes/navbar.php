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


            <!-- Widget: Dark Mode Toggle -->
            <button id="theme-toggle" aria-label="Toggle Dark/Light Theme"
                class="p-2 rounded-full hover:bg-white/10 transition-all text-gray-400 hover:text-primary-400">
                <i class="fas fa-moon text-lg translate-y-[-1px]"></i>
            </button>

            <?php if (is_logged_in()): ?>

                <!-- Widget: Currency Converter (BDT <-> USD) -->
                <button id="currency-toggle" aria-label="Toggle Currency"
                    class="p-2 rounded-full hover:bg-white/10 transition-all text-gray-400 hover:text-success-400 font-bold text-sm h-10 w-10 flex items-center justify-center border border-transparent hover:border-white/10">
                    ৳
                </button>

                <div class="relative group" id="notification-wrapper">
                    <?php
                    $unread_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_status = 0");
                    $unread_stmt->execute([$_SESSION['user_id']]);
                    $unread_count = $unread_stmt->fetchColumn();
                    ?>
                    <button id="notification-toggle"
                        class="p-2 rounded-full hover:bg-white/10 transition-all text-gray-400 hover:text-primary-400 relative">
                        <i class="fas fa-bell text-lg"></i>
                        <?php if ($unread_count > 0): ?>
                            <span
                                class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-[#0f172a] rounded-full animate-pulse"></span>
                        <?php endif; ?>
                    </button>


                    <div id="notification-dropdown"
                        class="hidden absolute right-0 mt-4 w-80 glass-light border border-primary-500/20 rounded-2xl shadow-2xl py-4 z-[100] animate-fade-in">
                        <div class="px-6 pb-3 border-b border-white/10 flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Alert Stream</span>
                            <?php if ($unread_count > 0): ?>
                                <span
                                    class="bg-primary-500/20 text-primary-400 text-[8px] px-2 py-0.5 rounded-full font-black uppercase"><?php echo $unread_count; ?>
                                    New</span>
                            <?php endif; ?>
                        </div>
                        <div class="max-h-64 overflow-y-auto custom-scrollbar">
                            <?php
                            $notif_stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                            $notif_stmt->execute([$_SESSION['user_id']]);
                            $nav_notifs = $notif_stmt->fetchAll();

                            if ($nav_notifs):
                                foreach ($nav_notifs as $nn):
                                    ?>
                                    <div class="px-6 py-4 hover:bg-white/5 transition-colors border-b border-white/5 last:border-0">
                                        <p class="text-[11px] text-white leading-relaxed mb-1"><?php echo $nn['message']; ?></p>
                                        <p class="text-[8px] text-gray-500 font-bold uppercase">
                                            <?php echo date('d M, H:i', strtotime($nn['created_at'])); ?>
                                        </p>
                                    </div>
                                    <?php
                                endforeach;
                            else:
                                ?>
                                <div class="px-6 py-8 text-center">
                                    <i class="fas fa-shield-blank text-gray-800 text-2xl mb-2"></i>
                                    <p class="text-[10px] text-gray-600 font-black uppercase">No Data Pulses</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="px-6 pt-3 border-t border-white/10 text-center">
                            <a href="<?php echo $prefix; ?>customer/dashboard.php"
                                class="text-[8px] font-black text-primary-400 uppercase tracking-widest hover:text-white transition-colors">Neural
                                Interface Access</a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-6 mr-6 border-r border-white/10 pr-6 ml-2">
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
                        <img src="<?php echo $prefix; ?>static/uploads/profiles/<?php echo !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_avatar.png'; ?>"
                            alt="User Avatar"
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


    // Event Listener: Notification Dropdown
    const notifToggle = document.getElementById('notification-toggle');
    const notifDropdown = document.getElementById('notification-dropdown');


    if (notifToggle && notifDropdown) {
        notifToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!notifDropdown.contains(e.target) && e.target !== notifToggle) {
                notifDropdown.classList.add('hidden');
            }
        });
    }


    // Event Listener: Theme Switching (Persisted in LocalStorage)
    const themeToggle = document.getElementById('theme-toggle');


    if (themeToggle) {
        const icon = themeToggle.querySelector('i');


        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-theme');
            if (icon) icon.classList.replace('fa-moon', 'fa-sun');
        }


        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-theme');

            if (document.body.classList.contains('light-theme')) {
                localStorage.setItem('theme', 'light');
                if (icon) icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                localStorage.setItem('theme', 'dark');
                if (icon) icon.classList.replace('fa-sun', 'fa-moon');
            }
        });
    }


    // System: Live Currency Conversion
    const currencyToggle = document.getElementById('currency-toggle');
    const exchangeRate = 0.0090; // Fixed Rate 1 BDT = 0.0090 USD


    if (currencyToggle) {

        let currentCurrency = localStorage.getItem('currency') || 'BDT';

        function updateCurrencyDisplay() {
            currencyToggle.innerText = currentCurrency === 'BDT' ? '৳' : '$';



            const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
            let node;





















            if (currentCurrency === 'USD') {
                convertAllToUSD();
                currencyToggle.classList.add('text-green-400');
                currencyToggle.classList.remove('text-success-400');
            } else {


                location.reload();
            }
        }

        // Helper: Convert all '৳' prices to USD in DOM
        function convertAllToUSD() {

            const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
            let node;
            while (node = walker.nextNode()) {
                const text = node.nodeValue.trim();

                if (text.includes('৳')) {
                    // Remove symbol and commas
                    const val = parseFloat(numericPart);
                    if (!isNaN(val)) {

                        if (!node.parentNode.dataset.originalBdt) {
                            node.parentNode.dataset.originalBdt = val;
                        }


                        const usdVal = (val * exchangeRate).toFixed(2);
                        node.nodeValue = text.replace(/৳\s*[\d,]+\.?\d*/, '$ ' + usdVal);
                    }
                }
            }
        }


        if (currentCurrency === 'USD') {


            setTimeout(updateCurrencyDisplay, 100);
        } else {
            currencyToggle.innerText = '৳';
        }

        currencyToggle.addEventListener('click', () => {
            if (currentCurrency === 'BDT') {
                currentCurrency = 'USD';
                localStorage.setItem('currency', 'USD');
                updateCurrencyDisplay();
            } else {
                currentCurrency = 'BDT';
                localStorage.setItem('currency', 'BDT');
                location.reload();
            }
        });
    }
</script>