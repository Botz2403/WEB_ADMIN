<?php
$pageTitle  = "Định Mức Nguyên Liệu";
$activePage = "dinh_muc";

require_once __DIR__ . '/../model/kho.php';

// ── AJAX handler ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'luu_cong_thuc') {
        $ma_mon = trim($_POST['ma_mon_an'] ?? '');
        $items  = json_decode($_POST['items'] ?? '[]', true);
        if (!$ma_mon) { echo json_encode(['ok'=>false,'msg'=>'Thiếu mã món']); exit; }
        $ok = cthuc_luu($ma_mon, $items);
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($_POST['action'] === 'xoa_cong_thuc') {
        $ok = cthuc_xoa(trim($_POST['ma_mon_an'] ?? ''));
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($_POST['action'] === 'get_cong_thuc') {
        $data = cthuc_get_by_mon(trim($_POST['ma_mon_an'] ?? ''));
        echo json_encode(['ok' => true, 'data' => $data]);
        exit;
    }

    echo json_encode(['ok'=>false,'msg'=>'Unknown action']); exit;
}

// ── Dữ liệu render ──────────────────────────────────────────
$db         = connectdb();
$all_mon    = $db->query("SELECT ma_mon_an, ten_mon FROM mon_an ORDER BY ten_mon")->fetchAll(PDO::FETCH_ASSOC);
$all_nguyen = kho_get_nguyen_lieu();
$cong_thuc  = cthuc_get_all();   // [ ma_mon_an => [ten_mon, ingredients[]] ]

// URL để AJAX POST về đúng file này (kể cả khi load qua index.php?act=dinh_muc)
$ajax_url = 'index.php?act=dinh_muc';
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="dinhMuc">
<script>
document.addEventListener('alpine:init', () => {
    const AJAX_URL = '<?= $ajax_url ?>';

    Alpine.data('dinhMuc', () => ({
        showModal: false,
        mon: { ma: '', ten: '' },
        rows: [],

        openModal(ma, ten) {
            this.mon = { ma, ten };
            this.rows = [];
            this.showModal = true;
            this.loadCT(ma);
        },

        async loadCT(ma) {
            const fd = new FormData();
            fd.append('action', 'get_cong_thuc');
            fd.append('ma_mon_an', ma);
            const r = await fetch(AJAX_URL, { method: 'POST', body: fd });
            const d = await r.json();
            if (d.ok) {
                this.rows = d.data.map(x => ({
                    ma_nguyen_lieu: String(x.ma_nguyen_lieu),
                    luong: x.luong_tieu_hao
                }));
            }
        },

        addRow() { this.rows.push({ ma_nguyen_lieu: '', luong: 0 }); },
        removeRow(i) { this.rows.splice(i, 1); },

        async save() {
            const fd = new FormData();
            fd.append('action', 'luu_cong_thuc');
            fd.append('ma_mon_an', this.mon.ma);
            fd.append('items', JSON.stringify(this.rows));
            const r = await fetch(AJAX_URL, { method: 'POST', body: fd });
            const d = await r.json();
            if (d.ok) { location.reload(); } else { alert('Loi luu cong thuc!'); }
        },

        async del(ma, ten) {
            if (!confirm('Xoa het cong thuc cua: ' + ten + '?')) return;
            const fd = new FormData();
            fd.append('action', 'xoa_cong_thuc');
            fd.append('ma_mon_an', ma);
            const r = await fetch(AJAX_URL, { method: 'POST', body: fd });
            const d = await r.json();
            if (d.ok) { location.reload(); } else { alert('Loi xoa!'); }
        }
    }));
});
</script>

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <!-- Top Bar -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">📋 Định Mức Nguyên Liệu</h1>
            <p class="text-sm text-gray-500">Cấu hình lượng tiêu hao nguyên liệu cho từng món ăn</p>
        </div>
        <div class="flex gap-3">
            <select id="quick-mon"
                    @change="if($event.target.value){ openModal($event.target.value, $event.target.selectedOptions[0].text); $event.target.value=''; }"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="">⚡ Chọn nhanh món...</option>
                <?php foreach ($all_mon as $m): ?>
                <option value="<?= $m['ma_mon_an'] ?>"><?= htmlspecialchars($m['ten_mon']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- KPI -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="kpi-card gradient-primary">
                <p class="text-orange-100 text-sm">Tổng Món Có Công Thức</p>
                <p class="text-3xl font-extrabold mt-1"><?= count($cong_thuc) ?></p>
                <p class="text-orange-200 text-sm mt-2">/ <?= count($all_mon) ?> món ăn</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">📋</div>
            </div>
            <div class="kpi-card gradient-green">
                <p class="text-green-100 text-sm">Tổng Nguyên Liệu Kho</p>
                <p class="text-3xl font-extrabold mt-1"><?= count($all_nguyen) ?></p>
                <p class="text-green-200 text-sm mt-2">loại sẵn sàng dùng</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">📦</div>
            </div>
            <div class="kpi-card" style="background:linear-gradient(135deg,#1565C0,#0D47A1)">
                <p class="text-blue-100 text-sm">Món Chưa Có Công Thức</p>
                <p class="text-3xl font-extrabold mt-1"><?= count($all_mon) - count($cong_thuc) ?></p>
                <p class="text-blue-200 text-sm mt-2">cần thiết lập định mức</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">⚠️</div>
            </div>
        </div>

        <!-- Danh sách công thức đã thiết lập -->
        <?php if (!empty($cong_thuc)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">✅ Công Thức Đã Thiết Lập</h2>
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($cong_thuc as $ma => $info): ?>
                <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50 transition">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($info['ten_mon']) ?>
                            <span class="text-xs text-gray-400 ml-1">(<?= $ma ?>)</span>
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <?php foreach ($info['ingredients'] as $ing): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-50 text-orange-700 rounded-lg text-xs font-medium">
                                <?= htmlspecialchars($ing['ten_nguyen_lieu']) ?>
                                <span class="font-bold"><?= (float)$ing['luong_tieu_hao']+0 ?> <?= htmlspecialchars($ing['don_vi_tinh']) ?></span>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <!-- Hidden trigger button for quick-select -->
                        <button id="btn-open-<?= $ma ?>"
                                @click="openModal('<?= $ma ?>', '<?= addslashes($info['ten_mon']) ?>')"
                                class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition">
                            ✏️ Sửa
                        </button>
                        <button @click="del('<?= $ma ?>', '<?= addslashes($info['ten_mon']) ?>')"
                                class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition">
                            🗑️
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Danh sách món chưa có công thức -->
        <?php
        $da_co   = array_keys($cong_thuc);
        $chua_co = array_filter($all_mon, fn($m) => !in_array($m['ma_mon_an'], $da_co));
        if (!empty($chua_co)):
        ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">⚠️ Món Chưa Có Công Thức</h2>
                <p class="text-sm text-gray-500 mt-0.5">Nhấn "Thiết lập" để thêm định mức nguyên liệu</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 p-4">
                <?php foreach ($chua_co as $m): ?>
                <button id="btn-open-<?= $m['ma_mon_an'] ?>"
                        @click="openModal('<?= $m['ma_mon_an'] ?>', '<?= addslashes($m['ten_mon']) ?>')"
                        class="p-3 border-2 border-dashed border-gray-200 rounded-xl text-left hover:border-orange-300 hover:bg-orange-50 transition group">
                    <p class="text-sm font-semibold text-gray-700 group-hover:text-orange-700"><?= htmlspecialchars($m['ten_mon']) ?></p>
                    <p class="text-xs text-gray-400 mt-1">+ Thiết lập định mức</p>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<!-- Modal: Thiết lập công thức -->
<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div @click.away="showModal=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
            <div>
                <h2 class="text-lg font-bold" x-text="'📋 Công thức: ' + mon.ten"></h2>
                <p class="text-xs text-gray-400 mt-0.5">Nhập lượng tiêu hao nguyên liệu cho mỗi 1 suất món</p>
            </div>
            <button @click="showModal=false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>

        <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-500 font-semibold">Nguyên Liệu</th>
                            <th class="px-4 py-2 text-center text-gray-500 font-semibold w-40">Lượng Tiêu Hao / Suất</th>
                            <th class="w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in rows" :key="i">
                            <tr class="border-t border-gray-100">
                                <td class="px-4 py-2">
                                    <select x-model="row.ma_nguyen_lieu"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                                        <option value="">-- Chọn nguyên liệu --</option>
                                        <?php foreach ($all_nguyen as $ng): ?>
                                        <option value="<?= $ng['id'] ?>">
                                            <?= htmlspecialchars($ng['ten_nguyen_lieu']) ?> (<?= htmlspecialchars($ng['don_vi_tinh']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.001" min="0.001" x-model="row.luong"
                                           placeholder="VD: 0.2"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-center text-sm">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button @click="removeRow(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="rows.length === 0">
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Nhấn "+ Thêm nguyên liệu" để bắt đầu cấu hình
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button @click="addRow()"
                    class="px-4 py-2 border-2 border-dashed border-orange-300 text-orange-600 rounded-xl text-sm font-medium hover:bg-orange-50 transition w-full">
                + Thêm nguyên liệu vào công thức
            </button>
        </div>

        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50 flex-shrink-0">
            <button @click="showModal=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button @click="save()" class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">
                💾 Lưu Công Thức
            </button>
        </div>
    </div>
</div>

</body>
</html>
