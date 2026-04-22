<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Cho phép CORS từ thiết bị khác

require_once __DIR__ . '/model/connect_db.php';
require_once __DIR__ . '/model/menu.php';

$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {
    case 'menu':
        // Lấy danh sách menu từ model
        $menuList = get_all_menu();
        
        // Nếu bạn muốn lọc món "Hết hàng" không cho hiện lên app thì có thể lọc ở đây
        // VD: $menuList = array_filter($menuList, fn($mon) => $mon['trang_thai'] !== 'Hết hàng');
        // $menuList = array_values($menuList);

        // Chuẩn bị URL hình ảnh để app Android hiển thị được
        // App Android chạy localhost (10.0.2.2) hoặc IP máy tính, nên cần gán full path cho ảnh
        $baseUrl = "http://" . $_SERVER['SERVER_NAME'] . "/web_admin/view/";
        
        foreach ($menuList as &$mon) {
            if (!empty($mon['duong_dan_anh'])) {
                // Nếu ảnh là dạng uploads/... thì gắn thêm baseUrl
                if (!str_starts_with($mon['duong_dan_anh'], 'http')) {
                    $mon['duong_dan_anh'] = $baseUrl . $mon['duong_dan_anh'];
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Lấy danh sách món ăn thành công',
            'data' => $menuList
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'API endpoint không tồn tại',
            'data' => null
        ]);
        break;
}
?>
