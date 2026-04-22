<?php
$pageTitle = "Dashboard";
$activePage = "dashboard";

// Mock data — Replace with DB queries in production
$kpis = [
    "revenue_today"    => 12_450_000,
    "revenue_month"    => 387_200_000,
    "orders_today"     => 47,
    "orders_online"    => 18,
    "tables_occupied"  => 8,
    "tables_total"     => 12,
    "new_bookings"     => 5,
    "low_stock_alerts" => 3,
];

$recentOrders = [
    ["id"=>"O1042","table"=>"Bàn 01",  "items"=>3,"total"=>185000,"status"=>"PREPARING", "time"=>"18:45"],
    ["id"=>"O1041","table"=>"Bàn 06",  "items"=>5,"total"=>320000,"status"=>"COMPLETED", "time"=>"18:30"],
    ["id"=>"O1040","table"=>"Online",  "items"=>2,"total"=>130000,"status"=>"DELIVERING","time"=>"18:15"],
    ["id"=>"O1039","table"=>"VIP 01",  "items"=>8,"total"=>760000,"status"=>"COMPLETED", "time"=>"18:00"],
    ["id"=>"O1038","table"=>"Bàn 03",  "items"=>4,"total"=>245000,"status"=>"PAID",      "time"=>"17:50"],
];

$topItems = [
    ["name"=>"Phở Bò Đặc Biệt","qty"=>38,"revenue"=>2_470_000,"trend"=>"+12%"],
    ["name"=>"Cà Phê Sữa Đá",  "qty"=>56,"revenue"=>1_400_000,"trend"=>"+8%"],
    ["name"=>"Cơm Tấm Sườn",   "qty"=>27,"revenue"=>2_025_000,"trend"=>"+5%"],
    ["name"=>"Bún Bò Huế",     "qty"=>24,"revenue"=>1_392_000,"trend"=>"-2%"],
    ["name"=>"Chả Giò Rán",    "qty"=>19,"revenue"=>855_000,  "trend"=>"+15%"],
];
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{ sidebarOpen: true }">

<?php include '_sidebar.php'; ?>

<!-- ═══════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════ -->
<main class="ml-64 min-h-screen">

    <!-- Top Bar -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button onclick="document.getElementById('sidebar').classList.toggle('open')"
                    class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500">Thứ Ba, 15/04/2026 — Chi nhánh Q.1</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- Live indicator -->
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-3 py-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-semibold text-green-700">Live</span>
            </div>
            <!-- Bell -->
            <div class="relative">
                <button class="relative p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                </button>
            </div>
            <a href="orders.php" class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                + Thêm Đơn Hàng
            </a>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- ── KPI CARDS ─────────────────────────────────────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Revenue Today -->
            <div class="kpi-card gradient-primary">
                <p class="text-orange-100 text-sm font-medium">Doanh Thu Hôm Nay</p>
                <p class="text-3xl font-extrabold mt-1"><?= number_format($kpis['revenue_today'], 0, ',', '.') ?>₫</p>
                <p class="text-orange-200 text-sm mt-2">+18% so với hôm qua</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">💰</div>
            </div>

            <!-- Orders -->
            <div class="kpi-card gradient-blue">
                <p class="text-blue-100 text-sm font-medium">Đơn Hàng Hôm Nay</p>
                <p class="text-3xl font-extrabold mt-1"><?= $kpis['orders_today'] ?></p>
                <p class="text-blue-200 text-sm mt-2"><?= $kpis['orders_online'] ?> đơn online</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">📋</div>
            </div>

            <!-- Tables -->
            <div class="kpi-card gradient-green">
                <p class="text-green-100 text-sm font-medium">Bàn Đang Phục Vụ</p>
                <p class="text-3xl font-extrabold mt-1"><?= $kpis['tables_occupied'] ?>/<?= $kpis['tables_total'] ?></p>
                <p class="text-green-200 text-sm mt-2"><?= $kpis['new_bookings'] ?> đặt bàn mới</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">🪑</div>
            </div>

            <!-- Alerts -->
            <div class="kpi-card gradient-purple">
                <p class="text-purple-100 text-sm font-medium">Cảnh Báo Tồn Kho</p>
                <p class="text-3xl font-extrabold mt-1"><?= $kpis['low_stock_alerts'] ?> nguyên liệu</p>
                <p class="text-purple-200 text-sm mt-2">Cần đặt hàng bổ sung</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">⚠️</div>
            </div>
        </div>

        <!-- ── CHARTS + TOP ITEMS ─────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Revenue Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Doanh Thu Tuần Này</h2>
                    <div class="flex gap-2">
                        <button id="btn-week"  onclick="setRange('week')"  class="px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-sm font-medium">Tuần</button>
                        <button id="btn-month" onclick="setRange('month')" class="px-3 py-1 text-gray-500 rounded-lg text-sm hover:bg-gray-100">Tháng</button>
                        <button id="btn-year"  onclick="setRange('year')"  class="px-3 py-1 text-gray-500 rounded-lg text-sm hover:bg-gray-100">Năm</button>
                    </div>
                </div>
                <canvas id="revenueChart" height="100"></canvas>
            </div>

            <!-- Top Items -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">🔥 Món Bán Chạy</h2>
                <div class="space-y-3">
                    <?php foreach ($topItems as $i => $item): ?>
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold text-gray-700
                            <?= $i===0 ? 'bg-yellow-400' : ($i===1 ? 'bg-gray-300' : ($i===2 ? 'bg-orange-300' : 'bg-gray-100')) ?>">
                            <?= $i + 1 ?>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate"><?= $item['name'] ?></p>
                            <p class="text-xs text-gray-500"><?= $item['qty'] ?> phần · <?= number_format($item['revenue'], 0, '.', '.') ?>₫</p>
                        </div>
                        <span class="text-xs font-bold <?= str_starts_with($item['trend'], '+') ? 'text-green-600' : 'text-red-500' ?>">
                            <?= $item['trend'] ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── RECENT ORDERS + ORDER DISTRIBUTION ────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Recent Orders -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Đơn Gần Đây</h2>
                    <a href="orders.php" class="text-orange-600 text-sm font-semibold hover:underline">Xem tất cả →</a>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã Đơn</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vị Trí</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Món</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Giờ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($recentOrders as $order):
                            $statusMap = [
                                "PREPARING"  => ["bg-blue-100 text-blue-700",    "Đang nấu"],
                                "COMPLETED"  => ["bg-green-100 text-green-700",  "Hoàn thành"],
                                "DELIVERING" => ["bg-purple-100 text-purple-700","Đang giao"],
                                "PAID"       => ["bg-gray-100 text-gray-600",    "Đã thanh toán"],
                            ];
                            [$badgeClass, $statusLabel] = $statusMap[$order['status']] ?? ["bg-gray-100 text-gray-600", $order['status']];
                        ?>
                        <tr class="trow">
                            <td class="px-6 py-4 font-semibold text-gray-900 text-sm">#<?= $order['id'] ?></td>
                            <td class="px-6 py-4 text-gray-700 text-sm"><?= $order['table'] ?></td>
                            <td class="px-6 py-4 text-center text-gray-600 text-sm"><?= $order['items'] ?> món</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm"><?= number_format($order['total'], 0, '.', '.') ?>₫</td>
                            <td class="px-6 py-4 text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500 text-sm"><?= $order['time'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Distribution Donut -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Phân Bổ Đơn</h2>
                <canvas id="orderDonut" height="160"></canvas>
                <div class="mt-4 space-y-2">
                    <?php $channels = [
                        ["Tại bàn",    "#FF6F00", 62],
                        ["Online",     "#1976D2", 25],
                        ["Điện thoại", "#2E7D32",  9],
                        ["Khác",       "#9E9E9E",  4],
                    ];
                    foreach ($channels as [$label, $color, $pct]): ?>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background:<?= $color ?>"></span>
                            <span class="text-gray-600"><?= $label ?></span>
                        </div>
                        <span class="font-semibold text-gray-900"><?= $pct ?>%</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── Low Stock Alerts ───────────────────────────────── -->
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-xl">⚠️</span>
                <h2 class="text-lg font-bold text-red-600">Cảnh Báo Tồn Kho Thấp</h2>
                <span class="ml-auto text-sm text-gray-500"><?= date('H:i d/m/Y') ?></span>
                <a href="inventory.php" class="text-sm text-orange-600 font-semibold hover:underline ml-3">Xem kho →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php $lowStock = [
                    ["Thịt heo",  "kg",  2.0, 5.0],
                    ["Rau thơm",  "kg",  0.5, 2.0],
                    ["Nước mắm",  "lít", 1.2, 3.0],
                ];
                foreach ($lowStock as [$name, $unit, $qty, $threshold]): ?>
                <div class="border border-red-200 rounded-xl p-4 bg-red-50">
                    <div class="flex justify-between items-start">
                        <p class="font-semibold text-gray-900"><?= $name ?></p>
                        <span class="badge bg-red-100 text-red-700">Thấp</span>
                    </div>
                    <p class="text-2xl font-extrabold text-red-600 mt-1"><?= $qty ?> <?= $unit ?></p>
                    <p class="text-xs text-gray-500">Ngưỡng tối thiểu: <?= $threshold ?> <?= $unit ?></p>
                    <div class="mt-2 bg-red-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width:<?= round($qty / $threshold * 100) ?>%"></div>
                    </div>
                    <button class="mt-3 w-full py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                        Tạo PO ngay
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div><!-- /p-6 -->
</main>

<!-- ═══════════════════════════════════════════════════════
     CHARTS
═══════════════════════════════════════════════════════ -->
<script>
// ── Revenue Chart ──────────────────────────────────────
const revenueData = {
    week:  { labels: ['T2','T3','T4','T5','T6','T7','CN'], data: [8200, 9400, 7800, 11200, 10500, 14800, 12450] },
    month: { labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12','T13','T14','T15'],
             data: [6800,7200,8100,9300,8800,10200,11500,9800,10500,12000,11200,13500,9100,10800,12450] },
    year:  { labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
             data: [280000,310000,295000,387200,null,null,null,null,null,null,null,null] },
};

const ctx1 = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: revenueData.week.labels,
        datasets: [{
            label: 'Doanh thu (nghìn ₫)',
            data: revenueData.week.data,
            borderColor: '#FF6F00',
            backgroundColor: 'rgba(255,111,0,0.08)',
            borderWidth: 2.5, fill: true,
            tension: 0.4, pointRadius: 5,
            pointBackgroundColor: '#FF6F00'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: v => (v/1000).toFixed(0)+'k' }
            },
            x: { grid: { display: false } }
        }
    }
});

function setRange(range) {
    revenueChart.data.labels   = revenueData[range].labels;
    revenueChart.data.datasets[0].data = revenueData[range].data;
    revenueChart.update();
    // Update button styles
    ['week','month','year'].forEach(r => {
        const btn = document.getElementById('btn-' + r);
        btn.className = r === range
            ? 'px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-sm font-medium'
            : 'px-3 py-1 text-gray-500 rounded-lg text-sm hover:bg-gray-100';
    });
}

// ── Order Donut ────────────────────────────────────────
const ctx2 = document.getElementById('orderDonut').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Tại bàn', 'Online', 'Điện thoại', 'Khác'],
        datasets: [{
            data: [62, 25, 9, 4],
            backgroundColor: ['#FF6F00','#1976D2','#2E7D32','#9E9E9E'],
            borderWidth: 0, hoverOffset: 4
        }]
    },
    options: {
        responsive: true, cutout: '65%',
        plugins: { legend: { display: false } }
    }
});
</script>

</body>
</html>
