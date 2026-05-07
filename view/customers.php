<?php
$pageTitle = "Quản Lý Khách Hàng";
$activePage = "customers";

// Giả lập dữ liệu khách hàng (Mock Data)
$tiers = ["standard" => "Thường", "silver" => "Bạc", "gold" => "Vàng", "platinum" => "Kim Cương"];
$tierColors = [
    "standard" => "bg-gray-100 text-gray-700",
    "silver"   => "bg-slate-200 text-slate-800",
    "gold"     => "bg-yellow-100 text-yellow-700",
    "platinum" => "bg-purple-100 text-purple-700",
];

include_once "../model/connect_db.php";

$customers = [];
try {
    $conn = connectdb();
    $sql = "SELECT uid_firebase, ho_ten, email, so_dien_thoai, hang_thanh_vien, ngay_dang_ky FROM khach_hang ORDER BY ngay_dang_ky DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($result as $row) {
        $customers[] = [
            $row['uid_firebase'], 
            $row['ho_ten'] ?: "Chưa cập nhật", 
            $row['email'], 
            $row['so_dien_thoai'] ?: "Chưa cập nhật", 
            $row['ngay_dang_ky'], 
            0, // Số đơn hàng (Sẽ kết nối với bảng đơn hàng sau)
            0, // Tổng chi tiêu (Sẽ kết nối với bảng đơn hàng sau)
            $row['hang_thanh_vien']
        ];
    }
} catch (Exception $e) {
    // Bỏ qua nếu lỗi
}

?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    filterTier: 'all',
    searchQuery: '',
    showModal: false,
    editCustomer: null
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">🧑‍🤝‍🧑 Quản Lý Khách Hàng</h1>
            <p class="text-sm text-gray-500">Dữ liệu người dùng được đồng bộ từ Firebase</p>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php
            $kpiCustomers = [
                ["Tổng Khách Hàng", "all", count($customers), "gradient-primary"],
                ["Hạng Kim Cương", "platinum", 1, "gradient-purple"],
                ["Hạng Vàng", "gold", 2, "gradient-gold"],
                ["Khách Mới (Tháng)", "standard", 3, "gradient-blue"],
            ];
            foreach($kpiCustomers as [$label, $key, $cnt, $grad]): ?>
            <button @click="filterTier='<?= $key === 'all' ? 'all' : $key ?>'"
                    :class="filterTier==='<?= $key ?>' ? 'ring-2 ring-orange-400' : 'hover:shadow-md'"
                    class="<?= $grad ?> rounded-2xl p-4 text-white text-center transition-all cursor-pointer">
                <div class="text-2xl font-extrabold"><?= $cnt ?></div>
                <div class="text-xs opacity-80 mt-1"><?= $label ?></div>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex gap-2 flex-wrap">
                <button @click="filterTier='all'" :class="filterTier==='all'?'bg-orange-500 text-white':'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">Tất cả</button>
                <?php foreach($tiers as $tKey => $tLabel): ?>
                <button @click="filterTier='<?= $tKey ?>'" :class="filterTier==='<?= $tKey ?>'?'bg-orange-500 text-white':'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition"><?= $tLabel ?></button>
                <?php endforeach; ?>
            </div>
            <div class="ml-auto w-full md:w-auto">
                <input type="text" x-model="searchQuery" placeholder="Tìm theo tên, SĐT..."
                       class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 w-full md:w-64">
            </div>
        </div>

        <!-- Customers Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Khách Hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Liên Hệ</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Đăng Ký</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Đơn Hàng</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Tổng Chi Tiêu</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Hạng</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($customers as [$id, $name, $email, $phone, $regDate, $orders, $spent, $tier]):
                        $initials = implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $name), -2)));
                        $gradients = ["gradient-primary", "gradient-blue", "gradient-green", "gradient-purple", "gradient-teal"];
                        $grad = $gradients[crc32($name) % 5];
                    ?>
                    <tr class="trow transition-colors hover:bg-gray-50" 
                        x-show="(filterTier === 'all' || filterTier === '<?= $tier ?>') && 
                                ('<?= strtolower($name . ' ' . $phone) ?>'.includes(searchQuery.toLowerCase()))">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full <?= $grad ?> flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm">
                                    <?= $initials ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900"><?= $name ?></p>
                                    <p class="text-xs text-gray-400" title="<?= $id ?>">ID: <?= substr($id, 0, 8) ?>...</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-700"><?= $phone ?></p>
                            <p class="text-xs text-gray-400"><?= $email ?></p>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            <?= date('d/m/Y', strtotime($regDate)) ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg text-xs font-bold">
                                <?= $orders ?> đơn
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-orange-600">
                            <?= number_format($spent, 0, ',', '.') ?> ₫
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="badge <?= $tierColors[$tier] ?> shadow-sm"><?= $tiers[$tier] ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="showModal=true; editCustomer={name:'<?= $name ?>', phone:'<?= $phone ?>', email:'<?= $email ?>', tier:'<?= $tier ?>'}"
                                        class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition" title="Sửa">✏️</button>
                                <button class="px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition" title="Khóa/Xóa">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- Edit Customer Modal (Chỉ cập nhật hạng / thông tin ngoài Auth) -->
<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity">
    <div @click.away="showModal=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">📋 Chi Tiết & Phân Hạng Khách Hàng</h2>
            <button @click="showModal=false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên (Từ Firebase)</label>
                <input type="text" :value="editCustomer?.name||''" disabled
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-500 rounded-xl cursor-not-allowed">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email (Từ Firebase)</label>
                    <input type="email" :value="editCustomer?.email||''" disabled
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-500 rounded-xl cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="tel" :value="editCustomer?.phone||''" disabled
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-500 rounded-xl cursor-not-allowed">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hạng thành viên (Admin cấu hình)</label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 transition-shadow">
                    <?php foreach($tiers as $k => $v): ?>
                        <option value="<?= $k ?>" x-bind:selected="editCustomer?.tier === '<?= $k ?>'"><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-400 mt-2">* Thông tin cá nhân được quản lý bảo mật trên Firebase. Quản trị viên chỉ có thể cập nhật Hạng thành viên của khách.</p>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50">
            <button @click="showModal=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">Đóng</button>
            <button @click="showModal=false" class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 shadow-md transition">
                Lưu Thay Đổi Hạng
            </button>
        </div>
    </div>
</div>

</body>
</html>
