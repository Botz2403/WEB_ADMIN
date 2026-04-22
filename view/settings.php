<?php
$pageTitle = "Cài Đặt Hệ Thống";
$activePage = "settings";
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50" x-data="{
    activeTab: 'branches',
    saved: false,
    showSavedToast() {
        this.saved = true;
        setTimeout(() => this.saved = false, 2500);
    }
}">

<?php include '_sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">⚙️ Cài Đặt Hệ Thống</h1>
            <p class="text-sm text-gray-500">Cấu hình chi nhánh, thanh toán, giao hàng</p>
        </div>
        <button @click="showSavedToast()" class="px-5 py-2.5 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
            💾 Lưu Thay Đổi
        </button>
    </header>

    <!-- Saved Toast -->
    <div x-show="saved" x-cloak x-transition
         class="fixed top-6 right-6 z-50 bg-green-600 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-sm font-semibold">
        ✅ Đã lưu thay đổi thành công!
    </div>

    <div class="p-6 fade-up">
        <div class="flex gap-6">
            <!-- Left Tabs -->
            <div class="w-56 flex-shrink-0">
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden sticky top-24">
                    <?php $tabs = [
                        "branches"  => ["🏢","Chi Nhánh"],
                        "payment"   => ["💳","Thanh Toán"],
                        "delivery"  => ["🚗","Giao Hàng"],
                        "tax"       => ["🧾","Thuế & Phí"],
                        "printer"   => ["🖨️","Máy In"],
                        "api"       => ["🔗","API & Webhook"],
                        "security"  => ["🔐","Bảo Mật"],
                        "notif"     => ["🔔","Thông Báo"],
                    ];
                    foreach($tabs as $key=>[$icon,$label]): ?>
                    <button @click="activeTab='<?= $key ?>'"
                            :class="activeTab==='<?= $key ?>' ? 'bg-orange-50 text-orange-600 border-r-2 border-orange-500 font-semibold' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm transition-all">
                        <span><?= $icon ?></span> <?= $label ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Content -->
            <div class="flex-1 space-y-6">

                <!-- Branches -->
                <div x-show="activeTab==='branches'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">🏢 Quản Lý Chi Nhánh</h2>
                        <?php $branches = [
                            ["Chi nhánh Q.1","140 Lê Lợi, Q.1","07:00 – 22:30","35.000₫","active"],
                            ["Chi nhánh Q.7","89 Nguyễn Hữu Thọ, Q.7","07:30 – 22:00","40.000₫","active"],
                        ];
                        foreach($branches as [$bName,$bAddr,$bHours,$bDeliv,$bStatus]): ?>
                        <div class="border border-gray-100 rounded-2xl p-5 mb-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center text-white text-lg">🏠</div>
                                    <div>
                                        <p class="font-bold text-gray-900"><?= $bName ?></p>
                                        <p class="text-xs text-gray-500"><?= $bAddr ?></p>
                                    </div>
                                </div>
                                <span class="badge bg-green-100 text-green-700">● Hoạt động</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Giờ mở cửa</label>
                                    <input type="text" value="<?= $bHours ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Phí giao hàng</label>
                                    <input type="text" value="<?= $bDeliv ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Múi giờ</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                        <option>Asia/Ho_Chi_Minh (UTC+7)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <button class="w-full py-3 border-2 border-dashed border-gray-300 rounded-2xl text-gray-500 text-sm font-medium hover:border-orange-400 hover:text-orange-500 transition">
                            + Thêm Chi Nhánh Mới
                        </button>
                    </div>
                </div>

                <!-- Payment -->
                <div x-show="activeTab==='payment'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">💳 Cổng Thanh Toán</h2>
                        <?php $gateways = [
                            ["VNPay","vnpay","🔵","Merchant ID, Hash Secret","active"],
                            ["MoMo","momo","🟣","Partner Code, Access Key","active"],
                            ["Stripe","stripe","🟦","Publishable Key, Secret Key","inactive"],
                            ["PayPal","paypal","🔵","Client ID, Client Secret","inactive"],
                            ["COD","cod","💵","Thanh toán khi nhận hàng","active"],
                        ];
                        foreach($gateways as [$gwName,$gwKey,$gwIcon,$gwConfig,$gwStatus]): ?>
                        <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-2xl mb-3 hover:shadow-sm transition">
                            <div class="text-3xl w-12 text-center"><?= $gwIcon ?></div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900"><?= $gwName ?></p>
                                <p class="text-xs text-gray-500"><?= $gwConfig ?></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <?php if($gwStatus==='active'): ?>
                                <span class="badge bg-green-100 text-green-700">● Hoạt động</span>
                                <?php else: ?>
                                <span class="badge bg-gray-100 text-gray-400">⏸ Tắt</span>
                                <?php endif; ?>
                                <button class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-medium hover:bg-blue-100">Cấu hình</button>
                                <!-- Toggle -->
                                <button class="w-10 h-6 rounded-full transition-colors flex items-center px-1
                                    <?= $gwStatus==='active' ? 'bg-orange-500 justify-end' : 'bg-gray-300 justify-start' ?>">
                                    <div class="w-4 h-4 bg-white rounded-full shadow"></div>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Webhook -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-4">🔗 Webhook Thanh Toán</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Webhook URL</label>
                                <div class="flex gap-2">
                                    <input type="text" value="https://yourdomain.com/api/webhook/payment" readonly
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-sm bg-gray-50 font-mono text-xs">
                                    <button class="px-4 py-3 border border-gray-300 rounded-xl text-sm hover:bg-gray-50">📋</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Secret Token</label>
                                <div class="flex gap-2">
                                    <input type="password" value="wh_secret_xxxxxxxxxxxx" readonly
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-sm bg-gray-50">
                                    <button class="px-4 py-3 border border-gray-300 rounded-xl text-sm hover:bg-gray-50">👁</button>
                                    <button class="px-4 py-3 bg-orange-50 text-orange-600 border border-orange-200 rounded-xl text-sm hover:bg-orange-100">🔄</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery -->
                <div x-show="activeTab==='delivery'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">🚗 Cài Đặt Giao Hàng</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bán kính giao hàng tối đa (km)</label>
                                <input type="number" value="10" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian chuẩn bị trung bình (phút)</label>
                                <input type="number" value="20" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phí giao hàng cố định</label>
                                <input type="text" value="35.000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Miễn phí ship từ (₫)</label>
                                <input type="text" value="200.000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Đối tác giao hàng</label>
                            <div class="grid grid-cols-3 gap-3">
                                <?php foreach(["Shipper nội bộ","GrabFood","ShopeeFood"] as $p): ?>
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 transition-all">
                                    <input type="checkbox" <?= $p==='Shipper nội bộ'?'checked':'' ?> class="accent-orange-500 w-4 h-4">
                                    <span class="text-sm font-medium"><?= $p ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax -->
                <div x-show="activeTab==='tax'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">🧾 Thuế & Phí Dịch Vụ</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">VAT (%)</label>
                                <input type="number" value="10" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phí dịch vụ (%)</label>
                                <input type="number" value="5" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã số thuế DN</label>
                                <input type="text" value="0312345678" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tiền tệ hiển thị</label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    <option>VND (₫)</option><option>USD ($)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                            <p class="text-sm font-semibold text-yellow-800">💡 Lưu ý</p>
                            <p class="text-xs text-yellow-700 mt-1">Thuế VAT sẽ được tự động tính vào hóa đơn. Phí dịch vụ áp dụng cho đơn tại bàn.</p>
                        </div>
                    </div>
                </div>

                <!-- API Keys -->
                <div x-show="activeTab==='api'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="font-bold text-gray-900 text-lg">🔗 API Keys</h2>
                            <button class="px-4 py-2 gradient-primary text-white rounded-xl text-sm font-semibold hover:opacity-90">+ Tạo Key Mới</button>
                        </div>
                        <?php $apiKeys = [
                            ["Mobile App Key","gourmet_mob_xxxxxxxxxxxxxxxx","Đọc menu, tạo đơn","2026-12-31","active"],
                            ["POS Terminal","gourmet_pos_yyyyyyyyyyyyyyyy","Thanh toán, in bill","2026-12-31","active"],
                            ["Third Party CRM","gourmet_crm_zzzzzzzzzzzzzzzz","Đọc khách hàng","2026-06-30","inactive"],
                        ];
                        foreach($apiKeys as [$kName,$kVal,$kPerms,$kExpiry,$kStatus]): ?>
                        <div class="border border-gray-100 rounded-2xl p-4 mb-3">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-semibold text-gray-900"><?= $kName ?></p>
                                    <p class="text-xs text-gray-500">Quyền: <?= $kPerms ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if($kStatus==='active'): ?>
                                    <span class="badge bg-green-100 text-green-700">● Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-gray-100 text-gray-500">⏸ Tắt</span>
                                    <?php endif; ?>
                                    <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-xl text-xs hover:bg-red-100">Thu hồi</button>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <input type="password" value="<?= $kVal ?>" readonly
                                       class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-500">
                                <button class="px-3 py-2 border border-gray-200 rounded-xl text-xs hover:bg-gray-50">👁</button>
                                <button class="px-3 py-2 border border-gray-200 rounded-xl text-xs hover:bg-gray-50">📋</button>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Hết hạn: <?= date('d/m/Y', strtotime($kExpiry)) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Security -->
                <div x-show="activeTab==='security'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">🔐 Bảo Mật</h2>
                        <div class="space-y-4">
                            <?php $secSettings = [
                                ["Xác thực 2 yếu tố (2FA)","Bắt buộc với tài khoản admin","enabled","toggle"],
                                ["Session timeout","Tự đăng xuất sau bao lâu không hoạt động","30 phút","select"],
                                ["Giới hạn đăng nhập sai","Khóa tài khoản sau số lần sai","5 lần","input"],
                                ["IP Whitelist","Chỉ cho phép đăng nhập từ IP cụ thể","Tắt","toggle"],
                                ["Audit log retention","Lưu log hoạt động trong bao lâu","90 ngày","select"],
                            ];
                            foreach($secSettings as [$sName,$sDesc,$sVal,$sType]): ?>
                            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm"><?= $sName ?></p>
                                    <p class="text-xs text-gray-500 mt-0.5"><?= $sDesc ?></p>
                                </div>
                                <?php if($sType==='toggle'): ?>
                                <button class="w-12 h-6 rounded-full transition-colors flex items-center px-1 <?= $sVal==='enabled'?'bg-orange-500 justify-end':'bg-gray-300 justify-start' ?>">
                                    <div class="w-4 h-4 bg-white rounded-full shadow"></div>
                                </button>
                                <?php elseif($sType==='select'): ?>
                                <select class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-orange-400">
                                    <option><?= $sVal ?></option>
                                </select>
                                <?php else: ?>
                                <input type="text" value="<?= $sVal ?>" class="w-24 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-orange-400">
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div x-show="activeTab==='notif'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">🔔 Cài Đặt Thông Báo</h2>
                        <div class="space-y-4">
                            <?php $notifs = [
                                ["Đơn hàng mới","Push + Email","Nhân viên + Quản lý"],
                                ["Tồn kho thấp","Email + In-app","Quản lý"],
                                ["Đặt bàn mới","Push + Email","Nhân viên + Quản lý"],
                                ["Thanh toán thành công","Email (tự động)","Khách hàng"],
                                ["Chiến dịch marketing","Push notification","Khách hàng"],
                                ["Lỗi hệ thống","Email khẩn","Quản lý"],
                            ];
                            foreach($notifs as [$nName,$nChannel,$nTo]): ?>
                            <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm"><?= $nName ?></p>
                                    <p class="text-xs text-gray-500 mt-0.5"><?= $nChannel ?> → <?= $nTo ?></p>
                                </div>
                                <button class="w-12 h-6 rounded-full flex items-center px-1 bg-orange-500 justify-end transition-colors">
                                    <div class="w-4 h-4 bg-white rounded-full shadow"></div>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Printer -->
                <div x-show="activeTab==='printer'" class="space-y-5">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                        <h2 class="font-bold text-gray-900 mb-5 text-lg">🖨️ Cài Đặt Máy In</h2>
                        <?php $printers = [
                            ["Máy in bếp","Epson TM-T88VI","192.168.1.10","Kitchen","active"],
                            ["Máy in thu ngân Q.1","Star TSP143","192.168.1.11","POS","active"],
                            ["Máy in thu ngân Q.7","Epson TM-T20","192.168.2.10","POS","active"],
                        ];
                        foreach($printers as [$pName,$pModel,$pIP,$pType,$pStatus]): ?>
                        <div class="border border-gray-100 rounded-2xl p-4 mb-3 flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-2xl">🖨️</div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900"><?= $pName ?></p>
                                <p class="text-xs text-gray-500"><?= $pModel ?> · <?= $pIP ?></p>
                                <span class="badge bg-blue-50 text-blue-600 mt-1"><?= $pType ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge bg-green-100 text-green-700">● Online</span>
                                <button class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs hover:bg-gray-100">Test In</button>
                                <button class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs hover:bg-blue-100">Cấu hình</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <button class="w-full py-3 border-2 border-dashed border-gray-300 rounded-2xl text-gray-500 text-sm font-medium hover:border-orange-400 hover:text-orange-500 transition">
                            + Thêm Máy In
                        </button>
                    </div>
                </div>

            </div><!-- /right -->
        </div><!-- /flex -->
    </div><!-- /p-6 -->
</main>

</body>
</html>
