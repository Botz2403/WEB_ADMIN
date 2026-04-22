<?php
$pageTitle = "Báo Cáo Doanh Thu";
$activePage = "reports";

function fmtVND($n) { return number_format($n, 0, ',', '.') . '₫'; }

$summary = [
    "total_revenue"   => 387_200_000,
    "total_orders"    => 1_243,
    "avg_order_value" => 311_504,
    "total_customers" => 628,
];
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">📈 Báo Cáo Doanh Thu</h1>
            <p class="text-sm text-gray-500">Phân tích hiệu suất kinh doanh</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Date range -->
            <div class="flex items-center gap-2 border border-gray-300 rounded-xl px-4 py-2">
                <span class="text-sm text-gray-500">📅</span>
                <input type="date" value="2026-04-01" class="text-sm focus:outline-none">
                <span class="text-gray-400 mx-1">→</span>
                <input type="date" value="2026-04-15" class="text-sm focus:outline-none">
            </div>
            <select class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none">
                <option>Tất cả chi nhánh</option>
                <option>Chi nhánh Q.1</option>
                <option>Chi nhánh Q.7</option>
            </select>
            <button class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold flex items-center gap-2 hover:opacity-90 transition">
                📥 Export CSV
            </button>
            <button class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                🖨️ In PDF
            </button>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- Summary KPIs -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <?php $cards = [
                ["Tổng Doanh Thu",  fmtVND($summary['total_revenue']),              "+23% so kỳ trước", "💰", "from-orange-500 to-orange-700"],
                ["Tổng Đơn Hàng",  $summary['total_orders']." đơn",                "+15% so kỳ trước", "📋", "from-blue-600 to-blue-800"],
                ["Giá Trị TB/Đơn", fmtVND($summary['avg_order_value']),             "+7% so kỳ trước",  "📊", "from-green-600 to-green-800"],
                ["Lượt Khách",     $summary['total_customers']." người",            "+19% so kỳ trước", "👥", "from-purple-600 to-purple-900"],
            ];
            foreach ($cards as [$title, $value, $change, $icon, $gradient]): ?>
            <div class="rounded-2xl p-6 text-white bg-gradient-to-br <?= $gradient ?> relative overflow-hidden">
                <p class="text-white/80 text-sm font-medium"><?= $title ?></p>
                <p class="text-2xl font-extrabold mt-1"><?= $value ?></p>
                <p class="text-white/70 text-xs mt-1"><?= $change ?></p>
                <div class="text-5xl absolute right-3 bottom-3 opacity-15"><?= $icon ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Revenue Chart + Category Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Doanh Thu Theo Ngày</h2>
                    <div class="flex gap-2 text-sm">
                        <button class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-lg font-medium">Ngày</button>
                        <button class="px-3 py-1.5 text-gray-500 hover:bg-gray-100 rounded-lg">Tuần</button>
                        <button class="px-3 py-1.5 text-gray-500 hover:bg-gray-100 rounded-lg">Tháng</button>
                    </div>
                </div>
                <canvas id="dailyRevChart" height="120"></canvas>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Phân Bổ Danh Mục</h2>
                <canvas id="catDonut" height="160"></canvas>
                <div class="mt-4 space-y-2 text-sm">
                    <?php $cats = [
                        ["Món chính",   "#FF6F00", "58%"],
                        ["Đồ uống",     "#1976D2", "22%"],
                        ["Khai vị",     "#2E7D32", "12%"],
                        ["Tráng miệng", "#FBC02D",  "8%"],
                    ];
                    foreach ($cats as [$l, $c, $p]): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background:<?= $c ?>"></span>
                            <span class="text-gray-600"><?= $l ?></span>
                        </div>
                        <span class="font-semibold text-gray-900"><?= $p ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Top Items Performance Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-900">Hiệu Suất Từng Món</h2>
                <button class="text-sm text-orange-600 font-medium hover:underline">Xuất Excel →</button>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Món</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Số Lượng Bán</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Doanh Thu</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">% Tổng</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Xu Hướng</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $items2 = [
                        [1, "Phở Bò Đặc Biệt", 156, 10_140_000, 26.2, "+12%", true],
                        [2, "Cà Phê Sữa Đá",    203,  5_075_000, 13.1,  "+8%", true],
                        [3, "Cơm Tấm Sườn",      98,  7_350_000, 19.0,  "+5%", true],
                        [4, "Bún Bò Huế",         87,  5_046_000, 13.0,  "-2%", false],
                        [5, "Chả Giò Rán",        72,  3_240_000,  8.4, "+15%", true],
                    ];
                    foreach ($items2 as [$rank, $name, $qty, $rev, $pct, $trend, $isUp]): ?>
                    <tr class="trow">
                        <td class="px-6 py-3">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold text-gray-700
                                <?= $rank==1?'bg-yellow-400':($rank==2?'bg-gray-300':($rank==3?'bg-orange-300':'bg-gray-100')) ?>">
                                <?= $rank ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 font-semibold text-gray-900 text-sm"><?= $name ?></td>
                        <td class="px-6 py-3 text-center text-gray-600 text-sm"><?= $qty ?> phần</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-900 text-sm"><?= fmtVND($rev) ?></td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-20 bg-gray-100 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full" style="width:<?= $pct ?>%"></div>
                                </div>
                                <span class="text-sm text-gray-600"><?= $pct ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-sm font-bold <?= $isUp ? 'text-green-600' : 'text-red-500' ?>">
                                <?= $isUp ? '↑' : '↓' ?> <?= $trend ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<script>
// Daily Revenue Bar Chart
new Chart(document.getElementById('dailyRevChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['01/4','02/4','03/4','04/4','05/4','06/4','07/4','08/4','09/4','10/4','11/4','12/4','13/4','14/4','15/4'],
        datasets: [
            {
                label: 'Tại bàn',
                data: [7200,8100,6500,9200,8800,12000,11500,7800,8500,9800,10200,11800,9500,10500,8900],
                backgroundColor: 'rgba(255,111,0,0.75)', borderRadius: 4
            },
            {
                label: 'Online',
                data: [1800,2100,1500,2800,2200,3200,3000,1900,2100,2700,2800,3100,2600,2900,2300],
                backgroundColor: 'rgba(25,118,210,0.75)', borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { stacked: true, grid: { display: false } },
            y: { stacked: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => (v/1000).toFixed(0)+'k' } }
        }
    }
});

// Category Donut
new Chart(document.getElementById('catDonut').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Món chính','Đồ uống','Khai vị','Tráng miệng'],
        datasets: [{
            data: [58, 22, 12, 8],
            backgroundColor: ['#FF6F00','#1976D2','#2E7D32','#FBC02D'],
            borderWidth: 0
        }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { display: false } } }
});
</script>

</body>
</html>
