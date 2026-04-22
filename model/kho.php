<?php
require_once __DIR__ . '/connect_db.php';

/* ============================================================
   KHO MODEL — tương tác với CSDL web_admin
   Bảng liên quan:
     - kho_nguyen_lieu       (nguyên liệu)
     - danh_muc              (loai_danh_muc = 'kho_hang')
     - phieu_xuat_dieu_chinh (lịch sử điều chỉnh tồn kho)
     - phieu_nhap_kho        (phiếu nhập kho)
     - chi_tiet_phieu_nhap   (chi tiết phiếu nhập)
     - cong_thuc_mon_an      (món ăn liên kết nguyên liệu)
   ============================================================ */

/* ------------------------------------------------------------
   DANH MỤC KHO
   ------------------------------------------------------------ */

/** Lấy tất cả danh mục kho hàng */
function kho_get_danh_muc(): array {
    $db = connectdb();
    $stmt = $db->query(
        "SELECT id, ten_danh_muc
         FROM danh_muc
         WHERE loai_danh_muc = 'kho_hang'
         ORDER BY ten_danh_muc"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ------------------------------------------------------------
   NGUYÊN LIỆU (kho_nguyen_lieu)
   ------------------------------------------------------------ */

/**
 * Lấy danh sách nguyên liệu kèm tên danh mục và
 * danh sách tên món ăn liên kết (cong_thuc_mon_an).
 *
 * @param int|null $ma_danh_muc  Lọc theo danh mục (null = tất cả)
 * @param string   $filter_status  'all' | 'low'
 * @param string   $search        Chuỗi tìm kiếm tên nguyên liệu
 */
function kho_get_nguyen_lieu(
    ?int $ma_danh_muc = null,
    string $filter_status = 'all',
    string $search = ''
): array {
    $db  = connectdb();
    $sql = "SELECT k.id, k.ten_nguyen_lieu, k.don_vi_tinh,
                   k.so_luong_ton, k.nguong_bao_dong, k.gia_von_nhap,
                   d.ten_danh_muc,
                   CASE WHEN k.so_luong_ton <= k.nguong_bao_dong THEN 'low' ELSE 'ok' END AS trang_thai
            FROM kho_nguyen_lieu k
            LEFT JOIN danh_muc d ON d.id = k.ma_danh_muc
            WHERE 1=1";

    $params = [];

    if ($ma_danh_muc !== null) {
        $sql .= " AND k.ma_danh_muc = :ma_danh_muc";
        $params[':ma_danh_muc'] = $ma_danh_muc;
    }

    if ($filter_status === 'low') {
        $sql .= " AND k.so_luong_ton <= k.nguong_bao_dong";
    }

    if ($search !== '') {
        $sql .= " AND k.ten_nguyen_lieu LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY trang_thai DESC, k.ten_nguyen_lieu";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gắn danh sách tên món ăn liên kết cho mỗi nguyên liệu
    foreach ($rows as &$row) {
        $s = $db->prepare(
            "SELECT m.ten_mon
             FROM cong_thuc_mon_an c
             JOIN mon_an m ON m.ma_mon_an = c.ma_mon_an
             WHERE c.ma_nguyen_lieu = :id"
        );
        $s->execute([':id' => $row['id']]);
        $row['mon_lien_ket'] = $s->fetchAll(PDO::FETCH_COLUMN);
    }
    unset($row);

    return $rows;
}

/** KPI: tổng số nguyên liệu */
function kho_count_total(): int {
    $db = connectdb();
    return (int) $db->query("SELECT COUNT(*) FROM kho_nguyen_lieu")->fetchColumn();
}

/** KPI: số nguyên liệu dưới ngưỡng */
function kho_count_low(): int {
    $db = connectdb();
    return (int) $db->query(
        "SELECT COUNT(*) FROM kho_nguyen_lieu WHERE so_luong_ton <= nguong_bao_dong"
    )->fetchColumn();
}

/** KPI: tổng trị giá tồn kho (so_luong_ton × gia_von_nhap) */
function kho_tri_gia_ton(): float {
    $db = connectdb();
    return (float) $db->query(
        "SELECT COALESCE(SUM(so_luong_ton * gia_von_nhap), 0) FROM kho_nguyen_lieu"
    )->fetchColumn();
}

/** Thêm nguyên liệu mới */
function kho_them_nguyen_lieu(
    string $ten,
    string $don_vi,
    float  $so_luong,
    float  $nguong,
    float  $gia_von,
    ?int   $ma_danh_muc
): bool {
    $db   = connectdb();
    $stmt = $db->prepare(
        "INSERT INTO kho_nguyen_lieu
             (ten_nguyen_lieu, don_vi_tinh, so_luong_ton, nguong_bao_dong, gia_von_nhap, ma_danh_muc)
         VALUES (:ten, :dv, :sl, :ng, :gv, :dm)"
    );
    return $stmt->execute([
        ':ten' => $ten,
        ':dv'  => $don_vi,
        ':sl'  => $so_luong,
        ':ng'  => $nguong,
        ':gv'  => $gia_von,
        ':dm'  => $ma_danh_muc,
    ]);
}

/** Cập nhật thông tin nguyên liệu */
function kho_cap_nhat_nguyen_lieu(
    int    $id,
    string $ten,
    string $don_vi,
    float  $nguong,
    float  $gia_von,
    ?int   $ma_danh_muc
): bool {
    $db   = connectdb();
    $stmt = $db->prepare(
        "UPDATE kho_nguyen_lieu
         SET ten_nguyen_lieu = :ten,
             don_vi_tinh     = :dv,
             nguong_bao_dong = :ng,
             gia_von_nhap    = :gv,
             ma_danh_muc     = :dm
         WHERE id = :id"
    );
    return $stmt->execute([
        ':ten' => $ten,
        ':dv'  => $don_vi,
        ':ng'  => $nguong,
        ':gv'  => $gia_von,
        ':dm'  => $ma_danh_muc,
        ':id'  => $id,
    ]);
}

/** Xoá nguyên liệu */
function kho_xoa_nguyen_lieu(int $id): bool {
    $db   = connectdb();
    $stmt = $db->prepare("DELETE FROM kho_nguyen_lieu WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

/* ------------------------------------------------------------
   ĐIỀU CHỈNH TỒN KHO (phieu_xuat_dieu_chinh)
   ------------------------------------------------------------ */

/**
 * Ghi nhận điều chỉnh tồn kho và cập nhật so_luong_ton.
 *
 * @param int    $ma_nguyen_lieu
 * @param string $loai   'nhap' | 'xuat' | 'dieu_chinh' | 'hao_hut'
 * @param float  $so_luong  Dương = tăng, âm = giảm
 * @param string $ly_do
 * @param string $ghi_chu
 */
function kho_dieu_chinh_ton(
    int    $ma_nguyen_lieu,
    string $loai,
    float  $so_luong,
    string $ly_do    = '',
    string $ghi_chu  = ''
): bool {
    $db = connectdb();
    try {
        $db->beginTransaction();

        // Ghi lịch sử
        $ins = $db->prepare(
            "INSERT INTO phieu_xuat_dieu_chinh
                 (ma_nguyen_lieu, loai_thao_tac, so_luong, ly_do, ghi_chu)
             VALUES (:mnl, :loai, :sl, :lr, :gc)"
        );
        $ins->execute([
            ':mnl'  => $ma_nguyen_lieu,
            ':loai' => $loai,
            ':sl'   => $so_luong,
            ':lr'   => $ly_do,
            ':gc'   => $ghi_chu,
        ]);

        // Cập nhật tồn kho
        $upd = $db->prepare(
            "UPDATE kho_nguyen_lieu
             SET so_luong_ton = so_luong_ton + :sl
             WHERE id = :id"
        );
        $upd->execute([':sl' => $so_luong, ':id' => $ma_nguyen_lieu]);

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/**
 * Lịch sử điều chỉnh tồn kho của một nguyên liệu
 *
 * @param int $ma_nguyen_lieu
 * @param int $limit
 */
function kho_lich_su_dieu_chinh(int $ma_nguyen_lieu, int $limit = 20): array {
    $db   = connectdb();
    $stmt = $db->prepare(
        "SELECT p.id, p.loai_thao_tac, p.so_luong, p.ly_do, p.ghi_chu, p.ngay_thao_tac
         FROM phieu_xuat_dieu_chinh p
         WHERE p.ma_nguyen_lieu = :id
         ORDER BY p.ngay_thao_tac DESC
         LIMIT :lim"
    );
    $stmt->bindValue(':id',  $ma_nguyen_lieu, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit,          PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ------------------------------------------------------------
   PHIẾU NHẬP KHO
   ------------------------------------------------------------ */

/**
 * Tạo phiếu nhập kho (header + chi tiết) và cập nhật tồn kho.
 *
 * @param int|null $ma_ncc
 * @param string   $ghi_chu
 * @param array    $items  [ ['ma_nguyen_lieu'=>x, 'so_luong'=>y, 'don_gia'=>z], ... ]
 */
function kho_tao_phieu_nhap(?int $ma_ncc, string $ghi_chu, array $items): bool {
    $db = connectdb();
    try {
        $db->beginTransaction();

        $tong_tien = array_sum(array_map(fn($i) => $i['so_luong'] * $i['don_gia'], $items));

        // Tạo phiếu nhập
        $ins = $db->prepare(
            "INSERT INTO phieu_nhap_kho (ma_ncc, tong_tien, ghi_chu)
             VALUES (:ncc, :tt, :gc)"
        );
        $ins->execute([':ncc' => $ma_ncc, ':tt' => $tong_tien, ':gc' => $ghi_chu]);
        $ma_phieu = (int) $db->lastInsertId();

        foreach ($items as $item) {
            // Chi tiết phiếu nhập
            $det = $db->prepare(
                "INSERT INTO chi_tiet_phieu_nhap (ma_phieu_nhap, ma_nguyen_lieu, so_luong, don_gia)
                 VALUES (:mp, :mnl, :sl, :dg)"
            );
            $det->execute([
                ':mp'  => $ma_phieu,
                ':mnl' => $item['ma_nguyen_lieu'],
                ':sl'  => $item['so_luong'],
                ':dg'  => $item['don_gia'],
            ]);

            // Cập nhật tồn kho + ghi lịch sử
            $db->prepare(
                "UPDATE kho_nguyen_lieu SET so_luong_ton = so_luong_ton + :sl,
                                           gia_von_nhap  = :dg
                 WHERE id = :id"
            )->execute([':sl' => $item['so_luong'], ':dg' => $item['don_gia'], ':id' => $item['ma_nguyen_lieu']]);

            $db->prepare(
                "INSERT INTO phieu_xuat_dieu_chinh
                     (ma_nguyen_lieu, loai_thao_tac, so_luong, ly_do)
                 VALUES (:mnl, 'nhap', :sl, :lr)"
            )->execute([
                ':mnl' => $item['ma_nguyen_lieu'],
                ':sl'  => $item['so_luong'],
                ':lr'  => 'Nhập kho – phiếu #' . $ma_phieu,
            ]);
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/* ------------------------------------------------------------
   CÔNG THỨC MÓN ĂN (cong_thuc_mon_an)
   - Mỗi món ăn có danh sách nguyên liệu + lượng tiêu hao
   ------------------------------------------------------------ */

/**
 * Lấy toàn bộ công thức của một món ăn
 * Trả về: [ [ma_nguyen_lieu, ten_nguyen_lieu, don_vi_tinh, luong_tieu_hao], ... ]
 */
function cthuc_get_by_mon(string $ma_mon_an): array {
    $db   = connectdb();
    $stmt = $db->prepare(
        "SELECT c.ma_nguyen_lieu, k.ten_nguyen_lieu, k.don_vi_tinh, c.luong_tieu_hao
         FROM cong_thuc_mon_an c
         JOIN kho_nguyen_lieu k ON k.id = c.ma_nguyen_lieu
         WHERE c.ma_mon_an = :ma
         ORDER BY k.ten_nguyen_lieu"
    );
    $stmt->execute([':ma' => $ma_mon_an]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Lấy tất cả công thức (gom theo món) — dùng cho trang danh sách
 * Trả về: [ ma_mon_an => [ ten_mon, ingredients: [...] ] ]
 */
function cthuc_get_all(): array {
    $db   = connectdb();
    $stmt = $db->query(
        "SELECT c.ma_mon_an, m.ten_mon, c.ma_nguyen_lieu,
                k.ten_nguyen_lieu, k.don_vi_tinh, c.luong_tieu_hao
         FROM cong_thuc_mon_an c
         JOIN mon_an m ON m.ma_mon_an = c.ma_mon_an
         JOIN kho_nguyen_lieu k ON k.id = c.ma_nguyen_lieu
         ORDER BY m.ten_mon, k.ten_nguyen_lieu"
    );
    $rows   = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($rows as $r) {
        $key = $r['ma_mon_an'];
        if (!isset($result[$key])) {
            $result[$key] = ['ten_mon' => $r['ten_mon'], 'ingredients' => []];
        }
        $result[$key]['ingredients'][] = [
            'ma_nguyen_lieu'  => $r['ma_nguyen_lieu'],
            'ten_nguyen_lieu' => $r['ten_nguyen_lieu'],
            'don_vi_tinh'     => $r['don_vi_tinh'],
            'luong_tieu_hao'  => $r['luong_tieu_hao'],
        ];
    }
    return $result;
}

/**
 * Lưu công thức của một món (xoá cũ rồi insert lại toàn bộ)
 *
 * @param string $ma_mon_an
 * @param array  $items  [ ['ma_nguyen_lieu'=>x, 'luong'=>y], ... ]
 */
function cthuc_luu(string $ma_mon_an, array $items): bool {
    $db = connectdb();
    try {
        $db->beginTransaction();

        // Xoá công thức cũ
        $db->prepare("DELETE FROM cong_thuc_mon_an WHERE ma_mon_an = :ma")
           ->execute([':ma' => $ma_mon_an]);

        // Insert từng dòng mới
        $ins = $db->prepare(
            "INSERT INTO cong_thuc_mon_an (ma_mon_an, ma_nguyen_lieu, luong_tieu_hao)
             VALUES (:ma, :mnl, :luong)"
        );
        foreach ($items as $item) {
            if (empty($item['ma_nguyen_lieu']) || (float)$item['luong'] <= 0) continue;
            $ins->execute([
                ':ma'    => $ma_mon_an,
                ':mnl'   => (int)$item['ma_nguyen_lieu'],
                ':luong' => (float)$item['luong'],
            ]);
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

/** Xoá toàn bộ công thức của một món */
function cthuc_xoa(string $ma_mon_an): bool {
    $db = connectdb();
    return $db->prepare("DELETE FROM cong_thuc_mon_an WHERE ma_mon_an = :ma")
              ->execute([':ma' => $ma_mon_an]);
}

/* ------------------------------------------------------------
   TRỪ KHO TỰ ĐỘNG THEO ĐƠN HÀNG
   Gọi hàm này ngay sau khi đơn hàng được thanh toán thành công.
   ------------------------------------------------------------ */

/**
 * Trừ kho tự động dựa trên công thức món ăn.
 *
 * Luồng hoạt động:
 *   1. Lấy danh sách (món, số lượng) trong đơn hàng $id_hoa_don
 *   2. Với mỗi món → tra bảng cong_thuc_mon_an
 *   3. Tính tổng lượng tiêu hao = luong_tieu_hao × so_luong_mon
 *   4. Gọi kho_dieu_chinh_ton() kiểu 'xuat' (số âm) cho từng nguyên liệu
 *
 * Yêu cầu: bảng hoa_don và chi_tiet_hoa_don phải tồn tại với cấu trúc:
 *   chi_tiet_hoa_don: (ma_hoa_don, ma_mon_an, so_luong)
 *
 * @param  int  $id_hoa_don
 * @return bool  true nếu thành công, false nếu lỗi hoặc chưa có công thức
 */
function tru_kho_theo_don_hang(int $id_hoa_don): bool {
    $db = connectdb();
    try {
        $db->beginTransaction();

        // 1. Lấy danh sách món trong đơn hàng
        $stmt = $db->prepare(
            "SELECT ma_mon_an, so_luong
             FROM chi_tiet_hoa_don
             WHERE ma_hoa_don = :id"
        );
        $stmt->execute([':id' => $id_hoa_don]);
        $ds_mon = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($ds_mon)) {
            $db->rollBack();
            return false;
        }

        // Gom tổng lượng tiêu hao theo nguyên liệu
        $tieu_hao = []; // [ma_nguyen_lieu => tong_luong]

        foreach ($ds_mon as $mon) {
            // 2. Tra công thức của từng món
            $ct = $db->prepare(
                "SELECT ma_nguyen_lieu, luong_tieu_hao
                 FROM cong_thuc_mon_an
                 WHERE ma_mon_an = :ma"
            );
            $ct->execute([':ma' => $mon['ma_mon_an']]);
            $cong_thuc = $ct->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cong_thuc as $nl) {
                $id_nl = (int)$nl['ma_nguyen_lieu'];
                // 3. Tổng lượng tiêu hao = định mức × số lượng món
                $tieu_hao[$id_nl] = ($tieu_hao[$id_nl] ?? 0.0)
                    + (float)$nl['luong_tieu_hao'] * (float)$mon['so_luong'];
            }
        }

        // 4. Trừ kho từng nguyên liệu
        $upd = $db->prepare(
            "UPDATE kho_nguyen_lieu SET so_luong_ton = so_luong_ton - :sl WHERE id = :id"
        );
        $ins = $db->prepare(
            "INSERT INTO phieu_xuat_dieu_chinh
                 (ma_nguyen_lieu, loai_thao_tac, so_luong, ly_do)
             VALUES (:mnl, 'xuat', :sl, :lr)"
        );

        foreach ($tieu_hao as $id_nl => $luong) {
            if ($luong <= 0) continue;
            $upd->execute([':sl' => $luong, ':id' => $id_nl]);
            $ins->execute([
                ':mnl' => $id_nl,
                ':sl'  => -$luong,   // âm = xuất kho
                ':lr'  => 'Bán hàng – đơn #' . $id_hoa_don,
            ]);
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}