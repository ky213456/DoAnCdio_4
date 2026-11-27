<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SanPham;
use App\Models\User;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class DanhGiaController extends Controller
{
    private function resolveAuthenticatedUser(Request $request)
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

    private function hasCustomerOrderedProduct($userId, $productId): bool
    {
        return ChiTietDonHang::whereHas('order', function ($q) use ($userId) {
            $q->where('nguoi_dung_id', $userId)
              ->where('trang_thai_don_hang', '!=', 'da_huy');
        })->whereHas('variant', function ($q) use ($productId) {
            $q->where('san_pham_id', $productId);
        })->exists();
    }

    /**
     * Lấy danh sách đánh giá của sản phẩm (Client)
     */
    public function getReviewsByProduct(Request $request, $productId)
    {
        $sanPham = SanPham::find($productId);
        if (!$sanPham) {
            return response()->json([
                'status'  => false,
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        $reviews = Review::with('user:id,ho_ten,anh_dai_dien')
            ->where('san_pham_id', $productId)
            ->where('trang_thai_duyet', true)
            ->latest()
            ->get();

        $total = $reviews->count();
        $avg = $total > 0 ? round($reviews->avg('so_sao'), 1) : 5.0;

        // Phân bổ 1 - 5 sao
        $starsDistribution = [
            5 => $reviews->where('so_sao', 5)->count(),
            4 => $reviews->where('so_sao', 4)->count(),
            3 => $reviews->where('so_sao', 3)->count(),
            2 => $reviews->where('so_sao', 2)->count(),
            1 => $reviews->where('so_sao', 1)->count(),
        ];

        $user = $this->resolveAuthenticatedUser($request);
        $canReview = $user ? $this->hasCustomerOrderedProduct($user->id, $productId) : false;

        return response()->json([
            'status'     => true,
            'message'    => 'Lấy danh sách đánh giá thành công',
            'can_review' => $canReview,
            'stats'      => [
                'average'         => $avg,
                'total'           => $total,
                'stars_breakdown' => $starsDistribution
            ],
            'data'       => $reviews,
            'details'    => [
                'san_pham_id'     => $productId,
                'ten_san_pham'    => $sanPham->ten_san_pham,
                'diem_trung_binh' => $avg,
                'tong_danh_gia'   => $total,
                'phan_bo_sao'     => $starsDistribution,
                'danh_sach'       => $reviews
            ]
        ]);
    }

    /**
     * Gửi đánh giá bình luận mới (Client - Bắt buộc đã đặt mua sản phẩm)
     */
    public function guiDanhGia(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'so_sao'   => 'required|integer|min:1|max:5',
            'noi_dung' => 'required|string|min:5|max:1000',
        ], [
            'so_sao.required'   => 'Vui lòng chọn số sao đánh giá (1-5 sao)',
            'so_sao.min'        => 'Số sao tối thiểu là 1',
            'so_sao.max'        => 'Số sao tối đa là 5',
            'noi_dung.required' => 'Vui lòng nhập nhận xét chi tiết của bạn',
            'noi_dung.min'      => 'Nội dung đánh giá cần ít nhất 5 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $this->resolveAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập để gửi đánh giá sản phẩm!'
            ], 401);
        }

        // Bắt buộc khách hàng phải có đơn hàng hợp lệ chứa sản phẩm này mới được đánh giá công khai
        $daMuaHang = $this->hasCustomerOrderedProduct($user->id, $productId);
        if (!$daMuaHang) {
            return response()->json([
                'status'  => false,
                'message' => 'Bạn cần đặt mua sản phẩm này trước khi gửi đánh giá & bình luận!'
            ], 403);
        }

        // Tạo đánh giá mới (mặc định duyệt luôn để trải nghiệm mượt mà)
        $review = Review::create([
            'san_pham_id'       => $productId,
            'nguoi_dung_id'     => $user->id,
            'so_sao'            => (int) $request->so_sao,
            'noi_dung'          => trim($request->noi_dung),
            'da_mua_hang'       => true,
            'trang_thai_duyet'  => true,
        ]);

        $review->load('user:id,ho_ten,anh_dai_dien');

        return response()->json([
            'status'  => true,
            'message' => 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm TechMart!',
            'data'    => $review
        ], 201);
    }

    /**
     * Lấy toàn bộ danh sách đánh giá cho Quản trị viên (Admin)
     */
    public function getAdminReviews(Request $request)
    {
        $query = Review::with(['product:id,ten_san_pham,anh_chinh', 'user:id,ho_ten,email']);

        if ($request->filled('san_pham_id')) {
            $query->where('san_pham_id', $request->san_pham_id);
        }

        if ($request->filled('so_sao')) {
            $query->where('so_sao', $request->so_sao);
        }

        if ($request->filled('trang_thai_duyet')) {
            $query->where('trang_thai_duyet', (bool)$request->trang_thai_duyet);
        }

        $reviews = $query->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách đánh giá admin thành công',
            'data'    => $reviews,
            'stats'   => [
                'total'    => Review::count(),
                'approved' => Review::where('trang_thai_duyet', true)->count(),
                'pending'  => Review::where('trang_thai_duyet', false)->count(),
            ]
        ]);
    }

    /**
     * Admin đổi trạng thái duyệt đánh giá (hiển thị / ẩn)
     */
    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy đánh giá!'
            ], 404);
        }

        $review->trang_thai_duyet = !$review->trang_thai_duyet;
        $review->save();

        return response()->json([
            'status'            => true,
            'message'           => 'Đổi trạng thái duyệt đánh giá thành công',
            'trang_thai_duyet'  => $review->trang_thai_duyet
        ]);
    }

    /**
     * Admin xóa đánh giá
     */
    public function deleteReview(Request $request)
    {
        $id = $request->input('id');
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy đánh giá!'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đã xóa đánh giá thành công'
        ]);
    }
}
