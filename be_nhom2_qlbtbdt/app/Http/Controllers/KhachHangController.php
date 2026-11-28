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
                'quyen_han'     => $user->vai_tro === 'customer' ? [] : $user->getPermissionCodes(),
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
            'so_dien_thoai' => 'required|string|max:20|unique:nguoi_dung,so_dien_thoai',
            'dia_chi'       => 'nullable|string|max:255',
        ], [
            'ho_ten.required'        => 'Vui lòng nhập họ và tên',
            'email.required'         => 'Vui lòng nhập địa chỉ email',
            'email.email'            => 'Địa chỉ email không đúng định dạng',
            'email.unique'           => 'Email này đã được sử dụng trên hệ thống',
            'password.required'      => 'Vui lòng nhập mật khẩu',
            'password.min'           => 'Mật khẩu phải chứa ít nhất 6 ký tự',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại',
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
                'quyen_han'     => $user->vai_tro === 'customer' ? [] : $user->getPermissionCodes(),
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
                'quyen_han'     => $user->vai_tro === 'customer' ? [] : $user->getPermissionCodes(),
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
        // Lấy danh sách khách hàng (vai_tro = customer) kèm số đơn hàng
        $data = User::where('vai_tro', 'customer')
            ->withCount('orders')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu khách hàng thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten'        => 'required|string|max:100',
            'email'         => 'required|email|unique:nguoi_dung,email',
            'so_dien_thoai' => 'nullable|string|max:20|unique:nguoi_dung,so_dien_thoai',
            'password'      => 'nullable|string|min:6',
        ], [
            'ho_ten.required'      => 'Vui lòng nhập họ tên khách hàng',
            'email.required'       => 'Vui lòng nhập email',
            'email.unique'         => 'Email đã tồn tại',
            'so_dien_thoai.unique' => 'Số điện thoại đã tồn tại',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $kh = User::create([
            'ho_ten'        => trim($request->ho_ten),
            'email'         => trim($request->email),
            'so_dien_thoai' => $request->filled('so_dien_thoai') ? trim($request->so_dien_thoai) : null,
            'password'      => Hash::make($request->password ?: '123456'),
            'vai_tro'       => 'customer',
            'dia_chi'       => $request->dia_chi,
            'trang_thai'    => true,
        ]);

        return response()->json(['status' => true, 'message' => 'Thêm khách hàng thành công', 'data' => $kh], 201);
    }

    public function update(Request $request)
    {
        $kh = User::where('vai_tro', 'customer')->find($request->id);
        if (!$kh) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy khách hàng'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ho_ten'        => 'required|string|max:100',
            'email'         => 'required|email|unique:nguoi_dung,email,' . $kh->id,
            'so_dien_thoai' => 'nullable|string|max:20|unique:nguoi_dung,so_dien_thoai,' . $kh->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $kh->ho_ten        = trim($request->ho_ten);
        $kh->email         = trim($request->email);
        $kh->so_dien_thoai = $request->filled('so_dien_thoai') ? trim($request->so_dien_thoai) : null;
        $kh->dia_chi       = $request->dia_chi;
        $kh->save();

        return response()->json(['status' => true, 'message' => 'Cập nhật khách hàng thành công', 'data' => $kh]);
    }

    public function destroy(Request $request)
    {
        $kh = User::where('vai_tro', 'customer')->find($request->id);
        if (!$kh) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy khách hàng'], 404);
        }
        $kh->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa khách hàng thành công']);
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

    // ==========================================
    // QUẢN LÝ THÔNG TIN CÁ NHÂN (UC-PROFILE)
    // ==========================================
    private function getAuthenticatedUser(Request $request)
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
     * Lấy hồ sơ người dùng + Sổ địa chỉ + Thống kê hoạt động
     */
    public function getProfile(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập để xem thông tin cá nhân!'
            ], 401);
        }

        $addresses = \App\Models\UserAddress::where('nguoi_dung_id', $user->id)
            ->orderBy('mac_dinh', 'desc')
            ->latest()
            ->get();

        $donHangCount = \App\Models\DonHang::where('nguoi_dung_id', $user->id)->count();
        $tongChiTieu = \App\Models\DonHang::where('nguoi_dung_id', $user->id)
            ->where('trang_thai_thanh_toan', 'da_thanh_toan')
            ->sum('tong_thanh_toan');
        $yeuThichCount = \App\Models\SanPhamYeuThich::where('nguoi_dung_id', $user->id)->count();
        $danhGiaCount = \App\Models\Review::where('nguoi_dung_id', $user->id)->count();

        $userData = [
            'id'            => $user->id,
            'ho_ten'        => $user->ho_ten,
            'email'         => $user->email,
            'so_dien_thoai' => $user->so_dien_thoai,
            'dia_chi'       => $user->dia_chi,
            'anh_dai_dien'  => $user->anh_dai_dien,
            'vai_tro'       => $user->vai_tro,
            'created_at'    => $user->created_at ? $user->created_at->format('d/m/Y') : null,
        ];

        $statsData = [
            'so_don_hang'   => $donHangCount,
            'tong_chi_tieu' => (float) $tongChiTieu,
            'so_yeu_thich'  => $yeuThichCount,
            'so_danh_gia'   => $danhGiaCount,
        ];

        return response()->json([
            'status'    => true,
            'message'   => 'Lấy thông tin cá nhân thành công',
            'user'      => $userData,
            'addresses' => $addresses,
            'stats'     => $statsData,
            'data'      => [
                'user'      => $userData,
                'addresses' => $addresses,
                'stats'     => $statsData
            ]
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'ho_ten'        => 'required|string|max:100',
            'so_dien_thoai' => 'nullable|string|max:20|unique:nguoi_dung,so_dien_thoai,' . $user->id,
            'dia_chi'       => 'nullable|string|max:255',
            'anh_dai_dien'  => 'nullable|string|max:500',
        ], [
            'ho_ten.required'      => 'Vui lòng nhập họ và tên',
            'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng bởi tài khoản khác',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user->ho_ten = trim($request->ho_ten);
        if ($request->has('so_dien_thoai')) {
            $user->so_dien_thoai = trim($request->so_dien_thoai);
        }
        if ($request->has('dia_chi')) {
            $user->dia_chi = trim($request->dia_chi);
        }
        if ($request->filled('anh_dai_dien')) {
            $user->anh_dai_dien = trim($request->anh_dai_dien);
        }
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thông tin cá nhân thành công!',
            'user'    => [
                'id'            => $user->id,
                'ho_ten'        => $user->ho_ten,
                'email'         => $user->email,
                'so_dien_thoai' => $user->so_dien_thoai,
                'dia_chi'       => $user->dia_chi,
                'anh_dai_dien'  => $user->anh_dai_dien,
                'vai_tro'       => $user->vai_tro,
            ]
        ]);
    }

    /**
     * Đổi mật khẩu tài khoản
     */
    public function changePassword(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $currentPassword = $request->input('current_password') ?: $request->input('mat_khau_cu');
        $newPassword     = $request->input('new_password') ?: $request->input('mat_khau_moi');
        $confirmPassword = $request->input('new_password_confirmation') ?: ($request->input('xac_nhan_mat_khau') ?: $request->input('new_password'));

        if (empty($currentPassword)) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng nhập mật khẩu hiện tại'
            ], 422);
        }

        if (empty($newPassword) || strlen($newPassword) < 6) {
            return response()->json([
                'status'  => false,
                'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'
            ], 422);
        }

        if ($newPassword !== $confirmPassword) {
            return response()->json([
                'status'  => false,
                'message' => 'Xác nhận mật khẩu mới không khớp'
            ], 422);
        }

        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Mật khẩu hiện tại không chính xác!'
            ], 400);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới của bạn.'
        ]);
    }

    /**
     * Thêm địa chỉ nhận hàng vào sổ địa chỉ
     */
    public function addAddress(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'ho_ten_nguoi_nhan' => 'required|string|max:100',
            'so_dien_thoai'     => 'required|string|max:20',
            'dia_chi_chi_tiet'  => 'required|string|max:255',
            'tinh_thanh'        => 'required|string|max:100',
            'quan_huyen'        => 'required|string|max:100',
            'phuong_xa'         => 'required|string|max:100',
        ], [
            'ho_ten_nguoi_nhan.required' => 'Vui lòng nhập họ tên người nhận',
            'so_dien_thoai.required'     => 'Vui lòng nhập số điện thoại',
            'dia_chi_chi_tiet.required'  => 'Vui lòng nhập địa chỉ cụ thể',
            'tinh_thanh.required'        => 'Vui lòng chọn Tỉnh/Thành phố',
            'quan_huyen.required'        => 'Vui lòng chọn Quận/Huyện',
            'phuong_xa.required'         => 'Vui lòng chọn Phường/Xã',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $isDefault = $request->boolean('mac_dinh', false);
        if ($isDefault) {
            \App\Models\UserAddress::where('nguoi_dung_id', $user->id)->update(['mac_dinh' => false]);
        }

        $address = \App\Models\UserAddress::create([
            'nguoi_dung_id'     => $user->id,
            'ho_ten_nguoi_nhan' => trim($request->ho_ten_nguoi_nhan),
            'so_dien_thoai'     => trim($request->so_dien_thoai),
            'dia_chi_chi_tiet'  => trim($request->dia_chi_chi_tiet),
            'tinh_thanh'        => trim($request->tinh_thanh),
            'quan_huyen'        => trim($request->quan_huyen),
            'phuong_xa'         => trim($request->phuong_xa),
            'mac_dinh'          => $isDefault,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm địa chỉ giao hàng thành công',
            'data'    => $address
        ], 201);
    }

    /**
     * Xóa địa chỉ nhận hàng
     */
    public function deleteAddress(Request $request, $id = null)
    {
        $id = $id ?: $request->input('id');
        $user = $this->getAuthenticatedUser($request);
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $address = \App\Models\UserAddress::where('nguoi_dung_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$address) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy địa chỉ!'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đã xóa địa chỉ thành công'
        ]);
    }
}
