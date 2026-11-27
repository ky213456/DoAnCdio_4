<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
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
                'quyen_han'    => $user->getPermissionCodes(),
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
                'quyen_han'    => $user->getPermissionCodes(),
                'anh_dai_dien' => $user->anh_dai_dien,
            ]
        ]);
    }

    public function getChucNangList()
    {
        return response()->json([
            'status' => true,
            'data'   => ChucNang::orderBy('id')->get(),
        ]);
    }

    public function getData()
    {
        $allCodes = ChucNang::pluck('ma_chuc_nang')->values()->toArray();
        $data = NhanVien::with('chucNangs')
            ->whereIn('vai_tro', ['admin', 'staff'])
            ->get()
            ->map(function ($nv) use ($allCodes) {
                $arr = $nv->toArray();
                $arr['quyen_han'] = $nv->isAdmin()
                    ? $allCodes
                    : $nv->chucNangs->pluck('ma_chuc_nang')->values()->toArray();
                return $arr;
            });

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu nhân viên thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten'        => 'required|string|max:100',
            'email'         => 'required|email|unique:nguoi_dung,email',
            'so_dien_thoai' => 'nullable|string|max:20|unique:nguoi_dung,so_dien_thoai',
            'password'      => 'required|string|min:6',
            'vai_tro'       => 'required|in:admin,staff',
        ], [
            'ho_ten.required'      => 'Vui lòng nhập họ tên nhân viên',
            'email.required'       => 'Vui lòng nhập email',
            'email.unique'         => 'Email này đã tồn tại trong hệ thống',
            'so_dien_thoai.unique' => 'Số điện thoại này đã tồn tại',
            'password.required'    => 'Vui lòng nhập mật khẩu',
            'password.min'         => 'Mật khẩu tối thiểu 6 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $defaultPerms = $request->vai_tro === 'admin'
            ? ChucNang::pluck('ma_chuc_nang')->toArray()
            : ['UC12', 'UC13', 'UC14', 'UC15', 'UC16'];

        $nv = User::create([
            'ho_ten'        => trim($request->ho_ten),
            'email'         => trim($request->email),
            'so_dien_thoai' => $request->filled('so_dien_thoai') ? trim($request->so_dien_thoai) : null,
            'password'      => Hash::make($request->password),
            'vai_tro'       => $request->vai_tro,
            'dia_chi'       => $request->dia_chi,
            'trang_thai'    => $request->boolean('trang_thai', true),
        ]);

        $codes = $request->input('quyen_han', $defaultPerms);
        $chucNangIds = ChucNang::whereIn('ma_chuc_nang', (array) $codes)->pluck('id')->toArray();
        $nv->chucNangs()->sync($chucNangIds);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm nhân viên thành công',
            'data'    => $nv,
        ], 201);
    }

    public function update(Request $request)
    {
        $nv = User::whereIn('vai_tro', ['admin', 'staff'])->find($request->id);
        if (!$nv) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy nhân viên'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ho_ten'        => 'required|string|max:100',
            'email'         => 'required|email|unique:nguoi_dung,email,' . $nv->id,
            'so_dien_thoai' => 'nullable|string|max:20|unique:nguoi_dung,so_dien_thoai,' . $nv->id,
            'vai_tro'       => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $nv->ho_ten        = trim($request->ho_ten);
        $nv->email         = trim($request->email);
        $nv->so_dien_thoai = $request->filled('so_dien_thoai') ? trim($request->so_dien_thoai) : null;
        $nv->vai_tro       = $request->vai_tro;
        $nv->dia_chi       = $request->dia_chi;
        if ($request->filled('password')) {
            $nv->password = Hash::make($request->password);
        }
        $nv->save();
        if ($request->has('quyen_han')) {
            $chucNangIds = ChucNang::whereIn('ma_chuc_nang', (array) $request->quyen_han)->pluck('id')->toArray();
            $nv->chucNangs()->sync($chucNangIds);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật nhân viên thành công',
            'data'    => $nv,
        ]);
    }

    public function destroy(Request $request)
    {
        $nv = User::whereIn('vai_tro', ['admin', 'staff'])->find($request->id);
        if (!$nv) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy nhân viên'], 404);
        }
        if ($nv->vai_tro === 'admin' && User::where('vai_tro', 'admin')->count() <= 1) {
            return response()->json(['status' => false, 'message' => 'Không thể xóa Admin duy nhất của hệ thống!'], 400);
        }
        $nv->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa nhân viên thành công']);
    }

    public function changeStatus(Request $request)
    {
        $nv = User::whereIn('vai_tro', ['admin', 'staff'])->find($request->id);
        if (!$nv) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy nhân viên'], 404);
        }
        $nv->trang_thai = !$nv->trang_thai;
        $nv->save();
        return response()->json([
            'status'     => true,
            'message'    => 'Đổi trạng thái nhân viên thành công',
            'trang_thai' => $nv->trang_thai,
        ]);
    }

    public function updatePermissions(Request $request)
    {
        $nv = User::whereIn('vai_tro', ['admin', 'staff'])->find($request->id);
        if (!$nv) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy tài khoản nhân viên'], 404);
        }

        if ($request->has('chuc_nang_ids')) {
            $ids = array_map('intval', (array) $request->input('chuc_nang_ids', []));
        } else {
            $codes = (array) $request->input('quyen_han', []);
            $ids = ChucNang::whereIn('ma_chuc_nang', $codes)->pluck('id')->toArray();
        }

        $nv->chucNangs()->sync($ids);

        return response()->json([
            'status'    => true,
            'message'   => 'Cập nhật phân quyền thành công cho ' . $nv->ho_ten,
            'quyen_han' => $nv->getPermissionCodes(),
            'data'      => $nv,
        ]);
    }
}
