<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatBotController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * POST /api/client/chat-ai
     * Tư vấn sản phẩm & xu hướng dựa hoàn toàn trên CSDL thực tế của TechMart
     */
    public function chatWithAI(Request $request)
    {
        $userMessage = trim((string) ($request->input('message') ?? $request->input('noi_dung') ?? ''));

        if ($userMessage === '') {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng nhập câu hỏi cần tư vấn.',
            ], 422);
        }

        if (mb_strlen($userMessage) > 1000) {
            $userMessage = mb_substr($userMessage, 0, 1000);
        }

        $sessionId = trim((string) ($request->input('session_id') ?: ('sess_' . Str::random(16))));
        $userId    = $request->input('user_id') ?: $request->input('nguoi_dung_id');

        // Kiểm tra khóa ngoại nguoi_dung_id hợp lệ
        $validUserId = null;
        if ($userId && DB::table('nguoi_dung')->where('id', $userId)->exists()) {
            $validUserId = (int) $userId;
        }

        // 1. Tìm hoặc khởi tạo phiên hội thoại trong bảng hoi_thoai_ai
        $conversation = AiConversation::where('session_id', $sessionId)
            ->where('trang_thai', true)
            ->latest('id')
            ->first();

        if (!$conversation) {
            $conversation = AiConversation::create([
                'nguoi_dung_id' => $validUserId,
                'session_id'    => $sessionId,
                'tieu_de'       => Str::limit($userMessage, 60, '...'),
                'ngu_canh'      => 'TechMart DB Grounded RAG (Gemini)',
                'trang_thai'    => true,
            ]);
        } elseif ($validUserId && !$conversation->nguoi_dung_id) {
            $conversation->update(['nguoi_dung_id' => $validUserId]);
        }

        // 2. Lấy 6 tin nhắn gần nhất để duy trì mạch hội thoại đa lượt
        $recentHistory = AiMessage::where('hoi_thoai_id', $conversation->id)
            ->orderBy('id', 'desc')
            ->take(6)
            ->get(['vai_tro', 'noi_dung'])
            ->reverse()
            ->values()
            ->toArray();

        // 3. Gọi AiService (RAG trên CSDL dự án + Khóa chặt phạm vi Out-of-Scope)
        $aiResult = $this->aiService->askDatabaseGroundedAi($userMessage, $recentHistory);

        // 4. Lưu tin nhắn của Khách hàng & Trợ lý AI vào bảng tin_nhan_ai
        AiMessage::create([
            'hoi_thoai_id'     => $conversation->id,
            'vai_tro'          => 'user',
            'noi_dung'         => $userMessage,
            'san_pham_de_xuat' => null,
            'so_token'         => 0,
        ]);

        AiMessage::create([
            'hoi_thoai_id'     => $conversation->id,
            'vai_tro'          => 'assistant',
            'noi_dung'         => $aiResult['reply'],
            'san_pham_de_xuat' => !empty($aiResult['suggested_ids']) ? $aiResult['suggested_ids'] : null,
            'so_token'         => $aiResult['tokens'] ?? 0,
        ]);

        return response()->json([
            'status'             => true,
            'session_id'         => $sessionId,
            'conversation_id'    => $conversation->id,
            'message'            => 'Tư vấn từ dữ liệu CSDL TechMart thành công',
            'reply'              => $aiResult['reply'],
            'suggested_products' => $aiResult['suggested_products'] ?? [],
            'tokens_used'        => $aiResult['tokens'] ?? 0,
        ]);
    }

    /**
     * GET /api/client/chat-ai/history?session_id=...
     * Khôi phục lịch sử trò chuyện từ CSDL khi tải lại trang
     */
    public function getHistory(Request $request)
    {
        $sessionId = trim((string) $request->query('session_id', ''));
        if ($sessionId === '') {
            return response()->json([
                'status'   => true,
                'messages' => [],
            ]);
        }

        $conversation = AiConversation::where('session_id', $sessionId)
            ->where('trang_thai', true)
            ->latest('id')
            ->first();

        if (!$conversation) {
            return response()->json([
                'status'   => true,
                'messages' => [],
            ]);
        }

        $rawMessages = AiMessage::where('hoi_thoai_id', $conversation->id)
            ->orderBy('id', 'asc')
            ->get();

        // Gom tất cả ID sản phẩm đề xuất để lấy thông tin 1 lần tránh N+1 query
        $allSuggestedIds = [];
        foreach ($rawMessages as $m) {
            if (is_array($m->san_pham_de_xuat)) {
                foreach ($m->san_pham_de_xuat as $pid) {
                    $allSuggestedIds[] = (int) $pid;
                }
            }
        }
        $allSuggestedIds = array_values(array_unique(array_filter($allSuggestedIds)));

        $productsMap = collect();
        if (!empty($allSuggestedIds)) {
            $productsMap = DB::table('san_pham')
                ->whereIn('id', $allSuggestedIds)
                ->select('id', 'ma_san_pham', 'ten_san_pham', 'slug', 'anh_chinh', 'gia_goc', 'gia_khuyen_mai', 'thoi_gian_bao_hanh')
                ->get()
                ->keyBy('id');
        }

        $formatted = [];
        foreach ($rawMessages as $m) {
            $cards = [];
            if (is_array($m->san_pham_de_xuat)) {
                foreach ($m->san_pham_de_xuat as $pid) {
                    $p = $productsMap->get((int) $pid);
                    if ($p) {
                        $cards[] = [
                            'id'                 => $p->id,
                            'ma_san_pham'        => $p->ma_san_pham,
                            'ten_san_pham'       => $p->ten_san_pham,
                            'slug'               => $p->slug,
                            'anh_chinh'          => $p->anh_chinh,
                            'gia_goc'            => (float) $p->gia_goc,
                            'gia_khuyen_mai'     => $p->gia_khuyen_mai ? (float) $p->gia_khuyen_mai : (float) $p->gia_goc,
                            'thoi_gian_bao_hanh' => (int) $p->thoi_gian_bao_hanh,
                        ];
                    }
                }
            }

            $formatted[] = [
                'id'                 => $m->id,
                'sender'             => $m->vai_tro === 'user' ? 'user' : 'ai',
                'text'               => $m->noi_dung,
                'suggested_products' => $cards,
                'created_at'         => optional($m->created_at)->format('H:i d/m/Y'),
            ];
        }

        return response()->json([
            'status'          => true,
            'conversation_id' => $conversation->id,
            'messages'        => $formatted,
        ]);
    }

    /**
     * POST /api/client/chat-ai/clear-session
     * Làm mới cuộc hội thoại của khách hàng
     */
    public function clearSession(Request $request)
    {
        $sessionId = trim((string) $request->input('session_id', ''));
        if ($sessionId !== '') {
            AiConversation::where('session_id', $sessionId)
                ->where('trang_thai', true)
                ->update(['trang_thai' => false]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Đã làm mới phiên trò chuyện AI.',
        ]);
    }

    /**
     * GET /api/admin/ai/analytics
     * Thống kê hoạt động & xu hướng tư vấn của Trợ lý AI cho Admin Dashboard
     */
    public function adminAnalytics()
    {
        $totalSessions = AiConversation::count();
        $totalMessages = AiMessage::count();
        $totalTokens   = (int) AiMessage::sum('so_token');

        // Tính xu hướng sản phẩm được AI đề xuất nhiều nhất từ cột JSON san_pham_de_xuat
        $recommendCounts = [];
        $assistantMsgs = AiMessage::where('vai_tro', 'assistant')
            ->whereNotNull('san_pham_de_xuat')
            ->get(['san_pham_de_xuat']);

        foreach ($assistantMsgs as $msg) {
            if (is_array($msg->san_pham_de_xuat)) {
                foreach ($msg->san_pham_de_xuat as $pid) {
                    $pid = (int) $pid;
                    if ($pid > 0) {
                        $recommendCounts[$pid] = ($recommendCounts[$pid] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($recommendCounts);
        $topIds = array_slice(array_keys($recommendCounts), 0, 5);
        $topProducts = [];
        if (!empty($topIds)) {
            $rows = DB::table('san_pham')
                ->whereIn('id', $topIds)
                ->select('id', 'ma_san_pham', 'ten_san_pham', 'gia_khuyen_mai', 'gia_goc')
                ->get()
                ->keyBy('id');

            foreach ($topIds as $pid) {
                if ($rows->has($pid)) {
                    $r = $rows->get($pid);
                    $topProducts[] = [
                        'id'               => $r->id,
                        'ma_san_pham'      => $r->ma_san_pham,
                        'ten_san_pham'     => $r->ten_san_pham,
                        'gia'              => (float) ($r->gia_khuyen_mai ?: $r->gia_goc),
                        'so_lan_de_xuat'   => $recommendCounts[$pid],
                    ];
                }
            }
        }

        // 10 tin nhắn hỏi-đáp gần nhất
        $recentLogs = DB::table('tin_nhan_ai')
            ->join('hoi_thoai_ai', 'tin_nhan_ai.hoi_thoai_id', '=', 'hoi_thoai_ai.id')
            ->select(
                'tin_nhan_ai.id',
                'hoi_thoai_ai.session_id',
                'tin_nhan_ai.vai_tro',
                'tin_nhan_ai.noi_dung',
                'tin_nhan_ai.so_token',
                'tin_nhan_ai.created_at'
            )
            ->orderBy('tin_nhan_ai.id', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => [
                'total_sessions'          => $totalSessions,
                'total_messages'          => $totalMessages,
                'total_tokens'            => $totalTokens,
                'top_recommended_products' => $topProducts,
                'recent_logs'             => $recentLogs,
            ],
        ]);
    }
}
