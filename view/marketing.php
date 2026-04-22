<?php
$pageTitle = "Marketing & Ưu Đãi";
$activePage = "marketing";

function fmtVND4($n) { return number_format($n,0,'.','.') . '₫'; }

$vouchers = [
    ["V001","WELCOME20","Giảm 20% đơn đầu tiên","percent",20,0,500,325,"active","2026-06-30","Khách mới"],
    ["V002","FREESHIP","Miễn phí giao hàng","shipping",0,30000,200,87,"active","2026-05-31","Đơn online"],
    ["V003","FLASH50K","Giảm 50.000₫ đơn từ 200k","fixed",0,50000,100,100,"expired","2026-03-31","Tất cả"],
    ["V004","VIP15","Khách VIP giảm 15%","percent",15,0,999,56,"active","2026-12-31","VIP member"],
    ["V005","WEEKEND","Weekend deal -30%","percent",30,0,300,142,"active","2026-04-30","T7 & CN"],
];

$campaigns = [
    ["C001","Khai trương Q.7","Push notification","2026-04-10","2026-04-15",1250,340,"completed"],
    ["C002","Flash Sale Thứ 6","In-app banner + Push","2026-04-18","2026-04-18",0,0,"scheduled"],
    ["C003","Loyalty Tháng 4","Email + Push","2026-04-01","2026-04-30",850,210,"active"],
];

$loyaltyTiers = [
    ["Bronze","0 – 499 điểm","#CD7F32","5%","Tích điểm x1"],
    ["Silver","500 – 1,499 điểm","#C0C0C0","8%","Tích điểm x1.5 + Birthday"],
    ["Gold","1,500 – 4,999 điểm","#FFD700","12%","Tích điểm x2 + Priority"],
    ["Platinum","5,000+ điểm","#E5E4E2","15%","Tích điểm x3 + Concierge"],
];
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    activeTab: 'vouchers',
    showVoucherModal: false,
    showCampaignModal: false
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">🎯 Marketing & Khuyến Mãi</h1>
            <p class="text-sm text-gray-500">Mã giảm giá, chiến dịch, loyalty points</p>
        </div>
        <div class="flex gap-3">
            <button @click="activeTab==='vouchers' ? showVoucherModal=true : showCampaignModal=true"
                    class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                <span x-text="activeTab==='vouchers' ? '+ Tạo Mã Giảm Giá' : (activeTab==='campaigns' ? '+ Tạo Chiến Dịch' : '')"></span>
            </button>
        </div>
    </header>

    <div class="p-6 space-y-6 fade-up">

        <!-- KPIs -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="kpi-card gradient-primary">
                <p class="text-orange-100 text-sm">Mã Đang Hoạt Động</p>
                <p class="text-3xl font-extrabold mt-1">4</p>
                <p class="text-orange-200 text-sm mt-2">vouchers active</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">🎫</div>
            </div>
            <div class="kpi-card gradient-blue">
                <p class="text-blue-100 text-sm">Lượt Dùng Hôm Nay</p>
                <p class="text-3xl font-extrabold mt-1">28</p>
                <p class="text-blue-200 text-sm mt-2">lần sử dụng</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">📲</div>
            </div>
            <div class="kpi-card gradient-green">
                <p class="text-green-100 text-sm">Thành Viên Loyalty</p>
                <p class="text-3xl font-extrabold mt-1">628</p>
                <p class="text-green-200 text-sm mt-2">+19% tháng này</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">⭐</div>
            </div>
            <div class="kpi-card gradient-purple">
                <p class="text-purple-100 text-sm">Điểm Đã Phát</p>
                <p class="text-3xl font-extrabold mt-1">24,500</p>
                <p class="text-purple-200 text-sm mt-2">loyalty points</p>
                <div class="text-5xl absolute right-4 bottom-4 opacity-20">🏆</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="border-b border-gray-100 px-6 flex gap-1 pt-4">
                <?php foreach(["vouchers"=>"🎫 Mã Giảm Giá","campaigns"=>"📢 Chiến Dịch","loyalty"=>"⭐ Loyalty Program"] as $tabKey=>$tabLabel): ?>
                <button @click="activeTab='<?= $tabKey ?>'"
                        :class="activeTab==='<?= $tabKey ?>' ? 'border-b-2 border-orange-500 text-orange-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="px-5 py-3 text-sm transition -mb-px"><?= $tabLabel ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Vouchers Tab -->
            <div x-show="activeTab==='vouchers'" class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mã</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mô Tả</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Loại / Giá trị</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Dùng / Giới hạn</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Hết hạn</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Áp dụng</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Trạng Thái</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($vouchers as [$vid,$code,$desc,$type,$pct,$fixed,$limit,$used,$status,$expiry,$appTo]):
                            $value = $type==='percent' ? "-{$pct}%" : ($type==='shipping' ? 'Miễn ship' : fmtVND4($fixed));
                        ?>
                        <tr class="trow">
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-xl text-sm inline-block"><?= $code ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-48"><?= $desc ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-900 text-lg"><?= $value ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="w-24 mx-auto">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span><?= $used ?></span><span><?= $limit === 999 ? '∞' : $limit ?></span>
                                    </div>
                                    <div class="bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-orange-500 h-1.5 rounded-full" style="width:<?= $limit===999?round($used/500*100):round($used/$limit*100) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600"><?= date('d/m/Y',strtotime($expiry)) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $appTo ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if($status==='active'): ?>
                                <span class="badge bg-green-100 text-green-700">● Active</span>
                                <?php else: ?>
                                <span class="badge bg-gray-100 text-gray-500">⏸ Hết hạn</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button class="px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs hover:bg-blue-100">✏️</button>
                                    <button class="px-2.5 py-1.5 bg-red-50 text-red-500 rounded-lg text-xs hover:bg-red-100">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Campaigns Tab -->
            <div x-show="activeTab==='campaigns'" class="p-6">
                <div class="space-y-4">
                    <?php foreach($campaigns as [$cid,$name,$channels,$startDate,$endDate,$sent,$opened,$status]):
                        $statusStyles = [
                            "active"    => "bg-green-100 text-green-700",
                            "scheduled" => "bg-yellow-100 text-yellow-700",
                            "completed" => "bg-gray-100 text-gray-500",
                        ];
                        $statusLabels = ["active"=>"🔴 Đang chạy","scheduled"=>"⏳ Lên lịch","completed"=>"✅ Hoàn thành"];
                    ?>
                    <div class="border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h3 class="font-bold text-gray-900"><?= $name ?></h3>
                                    <span class="badge <?= $statusStyles[$status] ?>"><?= $statusLabels[$status] ?></span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">📡 <?= $channels ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?= date('d/m/Y',strtotime($startDate)) ?> → <?= date('d/m/Y',strtotime($endDate)) ?></p>
                            </div>
                            <div class="flex gap-2">
                                <button class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-medium hover:bg-blue-100">Xem chi tiết</button>
                                <?php if($status==='scheduled'): ?>
                                <button class="px-3 py-1.5 bg-orange-500 text-white rounded-xl text-xs font-medium hover:bg-orange-600">▶ Gửi ngay</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($sent > 0): ?>
                        <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-50">
                            <div class="text-center">
                                <p class="text-2xl font-extrabold text-gray-900"><?= number_format($sent) ?></p>
                                <p class="text-xs text-gray-500">Đã gửi</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-extrabold text-blue-600"><?= $opened ?></p>
                                <p class="text-xs text-gray-500">Đã mở</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-extrabold text-green-600"><?= $sent>0?round($opened/$sent*100):0 ?>%</p>
                                <p class="text-xs text-gray-500">Open rate</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Loyalty Tab -->
            <div x-show="activeTab==='loyalty'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tiers -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4">Hạng Thành Viên</h3>
                        <div class="space-y-3">
                            <?php foreach($loyaltyTiers as [$tier,$range,$color,$discount,$perks]): ?>
                            <div class="border border-gray-100 rounded-2xl p-4 flex items-center gap-4 hover:shadow-sm transition">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-extrabold text-sm flex-shrink-0"
                                     style="background:<?= $color ?>22; color:<?= $color ?>; border:2px solid <?= $color ?>">
                                    <?= mb_substr($tier,0,2) ?>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900"><?= $tier ?></p>
                                    <p class="text-xs text-gray-500"><?= $range ?></p>
                                    <p class="text-xs text-gray-400 mt-1"><?= $perks ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-extrabold text-orange-600"><?= $discount ?></p>
                                    <p class="text-xs text-gray-400">giảm giá</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Stats -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4">Thống Kê Loyalty</h3>
                        <div class="space-y-3">
                            <?php $tierStats = [["Bronze",312,"#CD7F32",50],["Silver",198,"#C0C0C0",32],["Gold",89,"#FFD700",14],["Platinum",29,"#9B9B9B",4]];
                            foreach($tierStats as [$t,$cnt,$c,$p]): ?>
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                                <span class="w-16 text-sm font-semibold text-gray-700"><?= $t ?></span>
                                <div class="flex-1 bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full" style="width:<?= $p ?>%;background:<?= $c ?>"></div>
                                </div>
                                <span class="font-bold text-gray-900 w-10 text-right"><?= $cnt ?></span>
                                <span class="text-xs text-gray-400 w-8"><?= $p ?>%</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-2xl">
                            <p class="font-semibold text-orange-700 text-sm">💡 Quy tắc tích điểm</p>
                            <ul class="text-xs text-orange-600 mt-2 space-y-1 list-disc list-inside">
                                <li>10.000₫ chi tiêu = 1 điểm</li>
                                <li>Điểm có hiệu lực 12 tháng</li>
                                <li>Đổi điểm: 100 điểm = 10.000₫</li>
                                <li>Không áp dụng cùng voucher khác</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Create Voucher Modal -->
<div x-show="showVoucherModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="showVoucherModal=false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b sticky top-0 bg-white">
            <h2 class="text-lg font-bold">🎫 Tạo Mã Giảm Giá</h2>
            <button @click="showVoucherModal=false" class="text-gray-400 hover:text-gray-700 text-2xl">×</button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã voucher</label>
                <input type="text" placeholder="VD: SUMMER25" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 uppercase font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <input type="text" placeholder="Mô tả ngắn về ưu đãi" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá</label>
                    <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option>Phần trăm (%)</option>
                        <option>Số tiền cố định (₫)</option>
                        <option>Miễn phí giao hàng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị</label>
                    <input type="number" placeholder="VD: 20" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới hạn sử dụng</label>
                    <input type="number" placeholder="999 = không giới hạn" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t bg-gray-50">
            <button @click="showVoucherModal=false" class="flex-1 py-3 border border-gray-300 rounded-xl text-sm font-medium hover:bg-gray-100">Hủy</button>
            <button class="flex-1 py-3 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">Tạo Mã</button>
        </div>
    </div>
</div>

</body>
</html>
