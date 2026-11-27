<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhToanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thanh_toan')->delete();

        $thanhToans = [
            // Thanh toán đơn 1: VNPAY thành công
            [
                'id' => 1,
                'don_hang_id' => 1,
                'ma_giao_dich' => 'VNPAY_1730419200',
                'cong_thanh_toan' => 'vnpay',
                'so_tien' => 5490000.00,
                'loai_tien' => 'VND',
                'trang_thai' => 'success',
                'ma_phan_hoi_cong' => '00',
                'du_lieu_phan_hoi' => json_encode(['vnp_ResponseCode' => '00', 'vnp_BankCode' => 'NCB', 'vnp_CardType' => 'ATM']),
                'thoi_gian_thanh_toan' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],

            // Thanh toán đơn 2: PayOS thành công
            [
                'id' => 2,
                'don_hang_id' => 2,
                'ma_giao_dich' => 'PAYOS_1730505600',
                'cong_thanh_toan' => 'payos',
                'so_tien' => 34990000.00,
                'loai_tien' => 'VND',
                'trang_thai' => 'success',
                'ma_phan_hoi_cong' => 'PAID',
                'du_lieu_phan_hoi' => json_encode(['status' => 'PAID', 'paymentLinkId' => 'link_rog_01']),
                'thoi_gian_thanh_toan' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],

            // Thanh toán đơn 3: COD đang chờ nhận tiền khi giao
            [
                'id' => 3,
                'don_hang_id' => 3,
                'ma_giao_dich' => 'COD_1730592000',
                'cong_thanh_toan' => 'cod',
                'so_tien' => 3170000.00,
                'loai_tien' => 'VND',
                'trang_thai' => 'pending',
                'ma_phan_hoi_cong' => null,
                'du_lieu_phan_hoi' => null,
                'thoi_gian_thanh_toan' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],

            // Thanh toán đơn 4: VNPAY thành công
            [
                'id' => 4,
                'don_hang_id' => 4,
                'ma_giao_dich' => 'VNPAY_1730678400',
                'cong_thanh_toan' => 'vnpay',
                'so_tien' => 2520000.00,
                'loai_tien' => 'VND',
                'trang_thai' => 'success',
                'ma_phan_hoi_cong' => '00',
                'du_lieu_phan_hoi' => json_encode(['vnp_ResponseCode' => '00', 'vnp_BankCode' => 'MBBANK']),
                'thoi_gian_thanh_toan' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('thanh_toan')->insert($thanhToans);
    }
}
