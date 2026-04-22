<?php
include "../model/connect_db.php";
include "../model/menu.php";
include "../model/image_helper.php";
include "../model/kho.php";

    $act=isset($_GET['act']) ? ($_GET['act']) : 'Dashboard';

    switch ($act) {
        case 'Dashboard':
            include 'Dashboard.php';
            break;
        case 'menu':
            if(isset($_POST['insert_menu'])){
                $ma_mon_an=$_POST['ma_mon_an'];
                $ten_mon=$_POST['ten_mon'];
                $gia_ban=$_POST['gia_ban'];
                $ma_danh_muc=$_POST['ma_danh_muc'];
                $mo_ta_ngan=$_POST['mo_ta_ngan'];
                $trang_thai=$_POST['trang_thai'];
                //  if($ma_mon_an == "") {
                //     $ma_mon_an = "M" . (count(get_all_menu()) + 1); 
                //  }
                $ten_anh_moi = upload_and_optimize_image($_FILES['duong_dan_anh'], "uploads/", null);
                $duong_dan_anh = ($ten_anh_moi) ? "uploads/" . $ten_anh_moi : "";
                if($ma_mon_an == "" || $ma_mon_an == "Tự động tạo") {
                   $ma_mon_an = "M" . (count(get_all_menu()) + 1); 
                   insert_menu($ma_mon_an, $ten_mon, $gia_ban, $ma_danh_muc, $duong_dan_anh, $mo_ta_ngan, $trang_thai);
                } else {
                   
                    update_menu($ma_mon_an, $ten_mon, $gia_ban, $ma_danh_muc, $duong_dan_anh, $mo_ta_ngan, $trang_thai);
                }
                header("Location: index.php?act=menu");
            }
            $list_menu=get_all_menu();
            include 'menu.php';
            break;
        case 'delete_menu':
            if(isset($_GET['ma_mon_an'])){
                $ma_mon_an = $_GET['ma_mon_an'];
                delete_menu($ma_mon_an);
            }
            header("Location: index.php?act=menu");
            break;
        case 'order':
            include 'order.php';
            break;
        case 'inventory':
            include 'inventory.php';
            break;
        case 'dinh_muc':
            include 'dinh_muc.php';
            break;
        case 'staff':
            include 'staff.php';
            break;
        case 'reports':
            include 'reports.php';
            break;
        case 'marketing':
            include 'marketing.php';
            break;
        case 'setting':
        case 'settings':
            include 'settings.php';
            break;
        default:
            include 'Dashboard.php';
            break;
    }

?>