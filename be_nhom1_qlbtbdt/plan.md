# KẾ HOẠCH TRIỂN KHAI HỆ THỐNG BACKEND (PLAN.MD)
## DỰ ÁN: WEBSITE QUẢN LÝ BÁN HÀNG THIẾT BỊ ĐIỆN TỬ TÍCH HỢP AI TƯ VẤN KHÁCH HÀNG
**Mã dự án:** `WQBH-TBĐT-AI` | **Repository:** `NHOM1_BE_QLBTBDT`  
**Giảng viên hướng dẫn:** ThS. Nguyễn Phúc Minh Tú  
**Đơn vị:** Khoa Công Nghệ Thông Tin - Đại học Duy Tân  
**Mô hình phát triển:** Scrum / Agile (2 Sprints chính, 10/2026 - 12/2026)

---

## 📌 QUY ƯỚC TRẠNG THÁI TIẾN ĐỘ
- `[x]` : **ĐÃ HOÀN THÀNH (DONE)**
- `[ ]` : **CHƯA THỰC HIỆN / ĐANG CHỜ TRIỂN KHAI (TODO / IN PROGRESS)**

---

## 1. TỔNG QUAN DỰ ÁN & CĂN CỨ TÀI LIỆU
Kế hoạch triển khai Back-end này được tổng hợp và đối soát trực tiếp từ 4 tài liệu dự án chính thức:
1. `0125_CS445_SE_01_Product Proposal.docx`: Đề xuất đề tài, mục tiêu số hóa chuỗi bán lẻ thiết bị điện tử, phạm vi chức năng và kỹ thuật tích hợp AI tư vấn cấu hình, thanh toán VNPay/PayOS.
2. `0126_CS445_SE_02-UserStory.docx`: Bộ 21 User Story đặc tả chi tiết chân dung người dùng (Guest, Customer, Staff, Admin) kèm Acceptance Criteria (AC).
3. `0126_CS445_SE_03-ProductBacklog.docx`: Danh mục Product Backlog Item (PB01 - PB21), ước lượng Story Points, định nghĩa độ ưu tiên và phân bổ Sprint.
4. `0126_CS445_SE_04-ProjectPlan.docx`: Kế hoạch dự án tổng thể, lịch trình WBS 442 giờ công, quản lý rủi ro và tiêu chuẩn kiểm thử chất lượng (QA).

---

## 2. ĐỘI NGŨ THỰC HIỆN & PHÂN CÔNG VAI TRÒ
Hệ thống vận hành theo khung Scrum chuẩn với 5 thành viên nòng cốt:

| STT | Thành viên | Vai trò Scrum | Trách nhiệm chính trong Backend (NHOM1_BE_QLBTBDT) |
|:---:|:---|:---|:---|
| 1 | **Nguyễn Văn Kỳ** | Scrum Master / Technical Lead | Quản lý tiến độ dự án, thiết kế Kiến trúc hệ thống, phân quyền RBAC, tích hợp API Trợ lý ảo AI (Google Gemini / Groq LLM Proxy). |
| 2 | **Võ Đình Huy** | Developer / Backend Specialist | Xây dựng Database Schema, ORM Eloquent, Module Sản phẩm, Danh mục, Biến thể cấu hình & Quản lý Serial/IMEI. |
| 3 | **Lê Quốc Khánh** | Developer / API Specialist | Phát triển API Giỏ hàng, Đặt hàng (Checkout), Xử lý Voucher và Tích hợp cổng thanh toán trực tuyến VNPAY / PayOS (IPN & Webhook). |
| 4 | **Phạm Tiến Ninh** | Developer / Fullstack & API Integration | Xây dựng API Quản lý tài khoản, Hồ sơ người dùng, Sổ địa chỉ, Đánh giá/Bình luận và phối hợp tích hợp Front-end. |
| 5 | **Dương Văn Trọng Đại** | QA / Tester / Data Engineer | Thiết kế Test Plan, viết Unit/Feature Tests (PHPUnit), kiểm thử tải API, kiểm thử luồng gán Serial/IMEI và Dashboard Báo cáo BI. |

---

## 3. CHECKLIST CÁC CÔNG VIỆC CHI TIẾT (TASK BREAKDOWN & PROGRESS)

### GIAI ĐOẠN 1: KHỞI TẠO NỀN TẢNG & CƠ SỞ DỮ LIỆU (SPRINT 0)
- [x] **1.1 Cấu hình Project Laravel 11 & Môi trường**
  - [x] Khởi tạo source code Laravel 11.x, thiết lập `.env` (SQLite/MySQL, App Key, App URL).
  - [x] Cấu hình CORS, Session, Cache và Timezone `Asia/Ho_Chi_Minh`.
  - [x] Cài đặt dependencies qua Composer (`laravel/sanctum`, v.v.).
- [x] **1.2 Thiết kế và tạo Migrations (17 Bảng CSDL Chuẩn Hóa)**
  - [x] `0001_01_01_000000_create_users_table.php` (Bảng `users` / `nguoi_dung`: Quản lý tài khoản, vai trò admin/staff/customer).
  - [x] `2026_10_31_000001_create_dia_chi_nguoi_dung_table.php` (Sổ địa chỉ nhận hàng).
  - [x] `2026_10_31_000002_create_thuong_hieu_table.php` (Thương hiệu: Apple, Asus, Dell, HP, Lenovo...).
  - [x] `2026_10_31_000003_create_danh_muc_table.php` (Danh mục phân cấp cha - con).
  - [x] `2026_10_31_000004_create_san_pham_table.php` (Sản phẩm cốt lõi, giá niêm yết, hạn bảo hành).
  - [x] `2026_10_31_000005_create_hinh_anh_san_pham_table.php` (Thư viện ảnh sản phẩm).
  - [x] `2026_10_31_000006_create_thong_so_ky_thuat_table.php` (Chi tiết CPU, RAM, GPU, Ổ cứng, Màn hình, JSON mở rộng cho AI).
  - [x] `2026_10_31_000007_create_bien_the_san_pham_table.php` (Biến thể cấu hình/màu sắc, SKU, giá bán, tồn kho).
  - [x] `2026_10_31_000008_create_ma_giam_gia_table.php` (Mã Voucher khuyến mãi % hoặc tiền mặt cố định).
  - [x] `2026_10_31_000009_create_don_hang_table.php` (Đơn đặt hàng, thông tin giao nhận, trạng thái vận chuyển và thanh toán).
  - [x] `2026_10_31_000010_create_chi_tiet_don_hang_table.php` (Chi tiết mặt hàng và giá bán từng item trong đơn).
  - [x] `2026_10_31_000011_create_serial_san_pham_table.php` (Quản lý Serial/IMEI từng máy phục vụ bảo hành điện tử).
  - [x] `2026_10_31_000012_create_thanh_toan_table.php` (Lịch sử giao dịch VNPAY/PayOS/COD).
  - [x] `2026_10_31_000013_create_gio_hang_table.php` (Giỏ hàng người dùng và khách vãng lai).
  - [x] `2026_10_31_000014_create_danh_gia_table.php` (Đánh giá 1 - 5 sao và nhận xét phản hồi).
  - [x] `2026_10_31_000015_create_hoi_thoai_ai_table.php` (Phiên hội thoại AI Chatbot).
  - [x] `2026_10_31_000016_create_tin_nhan_ai_table.php` (Nội dung tin nhắn người dùng và phản hồi của AI kèm sản phẩm đề xuất).
- [x] **1.3 Tạo Data Seeders (Bộ Dữ Liệu Mẫu Phong Phú)**
  - [x] `DatabaseSeeder.php` (Điều phối chạy toàn bộ seeders theo đúng thứ tự khóa ngoại).
  - [x] `NguoiDungSeeder.php` (Tài khoản mẫu: Admin, Staff bán hàng, Staff kho, Khách hàng).
  - [x] `DiaChiNguoiDungSeeder.php` (Địa chỉ giao hàng mẫu tại Đà Nẵng, Hà Nội, TP.HCM).
  - [x] `ThuongHieuSeeder.php` (Đầy đủ logo, mô tả các hãng Apple, Asus, Dell, MSI, Lenovo, Samsung).
  - [x] `DanhMucSeeder.php` (Laptop Gaming, Laptop Văn phòng, Smartphone, Tablet, Linh kiện, Phụ kiện).
  - [x] `SanPhamSeeder.php` (Dữ liệu sản phẩm thực tế: MacBook Pro M3, Asus ROG, Dell XPS, iPhone 15...).
  - [x] `ThongSoKyThuatSeeder.php` (Thông số chi tiết chip, xung nhịp, RAM bus, card đồ họa phục vụ lọc kỹ thuật và AI).
  - [x] `BienTheSanPhamSeeder.php` (Các phiên bản dung lượng 256GB/512GB/1TB, RAM 16GB/32GB).
  - [x] `HinhAnhSanPhamSeeder.php` (Ảnh góc chụp nét cho từng sản phẩm).
  - [x] `SerialSanPhamSeeder.php` (Mã Serial/IMEI mẫu cho từng máy trong kho).
  - [x] `MaGiamGiaSeeder.php` (Voucher giảm giá khai trương, voucher sinh nhật, flash sale).
  - [x] `DonHangSeeder.php` & `ChiTietDonHangSeeder.php` (Đơn hàng mẫu với các trạng thái khác nhau).
  - [x] `ThanhToanSeeder.php` (Lịch sử thanh toán mẫu qua VNPAY và COD).
  - [x] `GioHangSeeder.php` (Giỏ hàng mẫu có sẵn sản phẩm).
  - [x] `DanhGiaSeeder.php` (Review nhận xét 5 sao thực tế từ khách hàng).
  - [x] `HoiThoaiAiSeeder.php` & `TinNhanAiSeeder.php` (Lịch sử chat mẫu với AI tư vấn cấu hình).
- [x] **1.4 Xây dựng Models Eloquent ORM & Relationships**
  - [x] Các Models cốt lõi: `User`, `Product`, `Category`, `Brand`, `Order`, `OrderItem`, `Cart`, `Review`, `Voucher`, `ProductVariant`, `ProductSerial`, `ProductSpecification`, `Payment`, `AiConversation`, `AiMessage`.
  - [x] Khai báo quan hệ: `hasMany`, `belongsTo`, `belongsToMany`, `hasOne`.

---

### GIAI ĐOẠN 2: PHÁT TRIỂN API BACKEND (SPRINT 1 - 31/10 ĐẾN 25/11/2026)
*Tập trung: Xác thực, Danh mục, Sản phẩm, Giỏ hàng, Đặt hàng & Cổng thanh toán VNPAY.*

#### Phân hệ 1: Xác thực & Quản lý Tài khoản (Auth & Accounts) - PB01, PB02, PB03, PB18
- [ ] Tạo `AuthController`:
  - [ ] `POST /api/auth/register`: Đăng ký tài khoản khách hàng mới, validate dữ liệu (Name, Email, Password >= 6 ký tự).
  - [ ] `POST /api/auth/login`: Đăng nhập hệ thống, kiểm tra mật khẩu hash Bcrypt, phát hành Token Sanctum/JWT.
  - [ ] `POST /api/auth/logout`: Thu hồi Token và xóa phiên đăng nhập.
  - [ ] `GET /api/auth/me`: Lấy thông tin tài khoản hiện tại kèm quyền hạn (Role).
- [ ] Tạo `PasswordResetController`:
  - [ ] `POST /api/auth/forgot-password`: Gửi mã token OTP xác thực qua email.
  - [ ] `POST /api/auth/reset-password`: Xác thực OTP và cập nhật mật khẩu mới.
- [ ] Tạo `AccountManagementController` (Admin):
  - [ ] `GET /api/admin/users`: Danh sách tài khoản kèm bộ lọc vai trò, trạng thái.
  - [ ] `POST /api/admin/users`: Tạo tài khoản nhân viên mới (cấp quyền sales, kho).
  - [ ] `PUT /api/admin/users/{id}`: Cập nhật thông tin / khóa tài khoản vi phạm.
  - [ ] `DELETE /api/admin/users/{id}`: Xóa tài khoản người dùng.

#### Phân hệ 2: Danh mục, Thương hiệu & Sản phẩm (Catalog & Products) - PB04, PB05, PB06, PB14, PB15, PB16
- [ ] Tạo `CategoryController`:
  - [ ] `GET /api/categories`: Danh sách danh mục đa cấp (Laptop, Smartphone, Linh kiện...).
  - [ ] `POST /api/admin/categories`: Thêm danh mục mới (Admin).
  - [ ] `PUT /api/admin/categories/{id}`: Sửa danh mục (Admin).
  - [ ] `DELETE /api/admin/categories/{id}`: Xóa danh mục (Admin).
- [ ] Tạo `BrandController`:
  - [ ] `GET /api/brands`: Danh sách thương hiệu kèm logo.
  - [ ] `POST/PUT/DELETE /api/admin/brands`: Thao tác CRUD thương hiệu (Admin).
- [ ] Tạo `ProductController`:
  - [ ] `GET /api/products`: Danh sách sản phẩm trang chủ, phân trang, sắp xếp theo giá/độ mới.
  - [ ] `GET /api/products/filter`: Bộ lọc kỹ thuật đa chiều (CPU, RAM, VGA, Ổ cứng, Mức giá, Thương hiệu).
  - [ ] `GET /api/products/{slug}`: Chi tiết thông số kỹ thuật, biến thể cấu hình, album ảnh và đánh giá sao.
  - [ ] `POST /api/admin/products`: Thêm sản phẩm mới kèm cấu hình và biến thể (Admin/Staff).
  - [ ] `PUT /api/admin/products/{id}`: Cập nhật thông tin, giá bán, bảo hành (Admin/Staff).
  - [ ] `DELETE /api/admin/products/{id}`: Ẩn/Xóa sản phẩm (Admin/Staff).

#### Phân hệ 3: Giỏ hàng & Voucher Khuyến mãi (Cart & Voucher) - PB08, PB13, PB17
- [ ] Tạo `CartController`:
  - [ ] `GET /api/cart`: Lấy danh sách sản phẩm trong giỏ kèm giá, hình ảnh và tổng tiền tạm tính.
  - [ ] `POST /api/cart/add`: Thêm sản phẩm/biến thể vào giỏ (kiểm tra tồn kho tức thời).
  - [ ] `PUT /api/cart/update`: Cập nhật số lượng item trong giỏ.
  - [ ] `DELETE /api/cart/remove/{id}`: Xóa từng item khỏi giỏ hàng.
  - [ ] `DELETE /api/cart/clear`: Xóa toàn bộ giỏ hàng.
- [ ] Tạo `VoucherController`:
  - [ ] `GET /api/vouchers/active`: Danh sách mã giảm giá đang có hiệu lực.
  - [ ] `POST /api/vouchers/apply`: Kiểm tra tính hợp lệ của mã giảm giá (giá trị tối thiểu, hạn dùng, số lần còn lại) và tính số tiền được chiết khấu.
  - [ ] `CRUD /api/admin/vouchers`: Quản lý tạo mã khuyến mãi, cấu hình thời gian flash sale (Admin).

#### Phân hệ 4: Đặt hàng & Cổng thanh toán Trực tuyến (Checkout & Payment) - PB06, PB12
- [ ] Tạo `OrderCheckoutController`:
  - [ ] `POST /api/orders/checkout`: Tạo đơn đặt hàng mới qua Database Transaction (khóa tồn kho, lưu người nhận, lưu chi tiết đơn hàng).
  - [ ] Xử lý phương thức thanh toán: COD (Thanh toán khi nhận hàng) hoặc VNPAY.
- [ ] Tạo `VnPayController`:
  - [ ] `POST /api/payment/vnpay/create-url`: Khởi tạo URL chuyển hướng sang cổng VNPAY với chữ ký bảo mật HMAC-SHA512.
  - [ ] `GET /api/payment/vnpay/return`: Xử lý khi khách hàng hoàn tất giao dịch và quay trở lại website (hiển thị kết quả thành công/thất bại).
  - [ ] `GET /api/payment/vnpay/ipn`: Xử lý Webhook IPN chạy ngầm giữa Server VNPAY và Backend để cập nhật trạng thái đơn hàng an toàn tuyệt đối.

---

### GIAI ĐOẠN 3: HOÀN THIỆN TÍNH NĂNG NÂNG CAO (SPRINT 2 - 26/11 ĐẾN 24/12/2026)
*Tập trung: Quản lý Kho Serial/IMEI, Trợ lý AI Chatbot Gemini, Tra cứu bảo hành, Quản lý đơn hàng & Báo cáo BI.*

#### Phân hệ 5: Quản lý Đơn hàng, Hủy đơn & Lịch sử mua (Orders & Tracking) - PB09, PB10, PB11, PB19
- [ ] `GET /api/user/orders`: Lịch sử đơn hàng của người dùng đang đăng nhập.
- [ ] `GET /api/user/orders/{code}`: Chi tiết đơn hàng, timeline vận chuyển và dải Serial/IMEI máy đã mua.
- [ ] `POST /api/user/orders/{code}/cancel`: Khách hàng hủy đơn hàng (khi đơn chưa qua bước đóng gói/xuất kho).
- [ ] Tạo `AdminOrderController` (Staff / Admin):
  - [ ] `GET /api/admin/orders`: Quản lý danh sách đơn hàng toàn hệ thống với bộ lọc trạng thái.
  - [ ] `PUT /api/admin/orders/{id}/status`: Cập nhật tiến trình đơn (Chờ duyệt -> Đã xác nhận -> Đang đóng gói -> Đang giao -> Đã giao).
  - [ ] `POST /api/admin/orders/{id}/assign-serial`: Gán mã Serial/IMEI vật lý từ kho vào từng máy trong đơn trước khi xuất kho.

#### Phân hệ 6: Quản lý Kho Serial/IMEI & Tra cứu Bảo hành Điện tử
- [ ] Tạo `ProductSerialController`:
  - [ ] `GET /api/admin/serials`: Danh sách quản lý Serial/IMEI theo từng biến thể sản phẩm.
  - [ ] `POST /api/admin/serials/import`: Nhập kho lô hàng kèm danh sách Serial/IMEI (hỗ trợ nhập đơn hoặc import Excel).
  - [ ] `GET /api/warranty/check`: Tra cứu bảo hành công khai bằng cách nhập mã Serial/IMEI (hiển thị ngày xuất kho, ngày hết hạn bảo hành).
  - [ ] `POST /api/admin/warranty/claim`: Tiếp nhận và lập phiếu bảo hành/đổi trả cho máy có Serial gặp lỗi kỹ thuật.

#### Phân hệ 7: Quản lý Hồ sơ Cá nhân & Đánh giá (Profile & Reviews) - PB07
- [ ] Tạo `UserProfileController`:
  - [ ] `PUT /api/user/profile`: Cập nhật họ tên, số điện thoại, ảnh đại diện.
  - [ ] `CRUD /api/user/addresses`: Quản lý sổ địa chỉ nhận hàng (thêm địa chỉ mặc định).
- [ ] Tạo `ReviewController`:
  - [ ] `POST /api/products/{id}/reviews`: Khách hàng gửi đánh giá 1-5 sao và cảm nhận sau khi mua hàng thành công.
  - [ ] `PUT /api/admin/reviews/{id}/moderate`: Admin duyệt hoặc ẩn bình luận rác, spam.

#### Phân hệ 8: Trợ lý AI Tư vấn Thiết bị Điện tử (AI Consultant Engine) - PB21
- [ ] Tạo `AiService` tích hợp API Trí tuệ nhân tạo (Google Gemini API / Groq LLM):
  - [ ] Xây dựng System Prompt chuyên biệt cho tư vấn thiết bị điện tử (Laptop Gaming, Đồ họa, Sinh viên, Văn phòng, Ngân sách từ 10tr - 50tr).
  - [ ] Xây dựng cơ chế Function Calling / RAG nội bộ: Cho phép AI truy vấn thông số thực tế từ bảng `thong_so_ky_thuat` và `bien_the_san_pham` để trả lời chính xác giá và còn hàng.
- [ ] Tạo `AiChatController`:
  - [ ] `POST /api/ai/chat`: Nhận câu hỏi người dùng, lưu lịch sử vào `hoi_thoai_ai` và `tin_nhan_ai`, trả về câu trả lời kèm danh sách ID sản phẩm đề xuất.
  - [ ] `GET /api/ai/history`: Lấy lại lịch sử trò chuyện của phiên người dùng.

#### Phân hệ 9: Dashboard Báo cáo Thống kê Quản trị (BI & Analytics) - PB20
- [ ] Tạo `AnalyticsController` (Admin):
  - [ ] `GET /api/admin/analytics/overview`: Tổng doanh thu, tổng số đơn hàng, tổng số khách hàng, số lượng máy tồn kho.
  - [ ] `GET /api/admin/analytics/revenue-chart`: Doanh số theo ngày, tuần, tháng phục vụ vẽ biểu đồ Chart.js.
  - [ ] `GET /api/admin/analytics/top-products`: Top 5 laptop / điện thoại bán chạy nhất.
  - [ ] `GET /api/admin/analytics/inventory-alert`: Cảnh báo các thiết bị sắp hết hàng trong kho.

---

### GIAI ĐOẠN 4: KIỂM THỬ, TỐI ƯU & NGHIỆM THU (QA & DEPLOYMENT)
- [ ] **Kiểm thử tự động (Automated Testing)**
  - [ ] Viết Unit Test cho các Service: Tính tiền giỏ hàng, áp mã Voucher, sinh mã Serial/IMEI.
  - [ ] Viết Feature Test cho API luồng chính: Đăng ký -> Đăng nhập -> Thêm giỏ -> Checkout -> Callback VNPAY.
- [ ] **Tối ưu hóa & Bảo mật**
  - [ ] Bật Cache Redis / Laravel Cache cho danh mục và thông số kỹ thuật sản phẩm tĩnh.
  - [ ] Áp dụng Rate Limiting chống DDoS và giới hạn gọi API Chatbot AI (`throttle:60,1`).
  - [ ] Kiểm tra phân quyền Policies: Đảm bảo khách hàng không truy cập được dữ liệu đơn hàng người khác, nhân viên không truy cập được thống kê tài chính Admin.
- [ ] **Đóng gói & Bàn giao Tài liệu**
  - [ ] Export bộ Postman Collection kèm Environment biến môi trường hoàn chỉnh.
  - [ ] Viết tài liệu hướng dẫn chạy Seeder dữ liệu mẫu cho các thành viên Frontend kết nối.
  - [ ] Hoàn tất báo cáo nghiệm thu Sprint Review và bàn giao sản phẩm v1.0.

---
*Tài liệu được cập nhật tự động theo tiến độ thực tế của repository `NHOM1_BE_QLBTBDT`.*
