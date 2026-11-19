<?php

use App\Http\Controllers\BaoHanhController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\DanhMucController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\ThuongHieuController;
use App\Http\Controllers\TrangChuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// ĐĂNG NHẬP / PUBLIC
// ==========================================
Route::prefix('')->group(function () {
    Route::post('/admin/dang-nhap', [NhanVienController::class, 'dangNhap']);
    Route::get('/admin/check-token', [NhanVienController::class, 'checkToken']);

    Route::post('/client/dang-nhap', [KhachHangController::class, 'dangNhap']);
    Route::post('/client/dang-ky', [KhachHangController::class, 'dangKy']);
    Route::post('/client/dang-xuat', [KhachHangController::class, 'dangXuat']);
    Route::get('/client/check-token', [KhachHangController::class, 'checkToken']);
    Route::get('/client/trang-chu/get-data', [TrangChuController::class, 'getData']);
    Route::get('/client/trang-chu/get-tat-ca-san-pham', [TrangChuController::class, 'getTatCaSanPham']);
});

// ==========================================
// 1. ROUTE ADMIN (QUẢN TRỊ VIÊN)
// ==========================================
Route::prefix('')->group(function () {
    // CHECK TOKEN ADMIN
    Route::group(['prefix' => '/admin'], function () {
        //Nhân viên
        Route::get('nhan-vien/get-data', [NhanVienController::class, 'getData']);
        Route::post('nhan-vien/add-data', [NhanVienController::class, 'addData']);
        Route::post('nhan-vien/update', [NhanVienController::class, 'update']);
        Route::post('nhan-vien/delete', [NhanVienController::class, 'destroy']);
        Route::post('nhan-vien/change-status', [NhanVienController::class, 'changeStatus']);

        //Khách hàng
        Route::get('khach-hang/get-data', [KhachHangController::class, 'getData']);
        Route::post('khach-hang/change-status', [KhachHangController::class, 'changeStatus']);

        //Danh mục
        Route::get('danh-muc/get-data', [DanhMucController::class, 'getData']);
        Route::post('danh-muc/add-data', [DanhMucController::class, 'addData']);
        Route::post('danh-muc/update', [DanhMucController::class, 'update']);
        Route::post('danh-muc/delete', [DanhMucController::class, 'destroy']);
        Route::post('danh-muc/change-status', [DanhMucController::class, 'changeStatus']);

        //Thương hiệu
        Route::get('thuong-hieu/get-data', [ThuongHieuController::class, 'getData']);
        Route::post('thuong-hieu/add-data', [ThuongHieuController::class, 'addData']);
        Route::post('thuong-hieu/update', [ThuongHieuController::class, 'update']);
        Route::post('thuong-hieu/delete', [ThuongHieuController::class, 'destroy']);
        Route::post('thuong-hieu/change-status', [ThuongHieuController::class, 'changeStatus']);

        //Sản phẩm
        Route::get('san-pham/get-data', [SanPhamController::class, 'getData']);
        Route::post('san-pham/add-data', [SanPhamController::class, 'addData']);
        Route::post('san-pham/update', [SanPhamController::class, 'update']);
        Route::post('san-pham/delete', [SanPhamController::class, 'destroy']);
        Route::post('san-pham/change-status', [SanPhamController::class, 'changeStatus']);

        //Đơn hàng
        Route::get('don-hang/get-data', [DonHangController::class, 'getData']);
        Route::post('don-hang/change-status', [DonHangController::class, 'changeStatus']);

        //Thống kê
        Route::get('thong-ke/kpi-dashboard', [ThongKeController::class, 'kpiDashboard']);

        //Giám sát & Xu hướng Trợ lý AI
        Route::get('ai/analytics', [ChatBotController::class, 'adminAnalytics']);

        //Quản lý & Đối soát Giao dịch Thanh toán (PayOS / VNPAY / COD)
        Route::get('thanh-toan/get-data', [ThanhToanController::class, 'getAdminTransactions']);
        Route::post('thanh-toan/doi-soat', [ThanhToanController::class, 'reconcilePayOs']);
    });
});

// ==========================================
// 2. ROUTE CLIENT (NGƯỜI DÙNG / KHÁCH HÀNG)
// ==========================================
Route::prefix('')->group(function () {
    Route::group(['prefix' => '/client'], function () {
        //Trang chủ
        Route::get('trang-chu/get-data', [TrangChuController::class, 'getData']);
        Route::get('trang-chu/get-tat-ca-san-pham', [TrangChuController::class, 'getTatCaSanPham']);

        //Sản phẩm
        Route::get('san-pham/get-data', [SanPhamController::class, 'getDataClient']);
        Route::get('san-pham/filter', [SanPhamController::class, 'filterClient']);
        Route::get('chi-tiet-san-pham/{id}', [SanPhamController::class, 'chiTietSanPham']);
        Route::post('san-pham/compare', [SanPhamController::class, 'compareClient']);

        //Danh mục & Thương hiệu
        Route::get('danh-muc/get-data', [DanhMucController::class, 'getDataClient']);
        Route::get('thuong-hieu/get-data', [ThuongHieuController::class, 'getDataClient']);

        //Giỏ hàng
        Route::get('gio-hang/get-data', [GioHangController::class, 'getData']);
        Route::post('gio-hang/them-vao-gio', [GioHangController::class, 'themVaoGio']);
        Route::post('gio-hang/update', [GioHangController::class, 'update']);
        Route::post('gio-hang/delete', [GioHangController::class, 'destroy']);
        Route::post('gio-hang/clear', [GioHangController::class, 'clear']);

        //Đơn hàng
        Route::post('don-hang/checkout', [DonHangController::class, 'checkout']);
        Route::get('don-hang/danh-sach', [DonHangController::class, 'getDonHangClient']);

        //Bảo hành
        Route::get('tra-cuu-bao-hanh', [BaoHanhController::class, 'traCuu']);

        //ChatBot AI
        Route::post('chat-ai', [ChatBotController::class, 'chatWithAI']);
        Route::get('chat-ai/history', [ChatBotController::class, 'getHistory']);
        Route::post('chat-ai/clear-session', [ChatBotController::class, 'clearSession']);
    });
});

// ==========================================
// 3. ROUTE CỔNG THANH TOÁN PAYOS & VNPAY
// ==========================================
Route::prefix('/payment/payos')->group(function () {
    Route::get('/verify/{orderCode}', [ThanhToanController::class, 'verifyPayOs']);
    Route::post('/webhook', [ThanhToanController::class, 'payOsWebhook']);
});

Route::prefix('/payment/vnpay')->group(function () {
    Route::get('/verify', [ThanhToanController::class, 'verifyVnPay']);
    Route::get('/ipn', [ThanhToanController::class, 'vnpayIpn']);
});
