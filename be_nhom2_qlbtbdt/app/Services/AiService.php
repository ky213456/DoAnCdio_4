<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Xử lý câu hỏi của khách hàng dựa hoàn toàn trên CSDL thực tế của dự án (RAG + Strict Guardrails)
     */
    public function askDatabaseGroundedAi(string $userMessage, array $recentHistory = []): array
    {
        $apiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        $model  = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-2.5-flash');

        if (empty($apiKey)) {
            return [
                'success'            => false,
                'reply'              => 'Hệ thống chưa cấu hình GEMINI_API_KEY trong file .env.',
                'suggested_products' => [],
                'tokens'             => 0,
            ];
        }

        // 1. Trích xuất ràng buộc lọc (giá, danh mục) từ câu hỏi & ngữ cảnh CSDL
        $constraints = $this->extractQueryConstraints($userMessage);
        $dbContext   = $this->buildDatabaseContext($userMessage, $constraints);

        // 2. Xây dựng System Instruction khóa chặt phạm vi trong CSDL TechMart
        $systemInstruction = $this->buildSystemInstruction($dbContext);

        // 3. Chuẩn bị lịch sử hội thoại đa lượt (Multi-turn context)
        $contents = [];
        foreach ($recentHistory as $msg) {
            $role = ($msg['vai_tro'] ?? 'user') === 'assistant' ? 'model' : 'user';
            $text = trim((string) ($msg['noi_dung'] ?? ''));
            if ($text !== '') {
                $contents[] = [
                    'role'  => $role,
                    'parts' => [['text' => $text]],
                ];
            }
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        // 4. Gọi Google Gemini API với temperature thấp (0.15) để chống ảo giác (Hallucination)
        $candidateModels = array_values(array_unique([
            $model,
            'gemini-3.1-flash-lite',
            'gemini-3.8-flash',
            'gemini-4-argon',
            'gemini-4-hydrogen',
            'gemma-4-26b-a4b-it',
        ]));

        try {
            $response = null;
            $lastException = null;

            foreach ($candidateModels as $candidateModel) {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:generateContent?key={$apiKey}";

                $generationConfig = [
                    'temperature'     => 0.15,
                    'topP'            => 0.85,
                    'maxOutputTokens' => 2048,
                ];

                if (str_contains($candidateModel, '2.5')) {
                    $generationConfig['thinkingConfig'] = [
                        'thinkingBudget' => 128,
                    ];
                }

                $payload = [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents'          => $contents,
                    'generationConfig'  => $generationConfig,
                ];

                try {
                    $response = Http::withoutVerifying()
                        ->timeout(20)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($endpoint, $payload);

                    if ($response->successful()) {
                        $lastException = null;
                        break;
                    }

                    Log::warning('Gemini API Fallback', [
                        'model'  => $candidateModel,
                        'status' => $response->status(),
                    ]);

                    if (!in_array($response->status(), [404, 429, 503], true)) {
                        break;
                    }
                } catch (\Throwable $e) {
                    $lastException = $e;
                    Log::warning('Gemini API Timeout/Exception Fallback', [
                        'model' => $candidateModel,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($lastException && (!$response || !$response->successful())) {
                throw $lastException;
            }

            if (!$response || !$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response?->status(),
                    'body'   => $response?->body(),
                ]);

                return [
                    'success'            => false,
                    'reply'              => 'Trợ lý AI đang tạm thời gián đoạn kết nối máy chủ. Vui lòng thử lại sau giây lát!',
                    'suggested_products' => [],
                    'tokens'             => 0,
                ];
            }

            $json = $response->json();
            $rawReply = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $totalTokens = (int) ($json['usageMetadata']['totalTokenCount'] ?? 0);

            if (trim($rawReply) === '') {
                $rawReply = 'Dạ hiện em chưa tìm thấy thông tin phù hợp trong cơ sở dữ liệu TechMart cho yêu cầu này ạ.';
            }

            // 5. Tách mọi thẻ [[SP_IDS:1,2]] để lấy danh sách sản phẩm đề xuất từ CSDL
            $suggestedIds = [];
            if (preg_match_all('/`?\[\[SP_IDS:\s*([0-9,\s]+)\s*\]\]`?/i', $rawReply, $matches)) {
                foreach ($matches[1] as $idsStr) {
                    foreach (explode(',', $idsStr) as $rawId) {
                        $idInt = (int) trim($rawId);
                        if ($idInt > 0 && !in_array($idInt, $suggestedIds, true)) {
                            $suggestedIds[] = $idInt;
                        }
                    }
                }
                $rawReply = trim(preg_replace('/`?\[\[SP_IDS:\s*[0-9,\s]+\s*\]\]`?/i', '', $rawReply));
            }

            // Lọc hậu kiểm trong PHP để đảm bảo card sản phẩm đề xuất 100% khớp điều kiện giá/danh mục của khách
            $suggestedIds = $this->filterSuggestedIdsByConstraints($suggestedIds, $constraints);

            $suggestedProducts = $this->fetchSuggestedProductCards(array_slice($suggestedIds, 0, 3));

            return [
                'success'            => true,
                'reply'              => $rawReply,
                'suggested_products' => $suggestedProducts,
                'suggested_ids'      => $suggestedIds,
                'tokens'             => $totalTokens,
            ];
        } catch (\Throwable $e) {
            Log::error('AiService Exception: ' . $e->getMessage());

            return [
                'success'            => false,
                'reply'              => 'Đã xảy ra lỗi kết nối khi truy xuất Trợ lý AI. Vui lòng thử lại!',
                'suggested_products' => [],
                'tokens'             => 0,
            ];
        }
    }

    /**
     * Trích xuất điều kiện lọc (giá tối đa/tối thiểu, danh mục) từ câu hỏi người dùng
     */
    protected function extractQueryConstraints(string $userMessage): array
    {
        $msg = mb_strtolower($userMessage, 'UTF-8');
        $minPrice = null;
        $maxPrice = null;
        $categoryKeywords = [];

        // Khoảng giá: "từ 15 đến 25 triệu", "15-25tr"
        if (preg_match('/(?:từ\s*)?(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|m)?\s*(?:đến|tới|-|~)\s*(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|m)/u', $msg, $m)) {
            $minPrice = (float) str_replace(',', '.', $m[1]) * 1000000;
            $maxPrice = (float) str_replace(',', '.', $m[2]) * 1000000;
        } else {
            // Giá tối đa: "dưới 20 triệu", "dưới 20tr", "tối đa 20 triệu", "không quá 20tr", "< 20 triệu"
            if (preg_match('/(?:dưới|nhỏ hơn|tối đa|không quá|<=|<|trở xuống)\s*(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|m)?/u', $msg, $m) ||
                preg_match('/(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|m)\s*(?:trở xuống|đổ lại)/u', $msg, $m)) {
                $val = (float) str_replace(',', '.', $m[1]);
                $maxPrice = $val < 1000 ? $val * 1000000 : $val;
            }
            // Giá tối thiểu: "trên 20 triệu", "từ 20 triệu trở lên", "> 20tr"
            if (preg_match('/(?:trên|lớn hơn|từ|>=|>)\s*(\d+(?:[.,]\d+)?)\s*(?:triệu|tr|m)/u', $msg, $m)) {
                $val = (float) str_replace(',', '.', $m[1]);
                $minPrice = $val < 1000 ? $val * 1000000 : $val;
            }
        }

        // Nhận diện danh mục rõ ràng
        if (preg_match('/\b(laptop|máy tính xách tay|macbook|ultrabook|thinkpad|zenbook)\b/u', $msg)) {
            $categoryKeywords[] = 'laptop';
        } elseif (preg_match('/(màn hình|monitor)/u', $msg)) {
            $categoryKeywords[] = 'màn hình';
        } elseif (preg_match('/\b(chuột|bàn phím|tai nghe|gaming gear|phụ kiện)\b/u', $msg)) {
            $categoryKeywords[] = 'gear';
        } elseif (preg_match('/\b(cpu|vi xử lý|ram|ssd|ổ cứng|vga|card đồ họa|card màn hình|linh kiện)\b/u', $msg)) {
            $categoryKeywords[] = 'linh kiện';
        }

        return [
            'min_price'         => $minPrice,
            'max_price'         => $maxPrice,
            'category_keywords' => $categoryKeywords,
        ];
    }

    /**
     * Lọc hậu kiểm danh sách ID đề xuất để đảm bảo không vượt ngân sách hoặc sai danh mục
     */
    protected function filterSuggestedIdsByConstraints(array $ids, array $constraints): array
    {
        if (empty($ids)) {
            return [];
        }

        $minPrice = $constraints['min_price'] ?? null;
        $maxPrice = $constraints['max_price'] ?? null;
        $catKws   = $constraints['category_keywords'] ?? [];

        if ($minPrice === null && $maxPrice === null && empty($catKws)) {
            return $ids;
        }

        $rows = DB::table('san_pham')
            ->leftJoin('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->whereIn('san_pham.id', $ids)
            ->select('san_pham.id', 'san_pham.gia_goc', 'san_pham.gia_khuyen_mai', 'danh_muc.ten_danh_muc', 'san_pham.ten_san_pham')
            ->get()
            ->keyBy('id');

        $filtered = [];
        foreach ($ids as $id) {
            $p = $rows->get($id);
            if (!$p) continue;

            $price = ($p->gia_khuyen_mai && $p->gia_khuyen_mai > 0) ? (float) $p->gia_khuyen_mai : (float) $p->gia_goc;
            if ($maxPrice !== null && $price > $maxPrice) continue;
            if ($minPrice !== null && $price < $minPrice) continue;

            if (!empty($catKws)) {
                $catLower = mb_strtolower((string) ($p->ten_danh_muc ?? '') . ' ' . $p->ten_san_pham, 'UTF-8');
                $matched = false;
                foreach ($catKws as $kw) {
                    if (str_contains($catLower, $kw)) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) continue;
            }

            $filtered[] = $id;
        }

        return $filtered;
    }

    /**
     * Trích xuất dữ liệu thực tế và phân tích Xu hướng (Trends) trực tiếp từ CSDL MySQL
     */
    protected function buildDatabaseContext(string $userMessage, array $constraints = []): string
    {
        // 1. Lấy toàn bộ sản phẩm đang kinh doanh kèm Danh mục, Thương hiệu và Thông số kỹ thuật
        $products = DB::table('san_pham')
            ->leftJoin('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->leftJoin('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->leftJoin('thong_so_ky_thuat', 'san_pham.id', '=', 'thong_so_ky_thuat.san_pham_id')
            ->where('san_pham.trang_thai', 1)
            ->select(
                'san_pham.id',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.gia_goc',
                'san_pham.gia_khuyen_mai',
                'san_pham.thoi_gian_bao_hanh',
                'san_pham.mo_ta_ngan',
                'san_pham.luot_xem',
                'san_pham.noi_bat',
                'danh_muc.ten_danh_muc',
                'thuong_hieu.ten_thuong_hieu',
                'thong_so_ky_thuat.cpu',
                'thong_so_ky_thuat.ram',
                'thong_so_ky_thuat.o_cung',
                'thong_so_ky_thuat.card_do_hoa',
                'thong_so_ky_thuat.man_hinh',
                'thong_so_ky_thuat.do_phan_giai',
                'thong_so_ky_thuat.tan_so_quet',
                'thong_so_ky_thuat.pin',
                'thong_so_ky_thuat.trong_luong',
                'thong_so_ky_thuat.he_dieu_hanh'
            )
            ->orderBy('san_pham.id', 'asc')
            ->get();

        // 2. Lấy biến thể cấu hình và tồn kho từng sản phẩm
        $variantsByProduct = DB::table('bien_the_san_pham')
            ->where('trang_thai', 1)
            ->get()
            ->groupBy('san_pham_id');

        // 3. Thống kê số lượng đã bán (từ chi_tiet_don_hang -> bien_the_san_pham -> don_hang không hủy)
        $soldStats = DB::table('chi_tiet_don_hang')
            ->join('don_hang', 'chi_tiet_don_hang.don_hang_id', '=', 'don_hang.id')
            ->join('bien_the_san_pham', 'chi_tiet_don_hang.bien_the_id', '=', 'bien_the_san_pham.id')
            ->where('don_hang.trang_thai_don_hang', '!=', 'da_huy')
            ->select('bien_the_san_pham.san_pham_id', DB::raw('SUM(chi_tiet_don_hang.so_luong) as tong_da_ban'))
            ->groupBy('bien_the_san_pham.san_pham_id')
            ->pluck('tong_da_ban', 'san_pham_id');

        // 4. Thống kê điểm đánh giá sao trung bình từ bảng danh_gia
        $reviewStats = DB::table('danh_gia')
            ->where('trang_thai_duyet', 1)
            ->select(
                'san_pham_id',
                DB::raw('ROUND(AVG(so_sao), 1) as diem_tb'),
                DB::raw('COUNT(*) as so_danh_gia')
            )
            ->groupBy('san_pham_id')
            ->get()
            ->keyBy('san_pham_id');

        // 5. Đếm Serial vật lý sẵn sàng trong kho theo biến thể -> sản phẩm
        $serialStockByProduct = DB::table('serial_san_pham')
            ->join('bien_the_san_pham', 'serial_san_pham.bien_the_id', '=', 'bien_the_san_pham.id')
            ->whereIn('serial_san_pham.trang_thai', ['trong_kho', 'san_sang_ban'])
            ->select('bien_the_san_pham.san_pham_id', DB::raw('COUNT(*) as serial_san_sang'))
            ->groupBy('bien_the_san_pham.san_pham_id')
            ->pluck('serial_san_sang', 'san_pham_id');

        // Xây dựng danh mục sản phẩm chi tiết cho Prompt
        $productLines = [];
        $minPrice = null;
        $maxPrice = null;

        foreach ($products as $p) {
            $effectivePrice = ($p->gia_khuyen_mai && $p->gia_khuyen_mai > 0) ? (float) $p->gia_khuyen_mai : (float) $p->gia_goc;
            $minPrice = $minPrice === null ? $effectivePrice : min($minPrice, $effectivePrice);
            $maxPrice = $maxPrice === null ? $effectivePrice : max($maxPrice, $effectivePrice);

            $specs = array_filter([
                $p->cpu ? "CPU: {$p->cpu}" : null,
                $p->ram ? "RAM: {$p->ram}" : null,
                $p->o_cung ? "Ổ cứng: {$p->o_cung}" : null,
                $p->card_do_hoa ? "GPU/VGA: {$p->card_do_hoa}" : null,
                $p->man_hinh ? "Màn hình: {$p->man_hinh}" : null,
                $p->do_phan_giai ? "Độ phân giải: {$p->do_phan_giai}" : null,
                $p->tan_so_quet ? "Tần số quét: {$p->tan_so_quet}" : null,
                $p->trong_luong ? "Trọng lượng: {$p->trong_luong}" : null,
                $p->he_dieu_hanh ? "HĐH: {$p->he_dieu_hanh}" : null,
            ]);

            $variants = $variantsByProduct->get($p->id, collect());
            $variantStrs = [];
            $totalVariantStock = 0;
            foreach ($variants as $v) {
                $vPrice = ($v->gia_khuyen_mai && $v->gia_khuyen_mai > 0) ? $v->gia_khuyen_mai : $v->gia_ban;
                $totalVariantStock += (int) $v->so_luong_ton;
                $variantStrs[] = "{$v->ten_bien_the} (Giá: " . number_format((float) $vPrice, 0, ',', '.') . "đ, Tồn: {$v->so_luong_ton})";
            }

            $soldCount = (int) ($soldStats[$p->id] ?? 0);
            $rev = $reviewStats->get($p->id);
            $ratingText = $rev ? "{$rev->diem_tb}/5 sao ({$rev->so_danh_gia} đánh giá)" : "Chưa có đánh giá";
            $serialReady = (int) ($serialStockByProduct[$p->id] ?? 0);

            $line = sprintf(
                "- [ID:%d | Mã:%s] %s | Danh mục: %s | Hãng: %s | Giá gốc: %sđ | Giá KM hiện tại: %sđ | Bảo hành: %d tháng | Lượt xem: %d | Đã bán: %d | Đánh giá: %s | Nổi bật: %s",
                $p->id,
                $p->ma_san_pham,
                $p->ten_san_pham,
                $p->ten_danh_muc ?? 'Khác',
                $p->ten_thuong_hieu ?? 'Khác',
                number_format((float) $p->gia_goc, 0, ',', '.'),
                number_format($effectivePrice, 0, ',', '.'),
                (int) $p->thoi_gian_bao_hanh,
                (int) $p->luot_xem,
                $soldCount,
                $ratingText,
                $p->noi_bat ? 'Có' : 'Không'
            );

            if (!empty($specs)) {
                $line .= "\n  + Thông số kỹ thuật: " . implode(' | ', $specs);
            }
            if (!empty($variantStrs)) {
                $line .= "\n  + Biến thể & Tồn kho (Tổng tồn biến thể: {$totalVariantStock}, Serial sẵn sàng: {$serialReady}): " . implode('; ', $variantStrs);
            }
            if ($p->mo_ta_ngan) {
                $line .= "\n  + Mô tả ngắn: " . $p->mo_ta_ngan;
            }

            $productLines[] = $line;
        }

        // 6. Tổng hợp Xu hướng Dữ liệu (Data Trends) từ CSDL
        $topViewed = collect($products)->sortByDesc('luot_xem')->take(5)->map(function ($p) {
            return "{$p->ten_san_pham} ({$p->luot_xem} lượt xem)";
        })->implode('; ');

        $topSoldList = [];
        foreach ($soldStats->sortDesc()->take(5) as $spId => $qty) {
            $found = collect($products)->firstWhere('id', $spId);
            if ($found) {
                $topSoldList[] = "{$found->ten_san_pham} (Đã bán: {$qty})";
            }
        }
        $topSoldText = !empty($topSoldList) ? implode('; ', $topSoldList) : 'Chưa phát sinh nhiều đơn hàng hoàn tất';

        $categoriesList = DB::table('danh_muc')->where('trang_thai', 1)->pluck('ten_danh_muc')->implode(', ');
        $brandsList     = DB::table('thuong_hieu')->where('trang_thai', 1)->pluck('ten_thuong_hieu')->implode(', ');

        // 7. Lấy danh sách Mã giảm giá / Voucher đang hoạt động
        $vouchers = DB::table('ma_giam_gia')
            ->where('trang_thai', 1)
            ->get();

        $voucherLines = [];
        foreach ($vouchers as $vc) {
            $conLai = max(0, (int) $vc->so_luong - (int) $vc->da_su_dung);
            if ($conLai <= 0) {
                continue;
            }
            $mucGiam = $vc->loai_giam_gia === 'percent'
                ? "Giảm {$vc->gia_tri_giam}% (tối đa " . number_format((float) $vc->giam_toi_da, 0, ',', '.') . "đ)"
                : "Giảm trực tiếp " . number_format((float) $vc->gia_tri_giam, 0, ',', '.') . "đ";

            $voucherLines[] = sprintf(
                "- Mã [%s]: %s | %s | Đơn tối thiểu: %sđ | Lượt còn lại: %d",
                $vc->ma_code,
                $vc->tieu_de,
                $mucGiam,
                number_format((float) $vc->don_hang_toi_thieu, 0, ',', '.'),
                $conLai
            );
        }

        // 8. Kiểm tra nếu khách hỏi tra cứu Serial / IMEI cụ thể
        $serialContext = '';
        if (preg_match_all('/[A-Z0-9]{2,}(?:-[A-Z0-9]+){1,}/i', $userMessage, $snMatches)) {
            $candidates = $snMatches[0];
            $serialRows = DB::table('serial_san_pham')
                ->join('bien_the_san_pham', 'serial_san_pham.bien_the_id', '=', 'bien_the_san_pham.id')
                ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
                ->where(function ($q) use ($candidates) {
                    foreach ($candidates as $code) {
                        $q->orWhere('serial_san_pham.so_serial', $code)
                          ->orWhere('serial_san_pham.imei', $code);
                    }
                })
                ->select(
                    'serial_san_pham.so_serial',
                    'serial_san_pham.imei',
                    'serial_san_pham.trang_thai',
                    'serial_san_pham.ngay_xuat_kho',
                    'serial_san_pham.han_bao_hanh',
                    'san_pham.ten_san_pham',
                    'bien_the_san_pham.ten_bien_the'
                )
                ->get();

            if ($serialRows->isNotEmpty()) {
                $snLines = [];
                foreach ($serialRows as $sr) {
                    $snLines[] = "- Serial: {$sr->so_serial} | Máy: {$sr->ten_san_pham} ({$sr->ten_bien_the}) | Trạng thái: {$sr->trang_thai} | Ngày xuất kho: " . ($sr->ngay_xuat_kho ?? 'Chưa xuất') . " | Hạn bảo hành: " . ($sr->han_bao_hanh ?? 'Chưa kích hoạt');
                }
                $serialContext = "\n=== KẾT QUẢ TRA CỨU SERIAL/IMEI TRONG CSDL ===\n" . implode("\n", $snLines) . "\n";
            }
        }

        $minFormatted = $minPrice !== null ? number_format($minPrice, 0, ',', '.') . 'đ' : '0đ';
        $maxFormatted = $maxPrice !== null ? number_format($maxPrice, 0, ',', '.') . 'đ' : '0đ';

        // 9. Tạo gợi ý lọc chính xác từ câu hỏi người dùng (giá / danh mục)
        $filterHint = '';
        $hasConstraints = ($constraints['min_price'] ?? null) !== null
            || ($constraints['max_price'] ?? null) !== null
            || !empty($constraints['category_keywords']);

        if ($hasConstraints) {
            $matchedIds = [];
            foreach ($products as $p) {
                $eff = ($p->gia_khuyen_mai && $p->gia_khuyen_mai > 0) ? (float) $p->gia_khuyen_mai : (float) $p->gia_goc;
                if (($constraints['max_price'] ?? null) !== null && $eff > $constraints['max_price']) continue;
                if (($constraints['min_price'] ?? null) !== null && $eff < $constraints['min_price']) continue;
                if (!empty($constraints['category_keywords'])) {
                    $catText = mb_strtolower((string) ($p->ten_danh_muc ?? '') . ' ' . $p->ten_san_pham, 'UTF-8');
                    $ok = false;
                    foreach ($constraints['category_keywords'] as $kw) {
                        if (str_contains($catText, $kw)) { $ok = true; break; }
                    }
                    if (!$ok) continue;
                }
                $matchedIds[] = "[ID:{$p->id}] {$p->ten_san_pham} (" . number_format($eff, 0, ',', '.') . "đ)";
            }

            $condParts = [];
            if (!empty($constraints['category_keywords'])) {
                $condParts[] = 'Danh mục: ' . implode(', ', $constraints['category_keywords']);
            }
            if (($constraints['min_price'] ?? null) !== null) {
                $condParts[] = 'Giá >= ' . number_format($constraints['min_price'], 0, ',', '.') . 'đ';
            }
            if (($constraints['max_price'] ?? null) !== null) {
                $condParts[] = 'Giá <= ' . number_format($constraints['max_price'], 0, ',', '.') . 'đ';
            }

            $matchedText = !empty($matchedIds)
                ? implode("\n", $matchedIds)
                : 'KHÔNG CÓ SẢN PHẨM NÀO THỎA MÃN ĐIỀU KIỆN NÀY TRONG KHO.';

            $filterHint = "\n=== BỘ LỌC BẮT BUỘC TRÍCH XUẤT TỪ CÂU HỎI HIỆN TẠI ===\n"
                . "- Điều kiện khách yêu cầu: " . implode(' | ', $condParts) . "\n"
                . "- Các sản phẩm ĐẠT CHUẨN 100% điều kiện:\n{$matchedText}\n"
                . "- QUY TẮC CỨNG: CHỈ được phép giới thiệu và gắn [[SP_IDS:...]] các sản phẩm nằm trong danh sách ĐẠT CHUẨN ở trên. TUYỆT ĐỐI KHÔNG liệt kê thêm sản phẩm vượt mức giá tối đa hoặc sai danh mục.\n";
        }

        return <<<CONTEXT
=== TỔNG QUAN & XU HƯỚNG DỮ LIỆU (TRENDS) TỪ CSDL TECHMART ===
- Tổng số sản phẩm đang kinh doanh trong CSDL: {$products->count()} sản phẩm
- Các danh mục hiện có: {$categoriesList}
- Các thương hiệu chính hãng hiện có: {$brandsList}
- Dải giá sản phẩm hiện có trong kho: Từ {$minFormatted} đến {$maxFormatted}
- Xu hướng Top sản phẩm được xem nhiều nhất: {$topViewed}
- Xu hướng Top sản phẩm bán chạy nhất: {$topSoldText}
{$filterHint}
=== DANH SÁCH CHI TIẾT TOÀN BỘ SẢN PHẨM TRONG CSDL ===
{$this->implodeLines($productLines)}

=== DANH SÁCH MÃ GIẢM GIÁ (VOUCHER) ĐANG HIỆU LỰC TRONG CSDL ===
{$this->implodeLines($voucherLines, 'Hiện không có mã giảm giá nào.')}
{$serialContext}
CONTEXT;
    }

    protected function implodeLines(array $lines, string $emptyFallback = 'Không có dữ liệu.'): string
    {
        return !empty($lines) ? implode("\n", $lines) : $emptyFallback;
    }

    /**
     * Xây dựng System Prompt khóa chặt phạm vi trong CSDL dự án (Strict Out-of-Scope Guardrails)
     */
    protected function buildSystemInstruction(string $dbContext): string
    {
        return <<<PROMPT
Bạn là **Trợ lý AI TechMart**, chuyên gia tư vấn thiết bị điện tử & phần cứng máy tính hoạt động độc quyền trên Cơ sở dữ liệu (CSDL) của dự án Website thương mại điện tử TechMart.

NHIỆM VỤ & QUY TẮC RÀNG BUỘC NGHIÊM NGẶT (BẮT BUỘC TUÂN THỦ 100%):

1. **CHỈ SỬ DỤNG DỮ LIỆU TRONG CSDL DỰ ÁN & TUÂN THỦ BỘ LỌC NGHIÊM NGẶT**:
   - Mọi thông tin bạn trả lời về sản phẩm, thông số kỹ thuật (CPU, RAM, SSD, VGA, màn hình...), mức giá, bảo hành, tồn kho, xu hướng mua sắm (lượt xem, lượt bán, đánh giá) và mã giảm giá Voucher **PHẢI LẤY CHÍNH XÁC 100%** từ khối dữ liệu CSDL bên dưới.
   - **TUYỆT ĐỐI KHÔNG BỊA ĐẶT (NO HALLUCINATION)**: Không được tự nghĩ ra sản phẩm, thương hiệu, mức giá hay mã giảm giá không tồn tại trong CSDL bên dưới.
   - **TUÂN THỦ CHẶT CHẼ ĐIỀU KIỆN GIÁ & THÔNG SỐ KHÁCH HÀNG HỎI**:
     + Nếu khách hỏi **"dưới X triệu"**: CHỈ được giới thiệu và gắn `[[SP_IDS:...]]` các sản phẩm có giá hiện tại `<= X` triệu. **TUYỆT ĐỐI KHÔNG** đưa thêm sản phẩm có giá cao hơn X triệu vào danh sách gợi ý (kể cả ghi chú "hơi vượt ngân sách"). Nếu chỉ có 1 sản phẩm đạt điều kiện, chỉ tư vấn đúng 1 sản phẩm đó!
     + Nếu khách hỏi đúng thông số (ví dụ: màn hình 2K / QHD `2560 x 1440`, Laptop chơi game có RTX, RAM DDR5...): chỉ chọn sản phẩm có thông số khớp chính xác yêu cầu.
   - Nếu khách hỏi một thiết bị hoặc phân khúc KHÔNG CÓ trong CSDL hiện tại (ví dụ: máy giặt, tủ lạnh, hoặc không có sản phẩm nào đạt điều kiện giá/cấu hình), bạn PHẢI trả lời rõ: *"Dạ hiện tại trong Cơ sở dữ liệu kho hàng của TechMart chưa có mẫu sản phẩm đáp ứng đúng yêu cầu này."* Sau đó có thể hỏi khách có muốn nâng ngân sách hoặc tham khảo dòng sản phẩm khác không (không tự ý chèn card sai điều kiện vào `[[SP_IDS]]`).

2. **KHÓA CHẶT PHẠM VI — TỪ CHỐI MỌI CÂU HỎI NGOÀI DỰ ÁN (STRICT OUT-OF-SCOPE REFUSAL)**:
   - Nếu người dùng hỏi bất kỳ nội dung nào KHÔNG LIÊN QUAN đến cửa hàng TechMart và CSDL dự án (ví dụ: hỏi thời tiết, nấu ăn, lịch sử, địa lý, chính trị, bóng đá, làm thơ, giải toán, viết code lập trình chung, dịch thuật, tâm sự đời tư...), bạn **TUYỆT ĐỐI KHÔNG TRẢ LỜI NỘI DUNG ĐÓ**.
   - Hãy từ chối lịch sự theo mẫu: *"Dạ xin lỗi bạn, em là Trợ lý AI chuyên biệt của hệ thống TechMart nên chỉ được phép trả lời các câu hỏi nằm trong phạm vi Cơ sở dữ liệu dự án (sản phẩm, cấu hình phần cứng, xu hướng bán chạy, mã giảm giá Voucher và tra cứu bảo hành tại TechMart). Bạn cần em tư vấn Laptop, Màn hình, Linh kiện PC hay Gaming Gear nào ạ?"*

3. **CÁCH TƯ VẤN CHUYÊN NGHIỆP & PHÂN TÍCH XU HƯỚNG**:
   - Trả lời bằng tiếng Việt thân thiện, ngắn gọn, súc tích, dễ đọc (sử dụng gạch đầu dòng khi liệt kê).
   - Luôn nêu rõ **Tên sản phẩm**, **Giá khuyến mãi hiện tại**, **Thông số nổi bật**, **Thời hạn bảo hành** và gợi ý **Mã Voucher giảm giá** trong CSDL nếu giá trị sản phẩm đủ điều kiện áp mã.
   - Khi khách hỏi về xu hướng (trend), sản phẩm hot, bán chạy hoặc đáng mua nhất, hãy phân tích dựa trên số liệu **Lượt xem**, **Số lượng đã bán**, **Điểm đánh giá sao** và nhãn **Nổi bật** thực tế từ CSDL.

4. **ĐỊNH DẠNG GẮN MÃ SẢN PHẨM ĐỀ XUẤT Ở CUỐI CÂU**:
   - Nếu trong câu trả lời bạn có giới thiệu hoặc đề xuất sản phẩm cụ thể từ CSDL, hãy đặt ở dòng cuối cùng thẻ định danh theo đúng cú pháp: `[[SP_IDS:id1,id2]]` (trong đó `id1,id2` là số ID của sản phẩm trong CSDL, tối đa 3 sản phẩm phù hợp nhất, ví dụ: `[[SP_IDS:8,1]]`).
   - Nếu không đề xuất sản phẩm nào hoặc từ chối câu hỏi ngoài phạm vi thì KHÔNG ghi thẻ này.

DƯỚI ĐÂY LÀ DỮ LIỆU THỜI GIAN THỰC TRÍCH XUẤT TRỰC TIẾP TỪ CSDL DỰ ÁN TECHMART:
{$dbContext}
PROMPT;
    }

    /**
     * Lấy thông tin Card sản phẩm chuẩn từ CSDL để hiển thị trực quan trên khung chat Frontend
     */
    protected function fetchSuggestedProductCards(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $rows = DB::table('san_pham')
            ->leftJoin('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->whereIn('san_pham.id', $productIds)
            ->where('san_pham.trang_thai', 1)
            ->select(
                'san_pham.id',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.slug',
                'san_pham.anh_chinh',
                'san_pham.gia_goc',
                'san_pham.gia_khuyen_mai',
                'san_pham.thoi_gian_bao_hanh',
                'thuong_hieu.ten_thuong_hieu'
            )
            ->get()
            ->keyBy('id');

        $ordered = [];
        foreach ($productIds as $pid) {
            if ($rows->has($pid)) {
                $item = $rows->get($pid);
                $ordered[] = [
                    'id'                 => $item->id,
                    'ma_san_pham'        => $item->ma_san_pham,
                    'ten_san_pham'       => $item->ten_san_pham,
                    'slug'               => $item->slug,
                    'anh_chinh'          => $item->anh_chinh,
                    'gia_goc'            => (float) $item->gia_goc,
                    'gia_khuyen_mai'     => $item->gia_khuyen_mai ? (float) $item->gia_khuyen_mai : (float) $item->gia_goc,
                    'thoi_gian_bao_hanh' => (int) $item->thoi_gian_bao_hanh,
                    'ten_thuong_hieu'    => $item->ten_thuong_hieu,
                ];
            }
        }

        return $ordered;
    }
}
