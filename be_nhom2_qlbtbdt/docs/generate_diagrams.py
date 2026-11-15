# -*- coding: utf-8 -*-
import sys
sys.stdout.reconfigure(encoding='utf-8')
from PIL import Image, ImageDraw, ImageFont

def get_fonts():
    try:
        font_header = ImageFont.truetype('C:/Windows/Fonts/arialbd.ttf', 24)
        font_title = ImageFont.truetype('C:/Windows/Fonts/arialbd.ttf', 18)
        font_box = ImageFont.truetype('C:/Windows/Fonts/arialbd.ttf', 14)
        font_sub = ImageFont.truetype('C:/Windows/Fonts/arial.ttf', 12)
        font_step = ImageFont.truetype('C:/Windows/Fonts/arialbd.ttf', 11)
    except:
        font_header = font_title = font_box = font_sub = font_step = ImageFont.load_default()
    return font_header, font_title, font_box, font_sub, font_step

# 1. Checkout Flow
def create_checkout_flow():
    font_header, font_title, font_box, font_sub, font_step = get_fonts()
    W, H = 1600, 420
    img = Image.new('RGB', (W, H), '#FFFFFF')
    draw = ImageDraw.Draw(img)

    draw.rounded_rectangle([10, 10, W-10, H-10], radius=16, fill='#F8FAFC', outline='#CBD5E1', width=2)
    draw.text((40, 30), 'QUY TRÌNH ĐẶT HÀNG & THANH TOÁN TRỰC TUYẾN (ORDER & PAYMENT FLOW)', fill='#0F172A', font=font_title)
    draw.text((40, 60), 'Quy trình khép kín: Khóa giỏ hàng -> DB Transaction -> Ký số HMAC-SHA512 VNPAY -> Webhook IPN -> Gán Serial/IMEI', fill='#64748B', font=font_sub)

    steps = [
        ('BƯỚC 1', 'Khách hàng Checkout', 'POST /api/orders/checkout\nKhóa giỏ hàng & áp Voucher\nValidate địa chỉ nhận hàng', '#3B82F6', '#EFF6FF'),
        ('BƯỚC 2', 'Tạo Đơn & Trừ Kho', 'DB Transaction an toàn\nTrừ tồn kho biến thể\nTrạng thái: Chờ thanh toán', '#6366F1', '#EEF2FF'),
        ('BƯỚC 3', 'Cổng VNPAY Sandbox', 'Tạo URL ký số SHA512\nKhách quét QR / Thẻ ATM\nChuyển hướng cổng an toàn', '#0EA5E9', '#F0F9FF'),
        ('BƯỚC 4', 'Xử lý IPN Webhook', 'Server VNPAY gọi ngầm\nXác thực chữ ký Checksum\nĐổi trạng thái: Đã thanh toán', '#10B981', '#ECFDF5'),
        ('BƯỚC 5', 'Xuất Kho Gán Serial', 'Thủ kho kiểm tra mã máy\nGán Serial/IMEI vật lý\nKích hoạt Bảo hành điện tử', '#8B5CF6', '#F5F3FF')
    ]

    x, y, bw, bh = 40, 110, 260, 240
    for i, (step_tag, title, desc, border_col, bg_col) in enumerate(steps):
        draw.rounded_rectangle([x, y, x + bw, y + bh], radius=12, fill=bg_col, outline=border_col, width=2)
        draw.rounded_rectangle([x + 16, y + 16, x + 85, y + 38], radius=6, fill=border_col)
        draw.text((x + 22, y + 20), step_tag, fill='#FFFFFF', font=font_step)
        draw.text((x + 16, y + 55), title, fill='#0F172A', font=font_box)
        for j, line in enumerate(desc.split('\n')):
            draw.text((x + 16, y + 90 + j * 24), line, fill='#334155', font=font_sub)
            
        if i < len(steps) - 1:
            ax = x + bw + 10
            ay = y + bh // 2
            draw.line([(ax, ay), (ax + 24, ay)], fill='#94A3B8', width=3)
            draw.polygon([(ax + 24, ay - 6), (ax + 34, ay), (ax + 24, ay + 6)], fill='#94A3B8')
        x += bw + 50

    img.save('E:/doantichhop/CodeNhom1/NHOM1_BE_QLBTBDT/docs/images/flow_checkout_vnpay.png', 'PNG', dpi=(150, 150))
    print('1. Created flow_checkout_vnpay.png')

# 2. AI Architecture Flow
def create_ai_flow():
    font_header, font_title, font_box, font_sub, font_step = get_fonts()
    W, H = 1600, 460
    img = Image.new('RGB', (W, H), '#FFFFFF')
    draw = ImageDraw.Draw(img)

    draw.rounded_rectangle([10, 10, W-10, H-10], radius=16, fill='#FDFBF7', outline='#E2E8F0', width=2)
    draw.text((40, 30), 'KIẾN TRÚC TRỢ LÝ ẢO AI TƯ VẤN CẤU HÌNH THIẾT BỊ (AI ENGINE ARCHITECTURE)', fill='#1E293B', font=font_title)
    draw.text((40, 60), 'Hệ thống AI RAG nội bộ kết hợp Google Gemini API phân tích nhu cầu phần cứng & ngân sách người dùng', fill='#64748B', font=font_sub)

    blocks = [
        ('1. KHÁCH HÀNG', 'Nhập nhu cầu tư vấn', 'Nhập câu hỏi tự nhiên\nNgân sách: 20-30 triệu\nPOST /api/ai/chat', '#3B82F6', '#EFF6FF'),
        ('2. AI CONTROLLER', 'Tiếp nhận & Xác thực', 'Kiểm tra Token phiên chat\nLưu tin nhắn vào CSDL\nGọi tầng AiService xử lý', '#6366F1', '#EEF2FF'),
        ('3. AI PROXY & RAG', 'Bơm Context Phần Cứng', 'System Prompt chuyên sâu\nQuery bảng thong_so_ky_thuat\nLọc danh sách máy phù hợp', '#EC4899', '#FDF2F8'),
        ('4. GOOGLE GEMINI', 'Mô hình Ngôn ngữ LLM', 'Phân tích ngữ cảnh kỹ thuật\nSo sánh hiệu năng CPU/GPU\nSinh câu trả lời thân thiện', '#EAB308', '#FEFCE8'),
        ('5. PHẢN HỒI WEB', 'Trả lời & Gợi ý Máy', 'Tin nhắn tư vấn chi tiết\nKèm Card link sản phẩm\nLưu vào bảng tin_nhan_ai', '#10B981', '#ECFDF5')
    ]

    x, y, bw, bh = 40, 115, 260, 260
    for i, (tag, title, desc, border_col, bg_col) in enumerate(blocks):
        draw.rounded_rectangle([x, y, x + bw, y + bh], radius=12, fill=bg_col, outline=border_col, width=2)
        draw.rounded_rectangle([x + 16, y + 16, x + 135, y + 38], radius=6, fill=border_col)
        draw.text((x + 22, y + 20), tag, fill='#FFFFFF', font=font_step)
        draw.text((x + 16, y + 55), title, fill='#0F172A', font=font_box)
        for j, line in enumerate(desc.split('\n')):
            draw.text((x + 16, y + 95 + j * 24), line, fill='#334155', font=font_sub)
            
        if i < len(blocks) - 1:
            ax = x + bw + 10
            ay = y + bh // 2
            draw.line([(ax, ay), (ax + 24, ay)], fill='#CBD5E1', width=3)
            draw.polygon([(ax + 24, ay - 6), (ax + 34, ay), (ax + 24, ay + 6)], fill='#94A3B8')
        x += bw + 50

    img.save('E:/doantichhop/CodeNhom1/NHOM1_BE_QLBTBDT/docs/images/flow_ai_architecture.png', 'PNG', dpi=(150, 150))
    print('2. Created flow_ai_architecture.png')

# 3. Serial / Warranty Flow
def create_serial_flow():
    font_header, font_title, font_box, font_sub, font_step = get_fonts()
    W, H = 1600, 420
    img = Image.new('RGB', (W, H), '#FFFFFF')
    draw = ImageDraw.Draw(img)

    draw.rounded_rectangle([10, 10, W-10, H-10], radius=16, fill='#F8FAFC', outline='#CBD5E1', width=2)
    draw.text((40, 30), 'QUY TRÌNH QUẢN LÝ SERIAL / IMEI & BẢO HÀNH ĐIỆN TỬ (WARRANTY FLOW)', fill='#0F172A', font=font_title)
    draw.text((40, 60), 'Kiểm soát vòng đời máy: Nhập lô hàng -> Tồn kho Serial -> Xuất kho theo đơn -> Tra cứu bảo hành trực tuyến', fill='#64748B', font=font_sub)

    steps = [
        ('BƯỚC 1', 'Nhập Kho Lô Hàng', 'Thủ kho tạo phiếu nhập\nSinh dải Serial/IMEI duy nhất\nTrạng thái: \"san_sang_ban\"', '#0284C7', '#F0F9FF'),
        ('BƯỚC 2', 'Xuất Kho Theo Đơn', 'Gán Serial cụ thể cho máy\nTrừ tồn kho biến thể\nTrạng thái máy: \"da_ban\"', '#F59E0B', '#FFFBEB'),
        ('BƯỚC 3', 'Kích Hoạt Bảo Hành', 'Lưu chi_tiet_don_hang\nTự động tính hạn bảo hành\n(12 - 24 tháng theo hãng)', '#10B981', '#ECFDF5'),
        ('BƯỚC 4', 'Khách Tra Cứu IMEI', 'Nhập mã IMEI trên Web\nGET /api/warranty/check\nXem ngày mua & hạn còn lại', '#6366F1', '#EEF2FF'),
        ('BƯỚC 5', 'Bảo Hành & Đổi Trả', 'Lập phiếu tiếp nhận sửa chữa\nNếu đổi máy mới: Cập nhật\nmã Serial mới vào hệ thống', '#EF4444', '#FEF2F2')
    ]

    x, y, bw, bh = 40, 110, 260, 240
    for i, (step_tag, title, desc, border_col, bg_col) in enumerate(steps):
        draw.rounded_rectangle([x, y, x + bw, y + bh], radius=12, fill=bg_col, outline=border_col, width=2)
        draw.rounded_rectangle([x + 16, y + 16, x + 85, y + 38], radius=6, fill=border_col)
        draw.text((x + 22, y + 20), step_tag, fill='#FFFFFF', font=font_step)
        draw.text((x + 16, y + 55), title, fill='#0F172A', font=font_box)
        for j, line in enumerate(desc.split('\n')):
            draw.text((x + 16, y + 90 + j * 24), line, fill='#334155', font=font_sub)
            
        if i < len(steps) - 1:
            ax = x + bw + 10
            ay = y + bh // 2
            draw.line([(ax, ay), (ax + 24, ay)], fill='#94A3B8', width=3)
            draw.polygon([(ax + 24, ay - 6), (ax + 34, ay), (ax + 24, ay + 6)], fill='#94A3B8')
        x += bw + 50

    img.save('E:/doantichhop/CodeNhom1/NHOM1_BE_QLBTBDT/docs/images/flow_serial_warranty.png', 'PNG', dpi=(150, 150))
    print('3. Created flow_serial_warranty.png')

# 4. System Layered Architecture
def create_architecture_diagram():
    font_header, font_title, font_box, font_sub, font_step = get_fonts()
    W, H = 1600, 680
    img = Image.new('RGB', (W, H), '#FFFFFF')
    draw = ImageDraw.Draw(img)

    draw.rounded_rectangle([10, 10, W-10, H-10], radius=16, fill='#F8FAFC', outline='#CBD5E1', width=2)
    draw.text((40, 25), 'KIẾN TRÚC PHÂN TẦNG HỆ THỐNG BACKEND (LAYERED ARCHITECTURE - CLEAN & REPO)', fill='#0F172A', font=font_title)
    draw.text((40, 55), 'Mô hình phân tầng chuyên nghiệp: Presentation Layer -> Domain Service Layer -> Data Repository Layer -> Database', fill='#64748B', font=font_sub)

    layers = [
        ('1. CLIENT PRESENTATION LAYER', 'Web Storefront (React 19 / Vue 3) | Mobile App (Flutter / PWA) | Postman Client API Testing', '#3B82F6', '#EFF6FF', 100),
        ('2. API ROUTING & GATEWAY LAYER', 'Laravel 11 Router (routes/api.php) | Sanctum Token Auth | CheckRole Middleware (RBAC) | Rate Limiter', '#6366F1', '#EEF2FF', 190),
        ('3. CONTROLLER & VALIDATION LAYER', 'AuthController | ProductController | OrderController | VnPayController | AiChatController | AnalyticsController', '#0284C7', '#F0F9FF', 280),
        ('4. SERVICE & BUSINESS LOGIC LAYER', 'OrderService (Checkout & Stock) | VnPayService (HMAC-SHA512) | AiService (Gemini RAG) | SerialWarrantyService', '#10B981', '#ECFDF5', 370),
        ('5. ELOQUENT ORM & REPOSITORY LAYER', '17 Models: User, Product, ProductVariant, Serial, Order, OrderItem, Payment, Cart, Review, AiConversation...', '#F59E0B', '#FFFBEB', 460),
        ('6. STORAGE & EXTERNAL SERVICES', 'MySQL 8.0 / SQLite Database | Google Gemini API Engine | VNPAY Sandbox Gateway | Mail OTP Service', '#8B5CF6', '#F5F3FF', 550)
    ]

    for title, desc, border_col, bg_col, ly in layers:
        draw.rounded_rectangle([40, ly, W - 40, ly + 72], radius=10, fill=bg_col, outline=border_col, width=2)
        draw.text((60, ly + 14), title, fill=border_col, font=font_box)
        draw.text((60, ly + 40), desc, fill='#334155', font=font_sub)

    img.save('E:/doantichhop/CodeNhom1/NHOM1_BE_QLBTBDT/docs/images/system_architecture.png', 'PNG', dpi=(150, 150))
    print('4. Created system_architecture.png')

create_checkout_flow()
create_ai_flow()
create_serial_flow()
create_architecture_diagram()
