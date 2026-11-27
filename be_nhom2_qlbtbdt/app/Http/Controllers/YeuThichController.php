<?php

namespace App\Http\Controllers;

use App\Models\SanPhamYeuThich;
use App\Models\SanPham;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class YeuThichController extends Controller
{
    private function resolveUser(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');
        if ($token) {
            $tokenInstance = PersonalAccessToken::findToken($token);
            if ($tokenInstance && $tokenInstance->tokenable) {
                return $tokenInstance->tokenable;
            }
        }
        if ($request->filled('user_id')) {
            return User::find($request->user_id);
        }
        return null;
    }

    /**
     * Lấy danh sách sản phẩm yêu thích của người dùng
     */
    public function getWishlist(Request $request)
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập để xem danh sách yêu thích!',
                'data'    => []
            ], 401);
        }

        $favorites = SanPhamYeuThich::where('nguoi_dung_id', $user->id)
            ->with(['product.category', 'product.brand', 'product.specification', 'product.variants'])
            ->latest()
            ->get();

        $products = $favorites->pluck('product')->filter()->values();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách sản phẩm yêu thích thành công',
            'count'   => $products->count(),
            'data'    => $products
        ]);
    }

    /**
     * Thêm / Xóa sản phẩm khỏi danh sách yêu thích (Toggle)
     */
    public function toggleFavorite(Request $request)
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!'
            ], 401);
        }

        $productId = $request->input('san_pham_id');
        $sanPham = SanPham::find($productId);
        if (!$sanPham) {
            return response()->json([
                'status'  => false,
                'message' => 'Sản phẩm không tồn tại!'
            ], 404);
        }

        $exist = SanPhamYeuThich::where('nguoi_dung_id', $user->id)
            ->where('san_pham_id', $productId)
            ->first();

        if ($exist) {
            $exist->delete();
            return response()->json([
                'status'       => true,
                'is_favorite'  => false,
                'is_favorited' => false,
                'message'      => 'Đã xóa "' . $sanPham->ten_san_pham . '" khỏi danh sách yêu thích!',
                'san_pham_id'  => $productId
            ]);
        } else {
            SanPhamYeuThich::create([
                'nguoi_dung_id' => $user->id,
                'san_pham_id'   => $productId
            ]);
            return response()->json([
                'status'       => true,
                'is_favorite'  => true,
                'is_favorited' => true,
                'message'      => 'Đã thêm "' . $sanPham->ten_san_pham . '" vào danh sách yêu thích!',
                'san_pham_id'  => $productId
            ]);
        }
    }

    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     */
    public function removeFavorite(Request $request)
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $productId = $request->input('san_pham_id');
        SanPhamYeuThich::where('nguoi_dung_id', $user->id)
            ->where('san_pham_id', $productId)
            ->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích thành công'
        ]);
    }

    /**
     * Lấy mảng ID các sản phẩm đã yêu thích
     */
    public function getFavoriteIds(Request $request)
    {
        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'status' => true,
                'data'   => []
            ]);
        }

        $ids = SanPhamYeuThich::where('nguoi_dung_id', $user->id)
            ->pluck('san_pham_id')
            ->toArray();

        return response()->json([
            'status' => true,
            'data'   => $ids
        ]);
    }
}
