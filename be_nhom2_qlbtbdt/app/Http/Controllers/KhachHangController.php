<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Models\User;
use App\Models\GioHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class KhachHangController extends Controller
{
    /**
     * Đăng nhập khách hàng
     */
    public function dangNhap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Vui lòng nhập email hoặc số điện thoại',
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

        // Tìm người dùng theo email hoặc số điện thoại
        $user = User::where('email', $loginInput)
            ->orWhere('so_dien_thoai', $loginInput)
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản không tồn tại trên hệ thống TechMart!'
            ], 404);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Mật khẩu không chính xác, vui lòng kiểm tra lại!'
            ], 401);
        }

        if (!$user->trang_thai) {
            return response()->json([
                'status'  => false,
                'message' => 'Tài khoản của bạn đang bị tạm khóa. Vui lòng liên hệ CSKH 1900.6789!'
            ], 403);
        }

        // Tạo Sanctum Token
        $token = $user->createToken('customer_token')->plainTextToken;

        // Nếu có session_id giỏ hàng của khách vãng lai, đồng bộ sang user_id
        $sessionId = $request->input('session_id');
        if ($sessionId) {
            GioHang::where('session_id', $sessionId)
                ->whereNull('nguoi_dung_id')
                ->update([
                    'nguoi_dung_id' => $user->id,
                    'session_id'    => null
                ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng nhập thành công! Chào mừng ' . $user->ho_ten,
            'token'   => $token,
            'user'    => [
                'id'            => $user->id,
                'ho_ten'        => $user->ho_ten,
                'email'         => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'vai_tro'       => $user->vai_tro,
                'anh_dai_dien'  => $user->anh_dai_dien,
                'dia_chi'       => $user->dia_chi,
            ]
        ]);
    }

    /**
     * Đăng ký tài khoản khách hàng mới
     */
    public function dangKy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten'        => 'required|string|max:100',
            'email'         => 'required|email|unique:nguoi_dung,email',
            'password'      => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:20|unique:nguoi_dung,so_dien_thoai',
            'dia_chi'       => 'nullable|string|max:255',
        ], [
            'ho_ten.required'        => 'Vui lòng nhập họ và tên',
            'email.required'         => 'Vui lòng nhập địa chỉ email',
            'email.email'            => 'Địa chỉ email không đúng định dạng',
            'email.unique'           => 'Email này đã được sử dụng trên hệ thống',
            'password.required'      => 'Vui lòng nhập mật khẩu',
            'password.min'           => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            'so_dien_thoai.unique'   => 'Số điện thoại này đã được đăng ký',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($request->input('ho_ten')) . '&background=2563eb&color=fff';

        $user = User::create([
            'ho_ten'        => $request->input('ho_ten'),
            'email'         => $request->input('email'),
            'password'      => Hash::make($request->input('password')),
            'so_dien_thoai' => $request->input('so_dien_thoai'),
            'dia_chi'       => $request->input('dia_chi') ?? 'Đà Nẵng, Việt Nam',
            'vai_tro'       => 'customer',
            'anh_dai_dien'  => $avatar,
            'trang_thai'    => true,
        ]);

        $token = $user->createToken('customer_token')->plainTextToken;

        // Nếu có session_id giỏ hàng thì merge luôn
        $sessionId = $request->input('session_id');
        if ($sessionId) {
            GioHang::where('session_id', $sessionId)
                ->whereNull('nguoi_dung_id')
                ->update([
                    'nguoi_dung_id' => $user->id,
                    'session_id'    => null
                ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng ký tài khoản thành công! Chào mừng ' . $user->ho_ten,
            'token'   => $token,
            'user'    => [
                'id'            => $user->id,
                'ho_ten'        => $user->ho_ten,
                'email'         => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'vai_tro'       => $user->vai_tro,
                'anh_dai_dien'  => $user->anh_dai_dien,
                'dia_chi'       => $user->dia_chi,
            ]
        ], 201);
    }

    /**
     * Xác thực Token khách hàng
     */
    public function checkToken(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');

        if (!$token) {
            return response()->json([
                'status'  => false,
                'message' => 'Chưa có Token xác thực'
            ], 401);
        }

        $tokenInstance = PersonalAccessToken::findToken($token);

        if (!$tokenInstance || !$tokenInstance->tokenable) {
            return response()->json([
                'status'  => false,
                'message' => 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại'
            ], 401);
        }

        $user = $tokenInstance->tokenable;

        return response()->json([
            'status'  => true,
            'message' => 'Token hợp lệ',
            'user'    => [
                'id'            => $user->id,
                'ho_ten'        => $user->ho_ten,
                'email'         => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'vai_tro'       => $user->vai_tro,
                'anh_dai_dien'  => $user->anh_dai_dien,
                'dia_chi'       => $user->dia_chi,
            ]
        ]);
    }

    /**
     * Đăng xuất khách hàng
     */
    public function dangXuat(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');
        if ($token) {
            $tokenInstance = PersonalAccessToken::findToken($token);
            if ($tokenInstance) {
                $tokenInstance->delete();
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đăng xuất tài khoản thành công'
        ]);
    }

    public function getData()
    {
        // Lấy danh sách khách hàng (vai_tro = customer)
        $data = User::where('vai_tro', 'customer')->orderBy('id', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu khách hàng thành công',
            'data'    => $data,
        ]);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $user = User::find($id);
        if ($user) {
            $user->trang_thai = !$user->trang_thai;
            $user->save();
            return response()->json([
                'status'     => true,
                'message'    => 'Đổi trạng thái tài khoản thành công',
                'trang_thai' => $user->trang_thai
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy người dùng'
        ], 404);
    }
}
