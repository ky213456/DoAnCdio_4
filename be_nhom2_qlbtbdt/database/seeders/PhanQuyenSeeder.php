<?php

namespace Database\Seeders;

use App\Models\ChucNang;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PhanQuyenSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('phan_quyen')->delete();
        Schema::enableForeignKeyConstraints();

        $chucNangMap = ChucNang::pluck('id', 'ma_chuc_nang');

        $rolePermissions = [
            'admin@gmail.com'      => ['UC09', 'UC10', 'UC11', 'UC12', 'UC13', 'UC14', 'UC15', 'UC16', 'UC19', 'UC22', 'UC23'],
            'staff@gmail.com'      => ['UC12', 'UC13', 'UC14', 'UC15', 'UC16'],
            'banhang@gmail.com'    => ['UC12', 'UC13', 'UC14', 'UC15', 'UC16'],
            'khobaohanh@gmail.com' => ['UC22', 'UC23', 'UC19'],
        ];

        foreach ($rolePermissions as $email => $codes) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                continue;
            }
            $ids = [];
            foreach ($codes as $code) {
                if (isset($chucNangMap[$code])) {
                    $ids[] = $chucNangMap[$code];
                }
            }
            $user->chucNangs()->sync($ids);
        }
    }
}
