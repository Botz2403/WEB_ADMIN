<?php
$pageTitle = "Quản Lý Nhân Viên";
$activePage = "staff";

$roles = ["admin"=>"Quản Lý","cashier"=>"Thu Ngân","waiter"=>"Phục Vụ","kitchen"=>"Bếp","shipper"=>"Shipper"];
$roleColors = [
    "admin"   => "bg-purple-100 text-purple-700",
    "cashier" => "bg-blue-100 text-blue-700",
    "waiter"  => "bg-orange-100 text-orange-700",
    "kitchen" => "bg-red-100 text-red-700",
    "shipper" => "bg-green-100 text-green-700",
];

$staff = [
    ["S01","Nguyễn Văn Quang","admin",  "admin@gourmet.vn",  "0901 234 567","Chi nhánh Q.1", "active", "2023-01-10", 28],
    ["S02","Trần Thị Lan",     "cashier","lan.tt@gourmet.vn", "0912 345 678","Chi nhánh Q.1", "active", "2023-03-15", 25],
    ["S03","Lê Minh Đức",      "waiter", "duc.lm@gourmet.vn", "0923 456 789","Chi nhánh Q.1", "active", "2024-02-01", 22],
    ["S04","Phạm Thu Hương",   "waiter", "huong.pt@gourmet.vn","0934 567 890","Chi nhánh Q.7","active", "2024-05-20", 24],
    ["S05","Hoàng Văn Bếp",    "kitchen","bep.hv@gourmet.vn", "0945 678 901","Chi nhánh Q.1", "active", "2022-08-12", 35],
    ["S06","Võ Thị Giao",      "shipper","giao.vt@gourmet.vn","0956 789 012","Chi nhánh Q.7", "active", "2025-01-05", 27],
    ["S07","Ngô Anh Tú",       "waiter", "tu.na@gourmet.vn",  "0967 890 123","Chi nhánh Q.1", "inactive","2023-11-01",23],
    ["S08","Bùi Thị Ngọc",     "cashier","ngoc.bt@gourmet.vn","0978 901 234","Chi nhánh Q.7", "active", "2024-09-15", 26],
];

$permissions = [
    "admin"   => ["Dashboard","Menu","Orders","Inventory","Reports","Staff","Marketing","Settings","API Keys"],
    "cashier" => ["Dashboard","Orders","Reports (limited)"],
    "waiter"  => ["Orders","Floor Plan"],
    "kitchen" => ["Kitchen View","Orders (receive)"],
    "shipper" => ["Delivery Orders"],
];
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    filterRole: 'all',
    showModal: false,
    showPerms: false,
    editStaff: null
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">👥 Quản Lý Nhân Viên</h1>
            <p class="text-sm text-gray-500">Phân quyền & quản lý tài khoản</p>
        </div>
        <div class="flex gap-3">
            <button @click="showPerms=true" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50">
                🔐 Phân Quyền
            </button>
            <button @click="showModal=true; editStaff=null"
                    class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                + Thêm Nhân Viên
            </button>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- KPI -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <?php
            $kpiStaff = [
                ["Tổng NV","all",count($staff),"gradient-primary"],
                ["Quản Lý","admin",1,"gradient-purple"],
                ["Thu Ngân","cashier",2,"gradient-blue"],
                ["Phục Vụ","waiter",3,"gradient-primary"],
                ["Bếp + Giao","kitchen",2,"gradient-green"],
            ];
            foreach($kpiStaff as [$label,$key,$cnt,$grad]): ?>
            <button @click="filterRole='<?= $key ?>'"
                    :class="filterRole==='<?= $key ?>' ? 'ring-2 ring-orange-400' : 'hover:shadow-md'"
                    class="<?= $grad ?> rounded-2xl p-4 text-white text-center transition-all cursor-pointer">
                <div class="text-2xl font-extrabold"><?= $cnt ?></div>
                <div class="text-xs opacity-80 mt-1"><?= $label ?></div>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col md:flex-row gap-4 items-center">
            <div class="flex gap-2 flex-wrap">
                <button @click="filterRole='all'" :class="filterRole==='all'?'bg-orange-500 text-white':'bg-gray-100 text-gray-700'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition">Tất cả</button>
                <?php foreach($roles as $rKey=>$rLabel): ?>
                <button @click="filterRole='<?= $rKey ?>'" :class="filterRole==='<?= $rKey ?>'?'bg-orange-500 text-white':'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition"><?= $rLabel ?></button>
                <?php endforeach; ?>
            </div>
            <div class="ml-auto">
                <input type="text" placeholder="Tìm nhân viên..."
                       class="px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 w-56">
            </div>
        </div>

        <!-- Staff Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nhân Viên</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vai Trò</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Chi Nhánh</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Liên Hệ</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Ngày Vào</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tuổi</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Trạng Thái</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($staff as [$id,$name,$role,$email,$phone,$branch,$status,$joinDate,$age]):
                        $initials = implode('',array_map(fn($w)=>mb_substr($w,0,1), array_slice(explode(' ',$name),-2)));
                        $gradients = ["gradient-primary","gradient-blue","gradient-green","gradient-purple","gradient-teal"];
                        $grad = $gradients[crc32($name)%5];
                    ?>
                    <tr class="trow" x-show="filterRole==='all' || filterRole==='<?= $role ?>'">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full <?= $grad ?> flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    <?= $initials ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900"><?= $name ?></p>
                                    <p class="text-xs text-gray-400">ID: <?= $id ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge <?= $roleColors[$role] ?>"><?= $roles[$role] ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 text-sm"><?= $branch ?></td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700"><?= $phone ?></p>
                            <p class="text-xs text-gray-400"><?= $email ?></p>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            <?= date('d/m/Y', strtotime($joinDate)) ?>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600"><?= $age ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php if($status==='active'): ?>
                            <span class="badge bg-green-100 text-green-700">● Đang làm</span>
                            <?php else: ?>
                            <span class="badge bg-gray-100 text-gray-500">⏸ Nghỉ</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="showModal=true; editStaff={name:'<?= $name ?>',role:'<?= $role ?>'}"
                                        class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-100">✏️</button>
                                <button class="px-2.5 py-1.5 bg-purple-50 text-purple-600 rounded-lg text-xs font-medium hover:bg-purple-100">🔐</button>
                                <button class="px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Audit Logs -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4">📜 Audit Log — Hoạt Động Gần Đây</h2>
            <div class="space-y-3">
                <?php $logs = [
                    ["18:42","Nguyễn V.Quang","Cập nhật giá món Phở Bò: 60.000₫ → 65.000₫","menu","blue"],
                    ["18:31","Trần T.Lan","Thanh toán đơn #O1041 — 320.000₫ (VNPay)","payment","green"],
                    ["18:15","Lê M.Đức","Tạo đơn #O1040 tại bàn 03 — 3 món","order","orange"],
                    ["17:50","Hoàng V.Bếp","Cập nhật trạng thái đơn #O1039 → COMPLETED","kitchen","gray"],
                    ["17:20","Nguyễn V.Quang","Xóa nhân viên Ngô Anh Tú (tài khoản)","staff","red"],
                ];
                foreach($logs as [$time,$actor,$action,$type,$color]): ?>
                <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-gray-50 transition">
                    <span class="text-xs text-gray-400 w-12 flex-shrink-0 pt-0.5"><?= $time ?></span>
                    <div class="w-1.5 h-1.5 rounded-full bg-<?= $color ?>-400 mt-2 flex-shrink-0"></div>
                    <div>
                        <span class="font-semibold text-sm text-gray-900"><?= $actor ?></span>
                        <span class="text-sm text-gray-600 ml-2"><?= $action ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</main>

<!-- Add/Edit Staff Modal -->
<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="showModal=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-bold" x-text="editStaff ? '✏️ Chỉnh Sửa Nhân Viên' : '+ Thêm Nhân Viên'"></h2>
            <button @click="showModal=false" class="text-gray-400 hover:text-gray-700 text-2xl">×</button>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" :value="editStaff?.name||''" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <?php foreach($roles as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chi nhánh</label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option>Chi nhánh Q.1</option><option>Chi nhánh Q.7</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu tạm</label>
                <input type="password" placeholder="Hệ thống tự gửi qua email nếu bỏ trống"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50">
            <button @click="showModal=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">
                <span x-text="editStaff ? 'Cập Nhật' : 'Tạo Tài Khoản'"></span>
            </button>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div x-show="showPerms" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="showPerms=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b sticky top-0 bg-white z-10">
            <h2 class="text-lg font-bold">🔐 Ma Trận Phân Quyền</h2>
            <button @click="showPerms=false" class="text-gray-400 hover:text-gray-700 text-2xl">×</button>
        </div>
        <div class="p-6">
            <?php foreach($permissions as $roleKey=>$perms): ?>
            <div class="mb-5">
                <div class="flex items-center gap-3 mb-3">
                    <span class="badge <?= $roleColors[$roleKey] ?> text-sm"><?= $roles[$roleKey] ?></span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach($perms as $p): ?>
                    <span class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-xl text-xs font-medium">✅ <?= $p ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>
