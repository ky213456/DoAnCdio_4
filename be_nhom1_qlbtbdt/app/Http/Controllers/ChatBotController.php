<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function chatWithAI(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Tư vấn cấu hình máy tính TechMart AI',
            'reply' => 'Xin chào! Tôi là Trợ lý AI TechMart. Bạn đang tìm cấu hình phục vụ nhu cầu đồ họa, gaming hay văn phòng?'
        ]);
    }
}
