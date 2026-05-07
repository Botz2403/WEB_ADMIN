<?php
$pageTitle = "Quản Lý Bàn Ăn";
$activePage = "tables";

$zoneColors = [
    "trong_nha"  => "bg-blue-100 text-blue-700",
    "ngoai_troi" => "bg-green-100 text-green-700",
    "vip"        => "bg-purple-100 text-purple-700",
    "bar"        => "bg-orange-100 text-orange-700"
];

$zoneNames = [
    "trong_nha"  => "Trong nhà",
    "ngoai_troi" => "Ngoài trời",
    "vip"        => "Phòng VIP",
    "bar"        => "Quầy Bar"
];

$statusColors = [
    "trong"     => "bg-gray-100 text-gray-600",
    "co_khach"  => "bg-orange-100 text-orange-700",
    "dat_truoc" => "bg-blue-100 text-blue-700",
    "bao_tri"   => "bg-red-100 text-red-700"
];

$statusNames = [
    "trong"     => "Trống",
    "co_khach"  => "Có khách",
    "dat_truoc" => "Đặt trước",
    "bao_tri"   => "Bảo trì"
];

include_once "../model/connect_db.php";

try {
    $conn = connectdb();
    
    // Xóa Bàn
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $stmt = $conn->prepare("DELETE FROM ban_an WHERE ma_ban = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: index.php?act=tables");
        exit();
    }

    // Thêm hoặc Cập nhật Bàn
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $ma_ban = $_POST['ma_ban'];
        $ten_ban = $_POST['ten_ban'];
        $khu_vuc = $_POST['khu_vuc'];
        $suc_chua = $_POST['suc_chua'];
        $trang_thai = $_POST['trang_thai'];

        if ($_POST['action'] == 'add') {
            $stmt = $conn->prepare("INSERT INTO ban_an (ma_ban, ten_ban, khu_vuc, suc_chua, trang_thai) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ma_ban, $ten_ban, $khu_vuc, $suc_chua, $trang_thai]);
        } elseif ($_POST['action'] == 'edit') {
            $old_id = $_POST['old_ma_ban'];
            $stmt = $conn->prepare("UPDATE ban_an SET ma_ban = ?, ten_ban = ?, khu_vuc = ?, suc_chua = ?, trang_thai = ? WHERE ma_ban = ?");
            $stmt->execute([$ma_ban, $ten_ban, $khu_vuc, $suc_chua, $trang_thai, $old_id]);
        }
        header("Location: index.php?act=tables");
        exit();
    }

    // Lấy danh sách bàn
    $stmt = $conn->prepare("SELECT * FROM ban_an ORDER BY khu_vuc, ma_ban");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính toán thống kê
    $totalTables = count($tables);
    $emptyTables = count(array_filter($tables, fn($t) => $t['trang_thai'] == 'trong'));
    $occupiedTables = count(array_filter($tables, fn($t) => $t['trang_thai'] == 'co_khach'));

} catch (PDOException $e) {
    echo "Lỗi CSDL: " . $e->getMessage();
    $tables = [];
    $totalTables = 0; $emptyTables = 0; $occupiedTables = 0;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <?php include '_head.php'; ?>
    <style>
    .grid-tables {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    </style>
</head>

<body class="bg-gray-50 text-gray-800" x-data="{ 
    modalOpen: false, 
    modalAction: 'add',
    formData: {
        old_ma_ban: '',
        ma_ban: '',
        ten_ban: '',
        khu_vuc: 'trong_nha',
        suc_chua: 4,
        trang_thai: 'trong'
    },
    openAddModal() {
        this.modalAction = 'add';
        this.formData = { old_ma_ban: '', ma_ban: '', ten_ban: '', khu_vuc: 'trong_nha', suc_chua: 4, trang_thai: 'trong' };
        this.modalOpen = true;
    },
    openEditModal(table) {
        this.modalAction = 'edit';
        this.formData = { ...table, old_ma_ban: table.ma_ban };
        this.modalOpen = true;
    }
}">

    <!-- Sidebar -->
    <?php include '_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8 transition-all">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quản Lý Bàn Ăn</h1>
                <p class="text-gray-500 mt-1">Sơ đồ và trạng thái hoạt động của các bàn</p>
            </div>
            <button @click="openAddModal()"
                class="gradient-primary text-white px-5 py-2.5 rounded-xl font-medium shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <span>➕</span> Thêm Bàn Mới
            </button>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                    🍽️</div>
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Tổng Số Bàn</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $totalTables ?></p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl">
                    🔥</div>
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Đang Phục Vụ</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $occupiedTables ?></p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl">
                    ✨</div>
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Bàn Trống</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $emptyTables ?></p>
                </div>
            </div>
        </div>

        <!-- Bàn Grid -->
        <div class="grid-tables">
            <?php foreach ($tables as $ban): ?>
            <div
                class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow relative group">
                <!-- Action Buttons (Hiện khi hover) -->
                <div class="absolute top-3 right-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="openEditModal(<?= htmlspecialchars(json_encode($ban)) ?>)"
                        class="w-8 h-8 bg-white text-blue-600 rounded-full shadow flex items-center justify-center hover:bg-blue-50 transition">✏️</button>
                    <a href="index.php?act=tables&action=delete&id=<?= $ban['ma_ban'] ?>"
                        onclick="return confirm('Xóa bàn <?= $ban['ten_ban'] ?>?')"
                        class="w-8 h-8 bg-white text-red-600 rounded-full shadow flex items-center justify-center hover:bg-red-50 transition">🗑️</a>
                </div>

                <div class="p-6 text-center">
                    <div
                        class="w-20 h-20 mx-auto bg-gray-50 rounded-full border-4 border-gray-100 flex items-center justify-center mb-4 relative">
                        <span class="text-3xl">🪑</span>
                        <!-- Chấm trạng thái -->
                        <div
                            class="absolute bottom-1 right-1 w-4 h-4 rounded-full border-2 border-white <?= $ban['trang_thai'] == 'trong' ? 'bg-green-500' : ($ban['trang_thai'] == 'co_khach' ? 'bg-orange-500' : ($ban['trang_thai'] == 'dat_truoc' ? 'bg-blue-500' : 'bg-red-500')) ?>">
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($ban['ten_ban']) ?></h3>
                    <p class="text-sm text-gray-500 font-medium"><?= htmlspecialchars($ban['ma_ban']) ?> •
                        <?= $ban['suc_chua'] ?> người</p>

                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full <?= $zoneColors[$ban['khu_vuc']] ?? 'bg-gray-100 text-gray-700' ?>">
                            <?= $zoneNames[$ban['khu_vuc']] ?? $ban['khu_vuc'] ?>
                        </span>
                        <span
                            class="px-3 py-1 text-xs font-medium rounded-full <?= $statusColors[$ban['trang_thai']] ?? 'bg-gray-100' ?>">
                            <?= $statusNames[$ban['trang_thai']] ?? $ban['trang_thai'] ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </main>

    <!-- Modal Thêm/Sửa Bàn -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="modalOpen = false" x-transition.opacity>
        </div>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 relative z-10 p-6"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-900"
                    x-text="modalAction === 'add' ? 'Thêm Bàn Mới' : 'Cập Nhật Bàn'"></h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>

            <form method="POST" action="index.php?act=tables" class="space-y-4">
                <input type="hidden" name="action" x-model="modalAction">
                <input type="hidden" name="old_ma_ban" x-model="formData.old_ma_ban">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã Bàn (VD: T01)</label>
                        <input type="text" name="ma_ban" x-model="formData.ma_ban" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sức chứa (người)</label>
                        <input type="number" name="suc_chua" x-model="formData.suc_chua" min="1" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Bàn hiển thị</label>
                    <input type="text" name="ten_ban" x-model="formData.ten_ban" required placeholder="Ví dụ: Bàn 01"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Khu vực</label>
                    <select name="khu_vuc" x-model="formData.khu_vuc"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                        <?php foreach($zoneNames as $key => $val): ?>
                        <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái hiện tại</label>
                    <select name="trang_thai" x-model="formData.trang_thai"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                        <?php foreach($statusNames as $key => $val): ?>
                        <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">Hủy</button>
                    <button type="submit"
                        class="px-4 py-2 gradient-primary text-white rounded-xl font-medium hover:shadow-lg transition">Lưu
                        Thông Tin</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>