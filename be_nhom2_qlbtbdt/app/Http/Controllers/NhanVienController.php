<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class NhanVienController extends Controller
{
    /**
     * Đăng nhập cổng Quản trị (Admin / Staff)
     */
    public function dangNhap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Vui lòng nhập email quản trị',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $loginInput = trim($request->input('email'));
        $password = $request->input('password');

        $user = User::where('email', $loginInput)
            ->orWhere('so_dien_thoai', $loginInput)
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản quản trị không tồn tại!'
            ], 404);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Mật khẩu quản trị không chính xác!'
            ], 401);
        }

        // ========================================================
        // PHÂN BIỆT VAI TRÒ: CHỈ CHO PHÉP ADMIN HOẶC STAFF TRUY CẬP
        // ========================================================
        if (!in_array($user->vai_tro, ['admin', 'staff'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Từ chối truy cập! Tài khoản của bạn là Khách hàng (customer), không có quyền vào cổng Quản trị!'
            ], 403);
        }

        if (!$user->trang_thai) {
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản nhân viên này đang bị tạm khóa!'
            ], 403);
        }

        // Tạo token quản trị
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Đăng nhập Quản trị thành công! Xin chào ' . $user->ho_ten,
            'token'   => $token,
            'user'    => [
                'id'           => $user->id,
                'ho_ten'       => $user->ho_ten,
                'email'        => $user->email,
                'vai_tro'      => $user->vai_tro,
                'anh_dai_dien' => $user->anh_dai_dien,
            ]
        ]);
    }

    /**
     * Xác thực Token Admin
     */
    public function checkToken(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');

        if (!$token) {
            return response()->json([
                'status'  => false,
                'message' => 'Chưa có Token quản trị'
            ], 401);
        }

        $tokenInstance = PersonalAccessToken::findToken($token);

        if (!$tokenInstance || !$tokenInstance->tokenable) {
            return response()->json([
                'status'  => false,
                'message' => 'Phiên đăng nhập quản trị đã hết hạn'
            ], 401);
        }

        $user = $tokenInstance->tokenable;

        if (!in_array($user->vai_tro, ['admin', 'staff'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Không đủ quyền hạn quản trị'
            ], 403);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Token hợp lệ',
            'user'    => [
                'id'           => $user->id,
                'ho_ten'       => $user->ho_ten,
                'email'        => $user->email,
                'vai_tro'      => $user->vai_tro,
                'anh_dai_dien' => $user->anh_dai_dien,
            ]
        ]);
    }

    public function getData()
    {
        // Lấy tất cả tài khoản thuộc nhóm quản trị (admin và staff)
        $data = NhanVien::whereIn('vai_tro', ['admin', 'staff'])->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu nhân viên thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Thêm nhân viên thành công'
        ]);
    }

    public function update(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật nhân viên thành công'
        ]);
    }

    public function destroy(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Xóa nhân viên thành công'
        ]);
    }

    public function changeStatus(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Đổi trạng thái nhân viên thành công'
        ]);
    }
}
