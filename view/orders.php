<?php
$pageTitle = "Quản Lý Đơn Hàng";
$activePage = "orders";

$orders = [
    ["id"=>"O1042","customer"=>"Bàn 01","channel"=>"dine-in","items"=>3,"total"=>185000,"status"=>"PREPARING","time"=>"18:45","note"=>"Không hành"],
    ["id"=>"O1041","customer"=>"Nguyễn Văn A","channel"=>"online","items"=>5,"total"=>320000,"status"=>"COMPLETED","time"=>"18:30","note"=>""],
    ["id"=>"O1040","customer"=>"Trần Thị B","channel"=>"online","items"=>2,"total"=>130000,"status"=>"DELIVERING","time"=>"18:15","note"=>"Giao trước 19h"],
    ["id"=>"O1039","customer"=>"VIP 01","channel"=>"dine-in","items"=>8,"total"=>760000,"status"=>"COMPLETED","time"=>"18:00","note"=>""],
    ["id"=>"O1038","customer"=>"Bàn 03","channel"=>"dine-in","items"=>4,"total"=>245000,"status"=>"PAID","time"=>"17:50","note"=>""],
    ["id"=>"O1037","customer"=>"Lê Minh C","channel"=>"phone","items"=>3,"total"=>195000,"status"=>"PENDING","time"=>"17:40","note"=>"Gọi lại xác nhận"],
    ["id"=>"O1036","customer"=>"Bàn 07","channel"=>"dine-in","items"=>6,"total"=>430000,"status"=>"PREPARING","time"=>"17:35","note"=>"1 khách dị ứng hải sản"],
    ["id"=>"O1035","customer"=>"Phạm Thu D","channel"=>"online","items"=>1,"total"=>65000,"status"=>"CANCELLED","time"=>"17:20","note"=>"Hủy theo yêu cầu khách"],
];

$statusMap = [
    "PENDING"   => ["bg-yellow-100 text-yellow-700",  "Chờ xác nhận", "⏳"],
    "PREPARING" => ["bg-blue-100 text-blue-700",      "Đang nấu",     "👨‍🍳"],
    "DELIVERING"=> ["bg-purple-100 text-purple-700",  "Đang giao",    "🚗"],
    "COMPLETED" => ["bg-green-100 text-green-700",    "Hoàn thành",   "✅"],
    "PAID"      => ["bg-gray-100 text-gray-500",      "Đã thanh toán","💳"],
    "CANCELLED" => ["bg-red-100 text-red-600",        "Đã hủy",       "❌"],
];

$channelMap = [
    "dine-in" => ["bg-orange-50 text-orange-700",  "🪑 Tại bàn"],
    "online"  => ["bg-blue-50 text-blue-700",      "🌐 Online"],
    "phone"   => ["bg-green-50 text-green-700",    "📞 Điện thoại"],
];

function fmtVND2($n) { return number_format($n,0,'.','.') . '₫'; }
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    filterStatus: 'all',
    filterChannel: 'all',
    showDetail: false,
    selectedOrder: null,
    orders: []
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <!-- Top Bar -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button onclick="document.getElementById('sidebar').classList.toggle('open')" class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">📋 Quản Lý Đơn Hàng</h1>
                <p class="text-sm text-gray-500">Realtime — cập nhật lúc <?= date('H:i') ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- Live indicator -->
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-3 py-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-semibold text-green-700">Live</span>
            </div>
            <button class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                + Tạo Đơn Mới
            </button>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- KPI Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php
            $kpiOrders = [
                ["Tất cả","all",count($orders),"bg-gray-100 text-gray-700"],
                ["Chờ xác nhận","PENDING",1,"bg-yellow-100 text-yellow-700"],
                ["Đang nấu","PREPARING",2,"bg-blue-100 text-blue-700"],
                ["Đang giao","DELIVERING",1,"bg-purple-100 text-purple-700"],
                ["Hoàn thành","COMPLETED",3,"bg-green-100 text-green-700"],
                ["Đã hủy","CANCELLED",1,"bg-red-100 text-red-600"],
            ];
            foreach($kpiOrders as [$label,$key,$cnt,$cls]): ?>
            <button @click="filterStatus = '<?= $key ?>'"
                    :class="filterStatus === '<?= $key ?>' ? 'ring-2 ring-orange-400 bg-orange-50' : 'hover:shadow-md'"
                    class="bg-white rounded-2xl border border-gray-100 p-4 text-center transition-all cursor-pointer">
                <div class="text-2xl font-extrabold text-gray-900"><?= $cnt ?></div>
                <div class="text-xs font-medium text-gray-500 mt-1"><?= $label ?></div>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col md:flex-row gap-4 items-center">
            <div class="flex gap-2 flex-wrap">
                <?php foreach(["all"=>"Tất cả kênh","dine-in"=>"🪑 Tại bàn","online"=>"🌐 Online","phone"=>"📞 Điện thoại"] as $k=>$v): ?>
                <button @click="filterChannel='<?= $k ?>'"
                        :class="filterChannel==='<?= $k ?>' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all"><?= $v ?></button>
                <?php endforeach; ?>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <input type="text" placeholder="Tìm mã đơn, khách hàng..."
                       class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 w-64">
                <button class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                    📥 Export
                </button>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mã Đơn</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Khách / Kênh</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Số Món</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng Tiền</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ghi chú</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Giờ</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($orders as $order):
                        [$badgeClass, $statusLabel, $statusIcon] = $statusMap[$order['status']] ?? ["bg-gray-100 text-gray-600", $order['status'], ""];
                        [$chClass, $chLabel] = $channelMap[$order['channel']] ?? ["bg-gray-50 text-gray-600", $order['channel']];
                    ?>
                    <tr class="trow"
                        x-show="
                            (filterStatus === 'all' || filterStatus === '<?= $order['status'] ?>') &&
                            (filterChannel === 'all' || filterChannel === '<?= $order['channel'] ?>')
                        ">
                        <td class="px-6 py-4 font-bold text-gray-900 text-sm">#<?= $order['id'] ?></td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($order['customer']) ?></p>
                            <span class="badge <?= $chClass ?> mt-1"><?= $chLabel ?></span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600 text-sm"><?= $order['items'] ?> món</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm"><?= fmtVND2($order['total']) ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="badge <?= $badgeClass ?>"><?= $statusIcon ?> <?= $statusLabel ?></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            <?= $order['note'] ? htmlspecialchars($order['note']) : '<span class="text-gray-300">—</span>' ?>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-500 text-sm"><?= $order['time'] ?></td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition">👁 Chi tiết</button>
                                <?php if(in_array($order['status'],['PENDING','PREPARING'])): ?>
                                <button class="px-2.5 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-medium hover:bg-green-100 transition">▶ Tiếp theo</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Floor Map Quick View -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-900">🗺️ Sơ Đồ Bàn — Khu Chính</h2>
                <div class="flex gap-3 text-xs font-medium">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-400 inline-block"></span>Trống</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-500 inline-block"></span>Đang dùng</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-400 inline-block"></span>Đặt trước</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-300 inline-block"></span>Bảo trì</span>
                </div>
            </div>
            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                <?php
                $tables = [
                    ["01","occupied","3 khách","O1042"],
                    ["02","empty","",""],
                    ["03","occupied","4 khách","O1038"],
                    ["04","reserved","18:30 - 4 kh",""],
                    ["05","empty","",""],
                    ["06","occupied","5 khách","O1041"],
                    ["07","occupied","6 khách","O1036"],
                    ["08","empty","",""],
                    ["VIP01","occupied","8 khách","O1039"],
                    ["VIP02","reserved","19:00 - 6 kh",""],
                    ["VIP03","empty","",""],
                    ["T.Sân","maintenance","Đang sửa",""],
                ];
                $tableColors = [
                    "occupied"    => "bg-orange-500 text-white",
                    "empty"       => "bg-green-100 text-green-800 border-2 border-green-300",
                    "reserved"    => "bg-yellow-400 text-yellow-900",
                    "maintenance" => "bg-gray-200 text-gray-500",
                ];
                foreach($tables as [$tName, $tStatus, $tInfo, $tOrder]): ?>
                <div class="<?= $tableColors[$tStatus] ?> rounded-xl p-3 text-center cursor-pointer hover:scale-105 transition-transform shadow-sm">
                    <div class="font-extrabold text-sm">Bàn <?= $tName ?></div>
                    <?php if($tInfo): ?>
                    <div class="text-xs mt-1 opacity-80 truncate"><?= $tInfo ?></div>
                    <?php endif; ?>
                    <?php if($tOrder): ?>
                    <div class="text-xs mt-0.5 opacity-70">#<?= $tOrder ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div><!-- /p-6 -->
</main>

</body>
</html>
