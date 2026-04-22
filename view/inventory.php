<?php
$pageTitle = "Quản Lý Kho Hàng";
$activePage = "inventory";

require_once __DIR__ . '/../model/kho.php';

function fmtVND($n) { return number_format($n, 0, '.', '.') . '₫'; }

// ── Xử lý AJAX ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $act = $_POST['action'];

    if ($act === 'them_nguyen_lieu') {
        $ok = kho_them_nguyen_lieu(
            trim($_POST['ten']   ?? ''),
            trim($_POST['dv']    ?? 'kg'),
            (float)($_POST['sl'] ?? 0),
            (float)($_POST['ng'] ?? 0),
            (float)($_POST['gv'] ?? 0),
            !empty($_POST['dm']) ? (int)$_POST['dm'] : null
        );
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($act === 'cap_nhat_nguyen_lieu') {
        $ok = kho_cap_nhat_nguyen_lieu(
            (int)$_POST['id'],
            trim($_POST['ten']   ?? ''),
            trim($_POST['dv']    ?? 'kg'),
            (float)($_POST['ng'] ?? 0),
            (float)($_POST['gv'] ?? 0),
            !empty($_POST['dm']) ? (int)$_POST['dm'] : null
        );
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($act === 'dieu_chinh') {
        $ok = kho_dieu_chinh_ton(
            (int)$_POST['id'],
            $_POST['loai']    ?? 'dieu_chinh',
            (float)($_POST['sl'] ?? 0),
            trim($_POST['ly_do']  ?? ''),
            trim($_POST['ghi_chu'] ?? '')
        );
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($act === 'xoa') {
        $ok = kho_xoa_nguyen_lieu((int)$_POST['id']);
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($act === 'nhap_kho') {
        $ma_ncc  = !empty($_POST['ma_ncc']) ? (int)$_POST['ma_ncc'] : null;
        $ghi_chu = trim($_POST['ghi_chu'] ?? '');
        $items   = json_decode($_POST['items'] ?? '[]', true);
        if (empty($items)) {
            echo json_encode(['ok' => false, 'msg' => 'Không có dòng hàng nào']);
            exit;
        }
        $ok = kho_tao_phieu_nhap($ma_ncc, $ghi_chu, $items);
        echo json_encode(['ok' => $ok]);
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Unknown action']);
    exit;
}

// ── Lấy dữ liệu ─────────────────────────────────────────────
$dm_list   = kho_get_danh_muc();
$total     = kho_count_total();
$low_count = kho_count_low();
$tri_gia   = kho_tri_gia_ton();
$inventory = kho_get_nguyen_lieu();  // tất cả, filter phía client
$ajax_url  = 'index.php?act=inventory';
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    filterCat: 'all',
    filterStatus: 'all',
    search: '',
    showAdjust: false,
    showAdd: false,
    showPO: false,
    selectedItem: null,
    form: { id:0, ten:'', dv:'kg', sl:0, ng:0, gv:0, dm:'', loai:'dieu_chinh', delta:0, ly_do:'', ghi_chu:'' },
    po: { ma_ncc: '', ghi_chu: '', rows: [] },

    addPORow() {
        this.po.rows.push({ ma_nguyen_lieu: '', ten: '', so_luong: 1, don_gia: 0 });
    },
    removePORow(i) { this.po.rows.splice(i, 1); },
    poTotal() {
        return this.po.rows.reduce((s, r) => s + (parseFloat(r.so_luong)||0) * (parseFloat(r.don_gia)||0), 0)
                   .toLocaleString('vi-VN') + '₫';
    },

    openAdjust(item) {
        this.selectedItem = item;
        this.form.id  = item.id;
        this.form.loai = 'dieu_chinh';
        this.form.delta = 0;
        this.form.ly_do = '';
        this.form.ghi_chu = '';
        this.showAdjust = true;
    },
    openAdd() {
        this.form = { id:0, ten:'', dv:'kg', sl:0, ng:0, gv:0, dm:'', loai:'dieu_chinh', delta:0, ly_do:'', ghi_chu:'' };
        this.showAdd = true;
    },

    async saveAdjust() {
        const fd = new FormData();
        fd.append('action','dieu_chinh');
        fd.append('id', this.form.id);
        fd.append('loai', this.form.loai);
        fd.append('sl', this.form.delta);
        fd.append('ly_do', this.form.ly_do);
        fd.append('ghi_chu', this.form.ghi_chu);
        const r = await fetch('<?= $ajax_url ?>', {method:'POST', body:fd});
        const d = await r.json();
        if(d.ok){ location.reload(); } else { alert('Lỗi lưu dữ liệu!'); }
    },

    async saveAdd() {
        const fd = new FormData();
        fd.append('action','them_nguyen_lieu');
        fd.append('ten', this.form.ten);
        fd.append('dv',  this.form.dv);
        fd.append('sl',  this.form.sl);
        fd.append('ng',  this.form.ng);
        fd.append('gv',  this.form.gv);
        fd.append('dm',  this.form.dm);
        const r = await fetch('<?= $ajax_url ?>', {method:'POST', body:fd});
        const d = await r.json();
        if(d.ok){ location.reload(); } else { alert('Lỗi thêm nguyên liệu!'); }
    },

    async deleteItem(id) {
        if(!confirm('Xoá nguyên liệu này?')) return;
        const fd = new FormData();
        fd.append('action','xoa');
        fd.append('id', id);
        const r = await fetch('<?= $ajax_url ?>', {method:'POST', body:fd});
        const d = await r.json();
        if(d.ok){ location.reload(); } else { alert('Không thể xoá!'); }
    },

    async savePO() {
        if(this.po.rows.length === 0){ alert('Thêm ít nhất 1 dòng nguyên liệu!'); return; }
        const items = this.po.rows.map(r => ({
            ma_nguyen_lieu: parseInt(r.ma_nguyen_lieu),
            so_luong: parseFloat(r.so_luong),
            don_gia: parseFloat(r.don_gia)
        }));
        const fd = new FormData();
        fd.append('action','nhap_kho');
        fd.append('ma_ncc', this.po.ma_ncc);
        fd.append('ghi_chu', this.po.ghi_chu);
        fd.append('items', JSON.stringify(items));
        const r = await fetch('<?= $ajax_url ?>', {method:'POST', body:fd});
        const d = await r.json();
        if(d.ok){ location.reload(); } else { alert('Lỗi tạo phiếu nhập: ' + (d.msg||'')); }
    }
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <!-- Top Bar -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">📦 Quản Lý Kho Hàng</h1>
            <p class="text-sm text-gray-500">Tự động trừ khi bán món — liên kết menu</p>
        </div>
        <div class="flex gap-3">
            <button @click="showPO=true; po={ma_ncc:'',ghi_chu:'',rows:[]}"
                    class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                📋 Tạo Phiếu Nhập Kho
            </button>
            <button @click="openAdd()"
                    class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                + Thêm Nguyên Liệu
            </button>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- KPI Row -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="kpi-card gradient-primary">
                <p class="text-orange-100 text-sm">Tổng Nguyên Liệu</p>
                <p class="text-3xl font-extrabold mt-1"><?= $total ?></p>
                <p class="text-orange-200 text-sm mt-2">loại đang theo dõi</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">📦</div>
            </div>
            <div class="kpi-card" style="background:linear-gradient(135deg,#C62828,#B71C1C)">
                <p class="text-red-100 text-sm">Cảnh Báo Thấp</p>
                <p class="text-3xl font-extrabold mt-1"><?= $low_count ?></p>
                <p class="text-red-200 text-sm mt-2">cần đặt hàng gấp</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">⚠️</div>
            </div>
            <div class="kpi-card gradient-green">
                <p class="text-green-100 text-sm">Trị Giá Tồn Kho</p>
                <p class="text-2xl font-extrabold mt-1"><?= fmtVND($tri_gia) ?></p>
                <p class="text-green-200 text-sm mt-2">tính theo giá vốn</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">💰</div>
            </div>
        </div>

        <!-- Low-stock banner -->
        <?php
        $low_items = array_filter($inventory, fn($x) => $x['trang_thai'] === 'low');
        if (count($low_items) > 0): ?>
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-xl flex-shrink-0">⚠️</div>
            <div class="flex-1">
                <p class="font-semibold text-red-700 text-sm">Cần bổ sung: <?= count($low_items) ?> nguyên liệu dưới ngưỡng tối thiểu</p>
                <p class="text-xs text-red-500 mt-0.5">
                    <?= implode(' • ', array_column($low_items, 'ten_nguyen_lieu')) ?>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col md:flex-row gap-4 items-center">
            <div class="flex gap-2 flex-wrap">
                <button @click="filterCat='all'"
                        :class="filterCat==='all' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">Tất cả</button>
                <?php foreach ($dm_list as $dm): ?>
                <button @click="filterCat='<?= $dm['ten_danh_muc'] ?>'"
                        :class="filterCat==='<?= $dm['ten_danh_muc'] ?>' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition"><?= htmlspecialchars($dm['ten_danh_muc']) ?></button>
                <?php endforeach; ?>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <button @click="filterStatus = filterStatus==='low'?'all':'low'"
                        :class="filterStatus==='low' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">⚠️ Chỉ nguyên liệu thấp</button>
                <input type="text" x-model="search" placeholder="Tìm nguyên liệu..."
                       class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 w-56">
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nguyên Liệu</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Danh Mục</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tồn Kho</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Ngưỡng Tối Thiểu</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Mức Tồn</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Đơn Giá</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Liên Kết Món</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($inventory as $row):
                        $pct = $row['nguong_bao_dong'] > 0
                            ? min(100, round($row['so_luong_ton'] / $row['nguong_bao_dong'] * 100))
                            : 100;
                        $barColor = $pct < 50 ? 'bg-red-500' : ($pct < 80 ? 'bg-yellow-400' : 'bg-green-500');
                        $cat      = htmlspecialchars($row['ten_danh_muc'] ?? '');
                        $status   = $row['trang_thai'];
                        $linked   = $row['mon_lien_ket'];
                        // Encode for Alpine
                        $itemJson = htmlspecialchars(json_encode([
                            'id'  => $row['id'],
                            'ten' => $row['ten_nguyen_lieu'],
                            'dv'  => $row['don_vi_tinh'],
                        ]), ENT_QUOTES);
                    ?>
                    <tr class="hover:bg-gray-50 transition"
                        x-show="
                            (filterCat==='all' || filterCat==='<?= $cat ?>') &&
                            (filterStatus==='all' || filterStatus==='<?= $status ?>') &&
                            (search==='' || '<?= addslashes($row['ten_nguyen_lieu']) ?>'.toLowerCase().includes(search.toLowerCase()))
                        ">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if ($status === 'low'): ?>
                                <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0 animate-pulse"></span>
                                <?php else: ?>
                                <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
                                <?php endif; ?>
                                <span class="font-semibold text-gray-900"><?= htmlspecialchars($row['ten_nguyen_lieu']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge bg-orange-50 text-orange-700"><?= $cat ?: '—' ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-extrabold <?= $status === 'low' ? 'text-red-600' : 'text-gray-900' ?> text-lg">
                                <?= (float)$row['so_luong_ton'] + 0 ?>
                            </span>
                            <span class="text-gray-500 text-xs ml-1"><?= htmlspecialchars($row['don_vi_tinh']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600 text-sm">
                            <?= (float)$row['nguong_bao_dong'] + 0 ?> <?= htmlspecialchars($row['don_vi_tinh']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1 items-center">
                                <div class="w-full max-w-24 bg-gray-100 rounded-full h-2">
                                    <div class="<?= $barColor ?> h-2 rounded-full" style="width:<?= $pct ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?= $pct ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900 text-sm">
                            <?= fmtVND($row['gia_von_nhap']) ?>/<?= htmlspecialchars($row['don_vi_tinh']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1 max-w-48">
                                <?php foreach (array_slice($linked, 0, 2) as $m): ?>
                                <span class="badge bg-blue-50 text-blue-600"><?= htmlspecialchars($m) ?></span>
                                <?php endforeach; ?>
                                <?php if (count($linked) > 2): ?>
                                <span class="badge bg-gray-100 text-gray-500">+<?= count($linked) - 2 ?></span>
                                <?php endif; ?>
                                <?php if (empty($linked)): ?>
                                <span class="text-xs text-gray-400">Chưa liên kết</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="openAdjust(<?= $itemJson ?>)"
                                        class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition">
                                    ✏️ Điều chỉnh
                                </button>
                                <button @click="deleteItem(<?= $row['id'] ?>)"
                                        class="px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inventory)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            Chưa có nguyên liệu nào. Nhấn <strong>+ Thêm Nguyên Liệu</strong> để bắt đầu.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- Modal: Điều Chỉnh Tồn Kho -->
<div x-show="showAdjust" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="showAdjust=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-bold" x-text="'✏️ Điều Chỉnh: ' + (selectedItem ? selectedItem.ten : '')"></h2>
            <button @click="showAdjust=false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng điều chỉnh</label>
                    <input type="number" step="0.01" x-model="form.delta"
                           placeholder="Dương=tăng, âm=giảm"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại thao tác</label>
                    <select x-model="form.loai"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="nhap">Nhập hàng bổ sung</option>
                        <option value="xuat">Xuất bếp thủ công</option>
                        <option value="hao_hut">Hư hỏng / hao hụt</option>
                        <option value="dieu_chinh">Điều chỉnh kiểm kê</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lý do</label>
                <input type="text" x-model="form.ly_do" placeholder="Lý do ngắn gọn"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                <textarea rows="2" x-model="form.ghi_chu"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50">
            <button @click="showAdjust=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button @click="saveAdjust()" class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">Lưu Thay Đổi</button>
        </div>
    </div>
</div>

<!-- Modal: Thêm Nguyên Liệu -->
<div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="showAdd=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-bold">+ Thêm Nguyên Liệu Mới</h2>
            <button @click="showAdd=false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên nguyên liệu <span class="text-red-500">*</span></label>
                <input type="text" x-model="form.ten" placeholder="VD: Thịt heo ba chỉ"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính</label>
                    <select x-model="form.dv"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option>kg</option><option>lít</option><option>lon</option><option>gói</option><option>cái</option><option>hộp</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục kho</label>
                    <select x-model="form.dm"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($dm_list as $dm): ?>
                        <option value="<?= $dm['id'] ?>"><?= htmlspecialchars($dm['ten_danh_muc']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tồn ban đầu</label>
                    <input type="number" step="0.01" min="0" x-model="form.sl"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngưỡng cảnh báo</label>
                    <input type="number" step="0.01" min="0" x-model="form.ng"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá vốn (₫/đvt)</label>
                    <input type="number" step="1" min="0" x-model="form.gv"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50">
            <button @click="showAdd=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button @click="saveAdd()" class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">Thêm Mới</button>
        </div>
    </div>
</div>

<!-- Modal: Phiếu Nhập Kho -->
<div x-show="showPO" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div @click.away="showPO=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b flex-shrink-0">
            <h2 class="text-lg font-bold">📋 Tạo Phiếu Nhập Kho</h2>
            <button @click="showPO=false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <!-- Header phiếu -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhà cung cấp</label>
                    <select x-model="po.ma_ncc"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Không chọn --</option>
                        <?php
                        $db = connectdb();
                        $nccs = $db->query("SELECT id, ten_ncc FROM nha_cung_cap ORDER BY ten_ncc")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($nccs as $ncc): ?>
                        <option value="<?= $ncc['id'] ?>"><?= htmlspecialchars($ncc['ten_ncc']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú phiếu</label>
                    <input type="text" x-model="po.ghi_chu" placeholder="VD: Nhập hàng tuần 3/4"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>

            <!-- Bảng dòng hàng -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-500 font-semibold">Nguyên Liệu</th>
                            <th class="px-4 py-2 text-center text-gray-500 font-semibold w-28">Số Lượng</th>
                            <th class="px-4 py-2 text-center text-gray-500 font-semibold w-36">Đơn Giá (₫)</th>
                            <th class="px-4 py-2 text-right text-gray-500 font-semibold w-36">Thành Tiền</th>
                            <th class="w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in po.rows" :key="i">
                            <tr class="border-t border-gray-100">
                                <td class="px-4 py-2">
                                    <select x-model="row.ma_nguyen_lieu"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                                        <option value="">-- Chọn nguyên liệu --</option>
                                        <?php foreach ($inventory as $ng): ?>
                                        <option value="<?= $ng['id'] ?>"><?= htmlspecialchars($ng['ten_nguyen_lieu']) ?> (<?= htmlspecialchars($ng['don_vi_tinh']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" min="0.01" x-model="row.so_luong"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-center text-sm">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="1" min="0" x-model="row.don_gia"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-center text-sm">
                                </td>
                                <td class="px-4 py-2 text-right font-semibold text-gray-800" x-text="((parseFloat(row.so_luong)||0)*(parseFloat(row.don_gia)||0)).toLocaleString('vi-VN') + '₫'"></td>
                                <td class="px-2 py-2 text-center">
                                    <button @click="removePORow(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="po.rows.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400 text-sm">Nhấn "+ Thêm dòng" để bắt đầu</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between">
                <button @click="addPORow()"
                        class="px-4 py-2 border-2 border-dashed border-orange-300 text-orange-600 rounded-xl text-sm font-medium hover:bg-orange-50 transition">
                    + Thêm dòng nguyên liệu
                </button>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Tổng tiền phiếu nhập</p>
                    <p class="text-xl font-extrabold text-gray-900" x-text="poTotal()"></p>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50 flex-shrink-0">
            <button @click="showPO=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button @click="savePO()" class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">✅ Xác Nhận Nhập Kho</button>
        </div>
    </div>
</div>

</body>
</html>
