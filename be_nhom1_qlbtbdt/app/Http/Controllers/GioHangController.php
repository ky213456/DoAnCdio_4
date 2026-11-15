<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\SanPham;
use App\Models\BienTheSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GioHangController extends Controller
{
    /**
     * Lấy dữ liệu giỏ hàng theo user_id hoặc session_id
     */
    public function getData(Request $request)
    {
        $userId = $request->get('user_id');
        $sessionId = $request->get('session_id');

        if (!$userId && !$sessionId) {
            return response()->json([
                'status'         => true,
                'message'        => 'Giỏ hàng trống',
                'data'           => [],
                'total_items'    => 0,
                'total_quantity' => 0,
                'subtotal'       => 0,
            ]);
        }

        // SQL JOIN: Lấy giỏ hàng kèm thông tin sản phẩm, thương hiệu và biến thể
        $query = GioHang::join('bien_the_san_pham', 'gio_hang.bien_the_id', '=', 'bien_the_san_pham.id')
            ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
            ->leftJoin('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id');

        if ($userId) {
            $query->where('gio_hang.nguoi_dung_id', $userId);
        } else {
            $query->where('gio_hang.session_id', $sessionId);
        }

        $data = $query->select(
            'gio_hang.id as id',
            'gio_hang.id as gio_hang_id',
            'gio_hang.nguoi_dung_id',
            'gio_hang.session_id',
            'gio_hang.bien_the_id',
            'gio_hang.so_luong as quantity',
            'gio_hang.so_luong',
            'san_pham.id as san_pham_id',
            'san_pham.ten_san_pham as name',
            'san_pham.ten_san_pham',
            'san_pham.anh_chinh as image',
            'san_pham.anh_chinh',
            'thuong_hieu.ten_thuong_hieu',
            'bien_the_san_pham.sku',
            'bien_the_san_pham.ten_bien_the',
            'bien_the_san_pham.mau_sac',
            'bien_the_san_pham.ram',
            'bien_the_san_pham.o_cung',
            'bien_the_san_pham.gia_ban as originalPrice',
            'bien_the_san_pham.gia_ban',
            'bien_the_san_pham.gia_khuyen_mai',
            'bien_the_san_pham.so_luong_ton as stock',
            'bien_the_san_pham.so_luong_ton',
            'bien_the_san_pham.hinh_anh as hinh_anh_bien_the'
        )->orderBy('gio_hang.created_at', 'desc')->get();

        // Chuẩn hóa trường price, specs, subtotal cho từng item khớp với giao diện Wireframe 07
        $subtotal = 0;
        $totalQuantity = 0;
        foreach ($data as $item) {
            $effectivePrice = ($item->gia_khuyen_mai && $item->gia_khuyen_mai > 0)
                ? (float)$item->gia_khuyen_mai
                : (float)$item->gia_ban;

            $item->price = $effectivePrice;
            $item->originalPrice = (float)$item->gia_ban;
            $item->selected = true;
            $item->isGift = false;

            // Tạo specs hiển thị chi tiết biến thể
            $specsArr = [];
            if ($item->ten_thuong_hieu) $specsArr[] = $item->ten_thuong_hieu;
            if ($item->ram) $specsArr[] = 'RAM ' . $item->ram;
            if ($item->o_cung) $specsArr[] = 'SSD ' . $item->o_cung;
            if ($item->mau_sac) $specsArr[] = $item->mau_sac;
            if (empty($specsArr)) {
                $specsArr[] = $item->ten_bien_the ?: 'Chính hãng fullbox';
            }
            $item->specs = implode(' | ', $specsArr);
            $item->warrantyNote = 'Bảo hành: 24 Tháng chính hãng theo Serial/IMEI';

            if ($item->hinh_anh_bien_the) {
                $item->image = $item->hinh_anh_bien_the;
            }

            $subtotal += ($effectivePrice * $item->so_luong);
            $totalQuantity += $item->so_luong;
        }

        return response()->json([
            'status'         => true,
            'message'        => 'Lấy dữ liệu giỏ hàng thành công',
            'data'           => $data,
            'total_items'    => $data->count(),
            'total_quantity' => $totalQuantity,
            'subtotal'       => $subtotal,
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function themVaoGio(Request $request)
    {
        $userId = $request->input('user_id');
        $sessionId = $request->input('session_id');
        $sanPhamId = $request->input('san_pham_id');
        $bienTheId = $request->input('bien_the_id');
        $soLuong = max(1, (int)$request->input('so_luong', 1));

        if (!$userId && !$sessionId) {
            $sessionId = 'guest_' . Str::random(24);
        }

        // 1. Xác định biến thể sản phẩm (BienTheSanPham)
        $variant = null;
        if ($bienTheId) {
            $variant = BienTheSanPham::find($bienTheId);
        }

        if (!$variant && $sanPhamId) {
            $product = SanPham::find($sanPhamId);
            if (!$product) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Sản phẩm không tồn tại trên hệ thống'
                ], 404);
            }

            // Lấy biến thể đầu tiên của sản phẩm
            $variant = BienTheSanPham::where('san_pham_id', $product->id)->first();

            // Nếu sản phẩm chưa có biến thể nào trong DB, tự động khởi tạo biến thể tiêu chuẩn
            if (!$variant) {
                $variant = BienTheSanPham::create([
                    'san_pham_id'     => $product->id,
                    'sku'             => ($product->ma_san_pham ?? 'SP' . $product->id) . '-STD',
                    'ten_bien_the'    => 'Tiêu chuẩn',
                    'gia_ban'         => $product->gia_goc ?? 0,
                    'gia_khuyen_mai'  => $product->gia_khuyen_mai,
                    'so_luong_ton'    => 50,
                    'hinh_anh'        => $product->anh_chinh,
                    'trang_thai'      => 1
                ]);
            }
        }

        if (!$variant) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy thông tin biến thể thiết bị cần thêm vào giỏ!'
            ], 400);
        }

        // 2. Kiểm tra tồn kho
        $stock = $variant->so_luong_ton ?? 99;
        if ($stock <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Sản phẩm này tạm thời đã hết hàng trong kho!'
            ], 400);
        }

        // 3. Tìm sản phẩm đã có trong giỏ của user/session này chưa
        $cartQuery = GioHang::where('bien_the_id', $variant->id);
        if ($userId) {
            $cartQuery->where('nguoi_dung_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }

        $cartItem = $cartQuery->first();

        if ($cartItem) {
            // Đã có -> Tăng số lượng
            $newQuantity = $cartItem->so_luong + $soLuong;
            if ($newQuantity > $stock) {
                $newQuantity = $stock;
            }
            $cartItem->so_luong = $newQuantity;
            $cartItem->save();
        } else {
            // Chưa có -> Tạo mới
            $cartItem = GioHang::create([
                'nguoi_dung_id' => $userId,
                'session_id'    => $userId ? null : $sessionId,
                'bien_the_id'   => $variant->id,
                'so_luong'      => min($soLuong, $stock),
            ]);
        }

        // 4. Đếm tổng số lượng và sản phẩm hiện có trong giỏ
        $countQuery = GioHang::query();
        if ($userId) {
            $countQuery->where('nguoi_dung_id', $userId);
        } else {
            $countQuery->where('session_id', $sessionId);
        }
        $totalItems = $countQuery->count();
        $totalQuantity = (int)$countQuery->sum('so_luong');

        // Lấy tên sản phẩm để trả về câu thông báo đẹp
        $productName = $variant->product ? $variant->product->ten_san_pham : ($variant->ten_bien_the ?? 'Thiết bị');

        return response()->json([
            'status'         => true,
            'message'        => 'Đã thêm "' . $productName . '" vào giỏ hàng thành công!',
            'session_id'     => $sessionId,
            'cart_item'      => $cartItem,
            'total_items'    => $totalItems,
            'total_quantity' => $totalQuantity,
        ]);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request)
    {
        $id = $request->input('id') ?: $request->input('gio_hang_id');
        $soLuong = (int)$request->input('so_luong', 1);

        if (!$id) {
            return response()->json([
                'status'  => false,
                'message' => 'Thiếu mã bản ghi giỏ hàng'
            ], 400);
        }

        $cartItem = GioHang::find($id);
        if (!$cartItem) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
            ], 404);
        }

        if ($soLuong <= 0) {
            $cartItem->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'action'  => 'deleted'
            ]);
        }

        // Kiểm tra tồn kho của biến thể
        if ($cartItem->variant && $cartItem->variant->so_luong_ton > 0) {
            if ($soLuong > $cartItem->variant->so_luong_ton) {
                $soLuong = $cartItem->variant->so_luong_ton;
            }
        }

        $cartItem->so_luong = $soLuong;
        $cartItem->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật số lượng giỏ hàng thành công',
            'data'    => $cartItem
        ]);
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id') ?: $request->input('gio_hang_id');

        if (!$id) {
            return response()->json([
                'status'  => false,
                'message' => 'Thiếu mã bản ghi giỏ hàng cần xóa'
            ], 400);
        }

        $cartItem = GioHang::find($id);
        if ($cartItem) {
            $cartItem->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công'
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng của user / session
     */
    public function clear(Request $request)
    {
        $userId = $request->input('user_id');
        $sessionId = $request->input('session_id');

        if (!$userId && !$sessionId) {
            return response()->json([
                'status'  => false,
                'message' => 'Thiếu định danh giỏ hàng để xóa'
            ], 400);
        }

        $query = GioHang::query();
        if ($userId) {
            $query->where('nguoi_dung_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $query->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đã làm trống toàn bộ giỏ hàng thành công'
        ]);
    }
}
