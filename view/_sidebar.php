<?php
// _sidebar.php — shared sidebar partial
// Requires: $activePage (string)
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gray-900 flex flex-col z-50 transition-transform duration-300">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10 flex-shrink-0">
        <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white text-xl">🍽</div>
        <div>
            <div class="text-white font-extrabold text-lg leading-tight">Gourmet Hub</div>
            <div class="text-gray-400 text-xs">Admin Panel v1.0</div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <p class="text-gray-500 text-xs uppercase tracking-wider px-4 mb-2">Tổng quan</p>
        <a href="index.php?act=Dashboard" class="sidebar-link <?= $activePage==='dashboard'?'active':'' ?>">
            <span class="text-xl">📊</span> Dashboard
        </a>

        <p class="text-gray-500 text-xs uppercase tracking-wider px-4 mt-4 mb-2">Vận hành</p>
        <a href="index.php?act=order" class="sidebar-link <?= $activePage==='orders'?'active':'' ?>">
            <span class="text-xl">📋</span> Đơn Hàng
        </a>
        <a href="index.php?act=tables" class="sidebar-link <?= $activePage==='tables'?'active':'' ?>">
            <span class="text-xl">🪑</span> Quản Lý Bàn
        </a>
        <a href="index.php?act=menu" class="sidebar-link <?= $activePage==='menu'?'active':'' ?>">
            <span class="text-xl">🍜</span> Quản Lý Menu
        </a>

        <p class="text-gray-500 text-xs uppercase tracking-wider px-4 mt-4 mb-2">Kho hàng</p>
        <a href="index.php?act=inventory" class="sidebar-link <?= $activePage==='inventory'?'active':'' ?>">
            <span class="text-xl">📦</span> Kho Nguyên Liệu
        </a>
        <a href="index.php?act=dinh_muc" class="sidebar-link <?= $activePage==='dinh_muc'?'active':'' ?>">
            <span class="text-xl">📋</span> Định Mức Món Ăn
        </a>

        <p class="text-gray-500 text-xs uppercase tracking-wider px-4 mt-4 mb-2">Phân tích</p>
        <a href="index.php?act=reports" class="sidebar-link <?= $activePage==='reports'?'active':'' ?>">
            <span class="text-xl">📈</span> Báo Cáo
        </a>

        <p class="text-gray-500 text-xs uppercase tracking-wider px-4 mt-4 mb-2">Quản lý</p>
        <a href="index.php?act=customers" class="sidebar-link <?= $activePage==='customers'?'active':'' ?>">
            <span class="text-xl">🧑‍🤝‍🧑</span> Khách Hàng
        </a>
        <a href="index.php?act=staff" class="sidebar-link <?= $activePage==='staff'?'active':'' ?>">
            <span class="text-xl">👥</span> Nhân Viên
        </a>
        <a href="index.php?act=marketing" class="sidebar-link <?= $activePage==='marketing'?'active':'' ?>">
            <span class="text-xl">🎯</span> Marketing
        </a>
        <a href="index.php?act=settings" class="sidebar-link <?= $activePage==='settings'?'active':'' ?>">
            <span class="text-xl">⚙️</span> Cài Đặt
        </a>
    </nav>

    <!-- User -->
    <div class="px-4 py-4 border-t border-white/10 flex-shrink-0">
        <div class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white/10 cursor-pointer transition">
            <div class="w-9 h-9 rounded-full gradient-primary flex items-center justify-center text-white font-bold text-sm">QL</div>
            <div class="flex-1 min-w-0">
                <div class="text-white text-sm font-semibold truncate">Quản Lý</div>
                <div class="text-gray-400 text-xs truncate">admin@gourmet.vn</div>
            </div>
            <span class="text-gray-500 text-xs">⋯</span>
        </div>
    </div>
</aside>
