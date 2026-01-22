<footer class="glass-light text-gray-400 py-8 px-6 mt-auto border-t border-white/10">
    <div class="max-w-7xl mx-auto flex flex-col items-center space-y-4">
        <?php
        $in_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/staff/') !== false || strpos($_SERVER['PHP_SELF'], '/customer/') !== false);
        $prefix = $in_subfolder ? '../' : '';
        ?>
        <div class="flex items-center space-x-2">
            <img src="<?php echo $prefix; ?>static/favicon.png" class="w-8 h-8 rounded shadow-lg" alt="Logo">
            <span class="text-xl font-bold text-white tracking-tight">Trust Mora Bank PLC</span>
        </div>

        <div class="text-center space-y-2">
            <p class="text-sm font-medium">&copy; <?php echo date('Y'); ?> Trust Mora Bank PLC. All Rights Reserved.</p>
            <p class="text-xs text-gray-500 uppercase tracking-widest">Developed by <span
                    class="text-primary-400 font-bold">Timon Biswas</span></p>
        </div>

        <div class="max-w-2xl text-center">
            <p class="text-[10px] leading-relaxed opacity-50 uppercase tracking-wider">
                All content, code, and design are proprietary. Unauthorized reproduction, modification, or distribution
                is strictly prohibited and subject to legal action. Member of Dhaka Clearing House.
            </p>
        </div>
    </div>
</footer>
</body>

</html>