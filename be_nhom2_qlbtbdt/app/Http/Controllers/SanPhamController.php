<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\ThongSoKyThuat;
use App\Models\BienTheSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SanPhamController extends Controller
{
    // ==========================================
    // 1. ADMIN - QUẢN LÝ SẢN PHẨM
    // ==========================================
    public function getData()
    {
        $data = SanPham::with(['category', 'brand', 'specification', 'variants'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu sản phẩm thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'ten_san_pham'   => 'required|string|max:255',
            'danh_muc_id'    => 'required|integer',
            'thuong_hieu_id' => 'required|integer',
            'gia_goc'        => 'required|numeric|min:1000',
        ], [
            'ten_san_pham.required' => 'Vui lòng nhập tên sản phẩm',
            'gia_goc.required'      => 'Vui lòng nhập giá bán niêm yết',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $sp = SanPham::create([
            'danh_muc_id'        => (int) $request->danh_muc_id,
            'thuong_hieu_id'     => (int) $request->thuong_hieu_id,
            'ten_san_pham'       => trim($request->ten_san_pham),
            'slug'               => \Illuminate\Support\Str::slug($request->ten_san_pham) . '-' . rand(100, 999),
            'ma_san_pham'        => $request->ma_san_pham ?: ('SP-' . strtoupper(\Illuminate\Support\Str::random(6))),
            'anh_chinh'          => $request->anh_chinh ?: 'https://via.placeholder.com/300',
            'gia_goc'            => (float) $request->gia_goc,
            'gia_khuyen_mai'     => $request->filled('gia_khuyen_mai') ? (float) $request->gia_khuyen_mai : null,
            'thoi_gian_bao_hanh' => (int) ($request->thoi_gian_bao_hanh ?? 24),
            'mo_ta_ngan'         => $request->mo_ta_ngan,
            'noi_dung_chi_tiet'  => $request->noi_dung_chi_tiet,
            'noi_bat'            => $request->boolean('noi_bat', false),
            'trang_thai'         => $request->boolean('trang_thai', true),
        ]);

        // Tạo biến thể mặc định
        BienTheSanPham::create([
            'san_pham_id'   => $sp->id,
            'ten_bien_the'  => $request->ten_bien_the ?: 'Phiên bản tiêu chuẩn',
            'ma_sku'        => $sp->ma_san_pham . '-STD',
            'gia_ban'       => (float) ($sp->gia_khuyen_mai ?: $sp->gia_goc),
            'gia_nhap'      => (float) ($request->gia_nhap ?? ($sp->gia_goc * 0.8)),
            'so_luong_ton'  => (int) ($request->so_luong_ton ?? 10),
            'trang_thai'    => true,
        ]);

        // Tạo thông số kỹ thuật nếu có
        ThongSoKyThuat::create([
            'san_pham_id' => $sp->id,
            'cpu'         => $request->cpu,
            'ram'         => $request->ram,
            'o_cung'      => $request->o_cung,
            'card_do_hoa' => $request->card_do_hoa,
            'man_hinh'    => $request->man_hinh,
            'pin'         => $request->pin,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm sản phẩm mới thành công!',
            'data'    => $sp->load(['category', 'brand', 'variants', 'specification']),
        ], 201);
    }

    public function update(Request $request)
    {
        $sp = SanPham::find($request->id);
        if (!$sp) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $sp->update([
            'danh_muc_id'        => $request->danh_muc_id ?: $sp->danh_muc_id,
            'thuong_hieu_id'     => $request->thuong_hieu_id ?: $sp->thuong_hieu_id,
            'ten_san_pham'       => $request->ten_san_pham ? trim($request->ten_san_pham) : $sp->ten_san_pham,
            'ma_san_pham'        => $request->ma_san_pham ?: $sp->ma_san_pham,
            'anh_chinh'          => $request->filled('anh_chinh') ? $request->anh_chinh : $sp->anh_chinh,
            'gia_goc'            => $request->filled('gia_goc') ? (float) $request->gia_goc : $sp->gia_goc,
            'gia_khuyen_mai'     => $request->filled('gia_khuyen_mai') ? (float) $request->gia_khuyen_mai : null,
            'thoi_gian_bao_hanh' => $request->filled('thoi_gian_bao_hanh') ? (int) $request->thoi_gian_bao_hanh : $sp->thoi_gian_bao_hanh,
            'mo_ta_ngan'         => $request->mo_ta_ngan ?? $sp->mo_ta_ngan,
        ]);

        // Cập nhật thông số kỹ thuật nếu truyền
        $spec = ThongSoKyThuat::firstOrNew(['san_pham_id' => $sp->id]);
        foreach (['cpu', 'ram', 'o_cung', 'card_do_hoa', 'man_hinh', 'pin'] as $f) {
            if ($request->has($f)) $spec->$f = $request->input($f);
        }
        $spec->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật sản phẩm thành công!',
            'data'    => $sp->load(['category', 'brand', 'variants', 'specification']),
        ]);
    }

    public function destroy(Request $request)
    {
        $sp = SanPham::find($request->id);
        if (!$sp) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
        }
        $sp->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa sản phẩm thành công']);
    }

    public function changeStatus(Request $request)
    {
        $sp = SanPham::find($request->id);
        if (!$sp) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
        }
        $sp->trang_thai = !$sp->trang_thai;
        $sp->save();
        return response()->json([
            'status'     => true,
            'message'    => 'Đổi trạng thái sản phẩm thành công',
            'trang_thai' => $sp->trang_thai,
        ]);
    }

    // ==========================================
    // 2. CLIENT - SẢN PHẨM & TÌM KIẾM ĐA TIÊU CHÍ (UC-SEARCH)
    // ==========================================
    public function getDataClient(Request $request)
    {
        $query = SanPham::with(['category', 'brand', 'specification', 'variants'])
            ->where('trang_thai', 1);

        // Tìm kiếm nhanh theo từ khóa nếu có truyền q
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_san_pham', 'like', "%{$keyword}%")
                  ->orWhere('ma_san_pham', 'like', "%{$keyword}%")
                  ->orWhere('mo_ta_ngan', 'like', "%{$keyword}%")
                  ->orWhereHas('brand', function ($b) use ($keyword) {
                      $b->where('ten_thuong_hieu', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('category', function ($c) use ($keyword) {
                      $c->where('ten_danh_muc', 'like', "%{$keyword}%");
                  });
            });
        }

        $data = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu sản phẩm client thành công',
            'total'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * Bộ lọc tìm kiếm đa tiêu chí nâng cao (UC-SEARCH & MULTI-FILTER)
     */
    public function filterClient(Request $request)
    {
        $query = SanPham::with(['category', 'brand', 'specification', 'variants'])
            ->where('trang_thai', 1);

        // 1. Tìm kiếm từ khóa (Search Keyword)
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_san_pham', 'like', "%{$keyword}%")
                  ->orWhere('ma_san_pham', 'like', "%{$keyword}%")
                  ->orWhere('mo_ta_ngan', 'like', "%{$keyword}%")
                  ->orWhereHas('brand', function ($b) use ($keyword) {
                      $b->where('ten_thuong_hieu', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('category', function ($c) use ($keyword) {
                      $c->where('ten_danh_muc', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('specification', function ($s) use ($keyword) {
                      $s->where('cpu', 'like', "%{$keyword}%")
                        ->orWhere('card_do_hoa', 'like', "%{$keyword}%")
                        ->orWhere('ram', 'like', "%{$keyword}%");
                  });
            });
        }

        // 2. Lọc theo Danh mục (Category)
        if ($request->filled('danh_muc_id')) {
            $catIds = is_array($request->danh_muc_id) 
                ? $request->danh_muc_id 
                : explode(',', $request->danh_muc_id);
            $query->whereIn('danh_muc_id', $catIds);
        }

        // 3. Lọc theo Thương hiệu (Brand)
        if ($request->filled('thuong_hieu_id')) {
            $brandIds = is_array($request->thuong_hieu_id) 
                ? $request->thuong_hieu_id 
                : explode(',', $request->thuong_hieu_id);
            $query->whereIn('thuong_hieu_id', $brandIds);
        }

        // 4. Lọc theo Khoảng giá (Price Range - xét giá thực tế ưu tiên giá khuyến mãi)
        if ($request->filled('gia_min')) {
            $giaMin = (float) $request->gia_min;
            $query->where(function ($q) use ($giaMin) {
                $q->where(function ($sub) use ($giaMin) {
                    $sub->whereNotNull('gia_khuyen_mai')->where('gia_khuyen_mai', '>=', $giaMin);
                })->orWhere(function ($sub) use ($giaMin) {
                    $sub->whereNull('gia_khuyen_mai')->where('gia_goc', '>=', $giaMin);
                });
            });
        }

        if ($request->filled('gia_max')) {
            $giaMax = (float) $request->gia_max;
            $query->where(function ($q) use ($giaMax) {
                $q->where(function ($sub) use ($giaMax) {
                    $sub->whereNotNull('gia_khuyen_mai')->where('gia_khuyen_mai', '<=', $giaMax);
                })->orWhere(function ($sub) use ($giaMax) {
                    $sub->whereNull('gia_khuyen_mai')->where('gia_goc', '<=', $giaMax);
                });
            });
        }

        // 5. Lọc theo RAM (8GB, 16GB, 32GB, 64GB)
        if ($request->filled('ram')) {
            $ramVal = $request->ram;
            $query->whereHas('specification', function ($s) use ($ramVal) {
                $s->where('ram', 'like', "%{$ramVal}%");
            });
        }

        // 6. Lọc theo CPU (Intel Core i5, i7, i9, AMD Ryzen, Apple M)
        if ($request->filled('cpu')) {
            $cpuVal = $request->cpu;
            $query->whereHas('specification', function ($s) use ($cpuVal) {
                $s->where('cpu', 'like', "%{$cpuVal}%");
            });
        }

        // 7. Lọc theo Card đồ họa / GPU (RTX 4050, 4060, 4070, 4080, Onboard)
        if ($request->filled('card_do_hoa')) {
            $gpuVal = $request->card_do_hoa;
            $query->whereHas('specification', function ($s) use ($gpuVal) {
                $s->where('card_do_hoa', 'like', "%{$gpuVal}%");
            });
        }

        // 8. Sắp xếp kết quả (Sorting)
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(gia_khuyen_mai, gia_goc) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(gia_khuyen_mai, gia_goc) DESC');
                break;
            case 'views':
                $query->orderBy('luot_xem', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('ten_san_pham', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $data = $query->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lọc sản phẩm thành công',
            'count'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * Chi tiết sản phẩm kèm cấu hình và đánh giá
     */
    public function chiTietSanPham($id)
    {
        $sanPham = SanPham::with([
            'category',
            'brand',
            'images',
            'specification',
            'variants',
            'reviews' => function ($q) {
                $q->where('trang_thai_duyet', true)->with('user:id,ho_ten,anh_dai_dien')->latest();
            }
        ])
        ->where('trang_thai', 1)
        ->where('id', $id)
        ->first();

        if (!$sanPham) {
            return response()->json([
                'status'  => false,
                'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh',
            ], 404);
        }

        // Tăng lượt xem
        $sanPham->increment('luot_xem');

        // Tính toán thông kê đánh giá
        $reviews = $sanPham->reviews;
        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('so_sao'), 1) : 5.0;

        return response()->json([
            'status'  => true,
            'message' => 'Lấy chi tiết sản phẩm thành công',
            'data'    => $sanPham,
            'rating'  => [
                'average' => $avgRating,
                'total'   => $totalReviews,
            ]
        ]);
    }

    // ==========================================
    // 3. SO SÁNH THÔNG SỐ KỸ THUẬT SẢN PHẨM (UC-COMPARE)
    // ==========================================
    public function compareClient(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        $ids = array_filter(array_map('intval', (array) $ids));

        if (empty($ids)) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng chọn ít nhất một sản phẩm để so sánh!',
                'data'    => []
            ], 422);
        }

        // Giới hạn tối đa 4 sản phẩm so sánh đồng thời
        $ids = array_slice($ids, 0, 4);

        // Lấy thông tin các sản phẩm cần so sánh kèm thông số kỹ thuật và thương hiệu
        $products = SanPham::with(['category', 'brand', 'specification', 'variants', 'images'])
            ->whereIn('id', $ids)
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy sản phẩm nào phù hợp!',
                'data'    => []
            ], 404);
        }

        // Phân loại danh mục chính của sản phẩm đầu tiên để xây dựng ma trận đặc tả chuẩn xác
        $firstProduct = $products->first();
        $catId = $firstProduct->danh_muc_id;
        $catName = strtolower($firstProduct->category->ten_danh_muc ?? ($firstProduct->danhMuc->ten_danh_muc ?? ''));
        $prodName = strtolower($firstProduct->ten_san_pham ?? '');

        // Kiểm tra tính đồng nhất danh mục
        $categoryIds = $products->pluck('danh_muc_id')->unique();
        $isSameCategory = ($categoryIds->count() === 1);

        // Xác định loại thiết bị: 'laptop', 'monitor', 'ram', 'ssd', 'cpu', 'vga', 'gear'
        $type = 'laptop';
        if ($catId == 2 || str_contains($catName, 'màn hình') || str_contains($prodName, 'màn hình')) {
            $type = 'monitor';
        } elseif (str_contains($prodName, 'ram ') || str_contains($prodName, 'ddr4') || str_contains($prodName, 'ddr5')) {
            $type = 'ram';
        } elseif (str_contains($prodName, 'ssd ') || str_contains($prodName, 'nvme') || str_contains($prodName, 'samsung 990') || str_contains($prodName, 'kingston nv2')) {
            $type = 'ssd';
        } elseif ($catId == 4 || str_contains($catName, 'gear') || str_contains($prodName, 'chuột') || str_contains($prodName, 'bàn phím')) {
            $type = 'gear';
        }

        // Cấu hình các trường đặc tả phù hợp với từng chuẩn thiết bị
        $hardwareFields = [];
        if ($type === 'monitor') {
            $hardwareFields = [
                'man_hinh'      => 'Kích thước hiển thị',
                'do_phan_giai'  => 'Độ phân giải hiển thị',
                'tan_so_quet'   => 'Tần số quét màn hình',
                'tam_nen_mau'   => 'Tấm nền & Độ phủ màu',
                'cong_ket_noi'  => 'Cổng giao tiếp xuất hình',
                'trong_luong'   => 'Trọng lượng (kèm chân đế)',
            ];
        } elseif ($type === 'ram') {
            $hardwareFields = [
                'ram'           => 'Dung lượng & Chuẩn RAM',
                'bus_ram'       => 'Tốc độ Bus RAM',
                'tan_nhiet_led' => 'Tản nhiệt & Đèn LED RGB',
                'cong_ket_noi'  => 'Chuẩn chân cắm (Socket)',
                'trong_luong'   => 'Trọng lượng thanh RAM',
            ];
        } elseif ($type === 'ssd') {
            $hardwareFields = [
                'o_cung'        => 'Dung lượng lưu trữ',
                'chuan_giao_tiep' => 'Chuẩn giao tiếp (Interface)',
                'toc_do_doc'    => 'Tốc độ đọc tuần tự',
                'toc_do_ghi'    => 'Tốc độ ghi tuần tự',
                'tbw'           => 'Độ bền ghi xóa (TBW)',
                'cong_ket_noi'  => 'Form Factor (Kích thước)',
            ];
        } elseif ($type === 'gear') {
            $hardwareFields = [
                'cam_bien_switch' => 'Cảm biến DPI / Loại Switch',
                'cong_ket_noi'  => 'Kiểu kết nối',
                'pin'           => 'Thời lượng Pin / Nguồn',
                'trong_luong'   => 'Trọng lượng thiết bị',
            ];
        } else {
            // Mặc định: Laptop & Máy tính để bàn
            $hardwareFields = [
                'cpu'           => 'Bộ vi xử lý (CPU)',
                'card_do_hoa'   => 'Card đồ họa (GPU)',
                'ram'           => 'Dung lượng RAM',
                'o_cung'        => 'Ổ cứng lưu trữ (SSD)',
                'man_hinh'      => 'Kích thước màn hình',
                'do_phan_giai'  => 'Độ phân giải & Tần số quét',
                'pin'           => 'Dung lượng Pin & Sạc',
                'trong_luong'   => 'Trọng lượng máy',
                'he_dieu_hanh'  => 'Hệ điều hành cài sẵn',
                'cong_ket_noi'  => 'Cổng kết nối ngoại vi',
            ];
        }

        // Chuẩn hóa ma trận so sánh theo tiêu chuẩn kỹ thuật
        $comparison = [
            'products'         => $products,
            'category_type'    => $type,
            'is_same_category' => $isSameCategory,
            'specs_matrix'     => [
                'thong_tin_co_ban' => [
                    'label'  => 'Thông tin chung & Bảo hành',
                    'fields' => [
                        'ten_san_pham' => 'Tên thiết bị',
                        'thuong_hieu'  => 'Thương hiệu',
                        'danh_muc'     => 'Danh mục sản phẩm',
                        'gia_ban'      => 'Giá bán tốt nhất',
                        'gia_goc'      => 'Giá niêm yết',
                        'bao_hanh'     => 'Thời gian bảo hành',
                        'tinh_trang'   => 'Trạng thái kho hàng',
                    ]
                ],
                'cau_hinh_phan_cung' => [
                    'label'  => 'Thông số kỹ thuật chuyên biệt (' . strtoupper($type) . ')',
                    'fields' => $hardwareFields
                ]
            ]
        ];

        return response()->json([
            'status'       => true,
            'message'      => 'Lấy dữ liệu so sánh thông số kỹ thuật thành công',
            'count'        => $products->count(),
            'products'     => $products,
            'specs_matrix' => $comparison['specs_matrix'],
            'data'         => $comparison
        ]);
    }
}
