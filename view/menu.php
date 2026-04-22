<?php
$pageTitle = "Quản Lý Menu";
$activePage = "menu";

$categories = ["Tất cả", "Món chính", "Khai vị", "Đồ uống", "Tráng miệng"];
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    showModal: false,
    editItem: null,
    searchQuery: '',
    filterCat: 'Tất cả'
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">🍜 Quản Lý Menu</h1>
            <p class="text-sm text-gray-500">Thêm, sửa, phân loại và đồng bộ thực đơn</p>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                📥 Import CSV
            </button>
            <button class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                📤 Export
            </button>
            <button @click="showModal = true; editItem = null"
                    class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                + Thêm Món Mới
            </button>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- Category Tabs + Search -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col md:flex-row gap-4">
            <div class="flex gap-2 flex-wrap">
                <?php foreach ($categories as $cat): ?>
                <button @click="filterCat = '<?= $cat ?>'"
                        :class="filterCat === '<?= $cat ?>' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all">
                    <?= $cat ?>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <input type="text" x-model="searchQuery" placeholder="Tìm kiếm món..."
                       class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 w-56">
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php $stats = [
                ["Tổng món",   count($list_menu), "📋"],
                ["Đang bán",   count(array_filter($list_menu, fn($x)=>$x['trang_thai']==='dang_ban')),   "✅"],
                ["Tạm dừng",  count(array_filter($list_menu, fn($x)=>$x['trang_thai']==='tam_dung')), "⏸"],
                ["Danh mục",   count($categories) - 1, "📂"],
            ];
            foreach ($stats as [$l, $v, $e]): ?>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center hover:shadow-sm transition">
                <div class="text-2xl"><?= $e ?></div>
                <div class="text-2xl font-extrabold text-gray-900 mt-1"><?= $v ?></div>
                <div class="text-sm text-gray-500"><?= $l ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Menu Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Món</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Danh Mục</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Giá</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Trạng Thái</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($list_menu as $item): ?>
                    <tr class="trow"
                        x-show="
                            (filterCat === 'Tất cả' || filterCat === '<?= $item['ma_danh_muc'] ?>') &&
                            ('<?= strtolower($item['ten_mon']) ?>'.includes(searchQuery.toLowerCase()) || searchQuery === '')
                        ">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="<?= $item['duong_dan_anh'] ?>" alt="<?= $item['ten_mon'] ?>"
                                     class="w-12 h-12 rounded-xl object-cover">
                                <div>
                                    <p class="font-semibold text-gray-900"><?= $item['ten_mon'] ?></p>
                                    <p class="text-xs text-gray-400">ID: <?= $item['ma_mon_an'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge bg-orange-50 text-orange-700"><?= $item['ten_danh_muc'] ?></span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            <?= number_format($item['gia_ban'], 0, '.', '.') ?>₫
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($item['trang_thai'] === 'dang_ban'): ?>
                            <span class="badge bg-green-100 text-green-700">● Đang bán</span>
                            <?php else: ?>
                            <span class="badge bg-gray-100 text-gray-500">⏸ Tạm dừng</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="showModal = true; editItem = {
                                            ma_mon_an: '<?= $item['ma_mon_an'] ?>',
                                            ten_mon: '<?= addslashes($item['ten_mon']) ?>',
                                            gia_ban: <?= $item['gia_ban'] ?>,
                                            ma_danh_muc: '<?= $item['ma_danh_muc'] ?>',
                                            mo_ta_ngan: '<?= addslashes($item['mo_ta_ngan']) ?>',
                                            trang_thai: '<?= $item['trang_thai'] ?>',
                                            duong_dan_anh: '<?= $item['duong_dan_anh'] ?>'
                                        }"
                                        class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100 transition">
                                    ✏️ Sửa
                                </button>
                                <a href="index.php?act=delete_menu&ma_mon_an=<?=$item['ma_mon_an']?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?')"  class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition">
                                    🗑️ Xóa
                                </a>
                            
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add/Edit Modal -->
<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <form action="index.php?act=menu" method="POST" enctype="multipart/form-data">
        <div @click.away="showModal = false"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-bold" x-text="editItem ? '✏️ Chỉnh Sửa Món' : '+ Thêm Món Mới'"></h2>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>
        <div class="p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã món ăn</label>
                <input type="text" :value="editItem ? editItem.ma_mon_an : 'Tự động tạo'" disabled
                       class="w-full px-4 py-3 border border-gray-200 bg-gray-50 rounded-xl text-gray-400 cursor-not-allowed font-mono">
                <input type="hidden" name="ma_mon_an" :value="editItem?.ma_mon_an || ''">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên món</label>
                <input type="text" name="ten_mon" :value="editItem?.ten_mon || ''" placeholder="Tên món ăn"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá (₫)</label>
                    <input type="number" name="gia_ban" :value="editItem?.gia_ban || ''" placeholder="65000"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                    <select name="ma_danh_muc" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <?php foreach (array_slice($categories, 1) as $index => $cat): ?>
                            <option value="<?= $index + 1 ?>" :selected="editItem?.ma_danh_muc == <?= $index + 1 ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="trang_thai" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="dang_ban" :selected="editItem?.trang_thai === 'dang_ban'">Đang bán</option>
                    <option value="tam_dung" :selected="editItem?.trang_thai === 'tam_dung'">Tạm dừng</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea rows="2" name="mo_ta_ngan" placeholder="Mô tả ngắn về món ăn..." x-text="editItem?.mo_ta_ngan || ''"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>
            <div>
              
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh món</label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-orange-400 transition cursor-pointer">
                    <input type="file" name="duong_dan_anh" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <template x-if="editItem?.duong_dan_anh">
                        <img :src="editItem.duong_dan_anh" class="w-16 h-16 mx-auto rounded-lg object-cover mb-2">
                    </template>
                    <p class="text-sm text-gray-500">Nhấn để thêm hoặc thay đổi ảnh</p>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50">
            <button @click="showModal = false"
                    class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button name="insert_menu" value="1" class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">
                <span x-text="editItem ? 'Cập Nhật Món' : 'Thêm Món'"></span>
            </button>
        </div>
    </div>
    </form>
</div>

</body>
</html>
