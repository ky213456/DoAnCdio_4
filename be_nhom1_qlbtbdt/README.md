# ⚡ TECHMART AI - HỆ THỐNG BACKEND QUẢN LÝ BÁN THIẾT BỊ ĐIỆN TỬ
### *Website E-Commerce Điện Máy Tích Hợp Trợ Lý Ảo AI Tư Vấn Cấu Hình & Cổng Thanh Toán Trực Tuyến*

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+" />
  <img src="https://img.shields.io/badge/Database-MySQL%20%2F%20SQLite-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="Database" />
  <img src="https://img.shields.io/badge/AI_Engine-Google%20Gemini%20API-4285F4?style=for-the-badge&logo=google&logoColor=white" alt="Gemini AI" />
  <img src="https://img.shields.io/badge/Payment-VNPAY%20Sandbox-005BAA?style=for-the-badge" alt="VNPAY" />
  <img src="https://img.shields.io/badge/Architecture-RESTful%20API%20%26%20Service--Repo-00C7B7?style=for-the-badge" alt="Clean Architecture" />
</p>

---

## 📖 MỤC LỤC
1. [Giới thiệu Dự án](#-giới-thiệu-dự-án)
2. [Điểm nhấn Công nghệ Cốt lõi](#-điểm-nhấn-công-nghệ-cốt-lõi)
3. [Đội ngũ Phát triển & Phân vai (Nhóm 1)](#-đội-ngũ-phát-triển--phân-vai-nhóm-1)
4. [Sơ đồ Kiến trúc Phân tầng Hệ thống (System Architecture)](#-sơ-đồ-kiến-trúc-phân-tầng-hệ-thống)
5. [Sơ đồ Các Quy trình Nghiệp vụ Cốt lõi (Workflow Diagrams)](#-sơ-đồ-các-quy-trình-nghiệp-vụ-cốt-lõi)
   - [5.1 Quy trình Đặt hàng & Thanh toán VNPAY (Checkout Flow)](#51-quy-trình-đặt-hàng--thanh-toán-vnpay-sandbox)
   - [5.2 Kiến trúc Trợ lý ảo AI tư vấn cấu hình (AI Engine Flow)](#52-kiến-trúc-trợ-lý-ảo-ai-tư-vấn-cấu-hình-gemini-api)
   - [5.3 Quy trình Quản lý Serial/IMEI & Bảo hành điện tử (Warranty Flow)](#53-quy-trình-quản-lý-kho-serialimei--bảo-hành-điện-tử)
6. [Cấu trúc Thư mục Mã nguồn Dự án (Directory Structure)](#-cấu-trúc-thư-mục-mã-nguồn-dự-án)
7. [Cơ sở Dữ liệu Chuẩn hóa (17 Bảng Tiếng Việt)](#-cơ-sở-dữ-liệu-chuẩn-hóa-17-bảng-tiếng-việt)
8. [Hệ thống Danh mục API (Endpoints Map)](#-hệ-thống-danh-mục-api-endpoints-map)
9. [Hướng dẫn Cài đặt & Khởi chạy (Quick Start)](#-hướng-dẫn-cài-đặt--khởi-chạy-quick-start)
10. [Tài liệu & Kế hoạch Dự án](#-tài-liệu--kế-hoạch-dự-án)

---

## 📌 GIỚI THIỆU DỰ ÁN
**TECHMART AI** là giải pháp phần mềm Back-end phục vụ hệ sinh thái thương mại điện tử chuyên ngành thiết bị công nghệ (Laptop Gaming, Ultrabook, Smartphone, Linh kiện PC, Phụ kiện số). Hệ thống giải quyết trọn vẹn chuỗi giá trị bán lẻ:
- **Khách hàng (Customer / Guest):** Tìm kiếm và lọc cấu hình đa chiều, so sánh thông số kỹ thuật, nhận tư vấn cấu hình tự động 24/7 từ Trợ lý AI, thanh toán bảo mật tức thời qua VNPAY và tra cứu lịch sử bảo hành theo mã Serial/IMEI.
- **Nhân viên Bán hàng & Chăm sóc (Sales / CSKH):** Quản lý trạng thái giỏ hàng, xác nhận đơn hàng, áp dụng Voucher khuyến mãi, xử lý đánh giá bình luận của khách.
- **Nhân viên Kho & Kỹ thuật (Warehouse & Warranty):** Quản lý nhập kho, dán và cấp phát dải mã Serial/IMEI vật lý cho từng máy, kích hoạt bảo hành điện tử và xử lý quy trình đổi trả hàng lỗi.
- **Quản trị viên (Admin):** Kiểm soát toàn quyền hệ thống, phân quyền vai trò (RBAC), quản lý nhân sự, xem dashboard phân tích doanh thu và báo cáo Business Intelligence (BI).

---

## ✨ ĐIỂM NHẤN CÔNG NGHỆ CỐT LÕI

| Tính năng cốt lõi | Mô tả giải pháp kỹ thuật |
|:---|:---|
| 🤖 **AI Smart Consultant** | Tích hợp **Google Gemini API** qua Service Proxy trung gian. Hệ thống xây dựng System Prompt chuyên sâu về phần cứng kết hợp RAG nội bộ để đọc dữ liệu thông số kỹ thuật (CPU, RAM, VGA, mức giá), tự động gợi ý thiết bị tối ưu theo ngân sách và mục đích sử dụng (học tập, đồ họa 3D, lập trình, gaming). |
| 💳 **VNPAY Gateway** | Tích hợp cổng thanh toán trực tuyến **VNPAY Sandbox** chuẩn HMAC-SHA512. Xử lý đồng bộ dữ liệu giao dịch qua Webhook IPN chạy ngầm, đảm bảo an toàn tuyệt đối, chống gian lận và tự động hóa cập nhật trạng thái đơn hàng. |
| 🏷️ **Quản lý Serial / IMEI** | Mỗi thiết bị xuất bán đều được gán mã định danh phần cứng (Serial/IMEI) duy nhất. Cho phép người dùng tra cứu hạn bảo hành trực tuyến và giúp thủ kho kiểm soát chính xác tồn kho theo từng máy vật lý. |
| ⚡ **Clean Architecture** | Áp dụng mô hình **Service - Repository Pattern** trên nền Laravel 11 giúp tách biệt rành mạch giữa Controller, Business Logic và Database Layer, dễ dàng mở rộng và bảo trì. |

---

## 👥 ĐỘI NGŨ PHÁT TRIỂN & PHÂN VAI (NHÓM 1)

> **Học phần:** Đồ án Công nghệ Thông tin / Kỹ thuật Phần mềm (SE447 G) - Đại học Duy Tân  
> **Giảng viên hướng dẫn:** ThS. Nguyễn Phúc Minh Tú

| STT | Thành viên | Vai trò Scrum | Phân công chuyên môn chính |
|:---:|:---|:---|:---|
| 1 | **Nguyễn Văn Kỳ** | **Scrum Master / Tech Lead** | Kiến trúc hệ thống, Xác thực JWT/Sanctum, Phân quyền RBAC, Tích hợp AI Consultant Engine (Gemini API). |
| 2 | **Võ Đình Huy** | **Backend Developer** | Thiết kế CSDL 17 bảng, Eloquent ORM, Module Danh mục, Sản phẩm, Biến thể cấu hình & Quản lý dải Serial/IMEI. |
| 3 | **Lê Quốc Khánh** | **API Specialist** | Module Giỏ hàng, Quy trình Checkout đơn hàng, Voucher khuyến mãi & Tích hợp cổng VNPAY / PayOS (IPN Webhook). |
| 4 | **Phạm Tiến Ninh** | **Fullstack & API Integration** | Quản lý tài khoản, Hồ sơ cá nhân, Sổ địa chỉ nhận hàng, Module Đánh giá / Bình luận và kết nối Front-end. |
| 5 | **Dương Văn Trọng Đại** | **QA / Tester / BI Specialist** | Thiết kế Test Plan, viết Unit/Feature Tests (PHPUnit), kiểm thử tải API và Xây dựng Dashboard Báo cáo Thống kê BI. |

---

## 🏛️ SƠ ĐỒ KIẾN TRÚC PHÂN TẦNG HỆ THỐNG

<p align="center">
  <img src="docs/images/system_architecture.png" alt="Kiến trúc Phân tầng Hệ thống Backend TechMart AI" width="95%" />
</p>

### Chi tiết các tầng kiến trúc:
1. **Client Presentation Layer:** Ứng dụng Web Storefront (React 19 / Vue 3), Mobile Web PWA và Postman Client kết nối thông qua giao thức bảo mật HTTPS/JSON RESTful API.
2. **API Routing & Security Layer:** Router Laravel 11 (`routes/api.php`), tầng xác thực Token Bearer (Laravel Sanctum), Middleware kiểm tra phân quyền người dùng (`CheckRole:admin,staff,customer`) và Rate Limiting chống DDoS / Spam API.
3. **Controller & Validation Layer:** Tiếp nhận HTTP Requests, chuẩn hóa dữ liệu thông qua Form Requests, điều hướng logic và trả về JSON chuẩn theo RESTful.
4. **Service & Business Logic Layer:** Xử lý nghiệp vụ chuyên sâu độc lập (`OrderService`, `VnPayService`, `AiService`, `SerialWarrantyService`).
5. **Eloquent ORM & Repository Layer:** Đóng gói truy vấn với 17 Models Eloquent, quản lý quan hệ bảng phức tạp và xử lý Database Transaction an toàn.
6. **Storage & External Services:** Hệ CSDL MySQL 8.0 / SQLite, Google Gemini LLM API, Cổng VNPAY Sandbox và Dịch vụ gửi Mail thông báo OTP.

---

## 🔄 SƠ ĐỒ CÁC QUY TRÌNH NGHIỆP VỤ CỐT LÕI

### 5.1 Quy trình Đặt hàng & Thanh toán VNPAY Sandbox

<p align="center">
  <img src="docs/images/flow_checkout_vnpay.png" alt="Quy trình Đặt hàng và Thanh toán VNPAY" width="95%" />
</p>

```mermaid
sequenceDiagram
    autonumber
    actor User as Khách hàng
    participant FE as Frontend Storefront
    participant API as Order & Payment API
    participant DB as CSDL (MySQL)
    participant VNP as Cổng VNPAY Sandbox

    User->>FE: Nhấn "Đặt hàng & Thanh toán"
    FE->>API: POST /api/orders/checkout (Giỏ hàng, Địa chỉ, Voucher)
    activate API
    API->>DB: Bắt đầu DB Transaction: Tạo Đơn hàng & Trừ tồn kho biến thể
    DB-->>API: Trả về order_id (Trạng thái: "cho_thanh_toan")
    API->>API: Tạo URL thanh toán với chữ ký số HMAC-SHA512
    API-->>FE: Trả về payment_url VNPAY
    deactivate API
    FE->>VNP: Chuyển hướng người dùng sang cổng VNPAY
    User->>VNP: Quét mã VietQR hoặc nhập thẻ ATM Sandbox
    VNP-->>FE: Redirect về Return URL: /api/payment/vnpay/return
    VNP-)API: Gửi IPN Webhook ngầm thông báo giao dịch thành công (Checksum hợp lệ)
    activate API
    API->>DB: Cập nhật don_hang ("da_thanh_toan") & thanh_toan ("thanh_cong")
    API-->>VNP: Phản hồi RspCode 00 (Xác nhận IPN)
    deactivate API
    FE-->>User: Hiển thị màn hình đặt hàng và thanh toán thành công
```

---

### 5.2 Kiến trúc Trợ lý ảo AI Tư vấn Cấu hình (Gemini API)

<p align="center">
  <img src="docs/images/flow_ai_architecture.png" alt="Kiến trúc Trợ lý AI Tư vấn Cấu hình Thiết bị" width="95%" />
</p>

```mermaid
flowchart LR
    A["👤 Khách hàng<br/>(Nhập nhu cầu: Laptop đồ họa 25tr)"] --> B["🌐 Frontend Web<br/>(Floating Chat Widget)"]
    B -->|POST /api/ai/chat| C["⚙️ AiChatController<br/>(Auth & Lưu tin nhắn)"]
    C --> D["🧠 AiService (RAG Engine)<br/>- System Prompt kĩ thuật điện máy<br/>- Query thong_so_ky_thuat & bien_the"]
    D -->|Context phần cứng + Câu hỏi| E["☁️ Google Gemini API<br/>(Mô hình Gemini 1.5 Flash / Pro)"]
    E -->|Câu trả lời + ID máy gợi ý| D
    D --> C
    C -->|Lưu tin_nhan_ai & trả JSON| B
    B --> A
```

---

### 5.3 Quy trình Quản lý Kho Serial/IMEI & Bảo hành Điện tử

<p align="center">
  <img src="docs/images/flow_serial_warranty.png" alt="Quy trình Quản lý Serial/IMEI và Bảo hành Điện tử" width="95%" />
</p>

- **Giai đoạn Nhập kho:** Thủ kho nhập lô hàng máy mới, hệ thống tự động sinh dải Serial/IMEI duy nhất cho từng máy với trạng thái `san_sang_ban`.
- **Giai đoạn Xuất bán:** Khi đơn hàng hoàn tất đóng gói, nhân viên quét gán mã Serial/IMEI cụ thể vào `chi_tiet_don_hang`. Trạng thái máy chuyển thành `da_ban` và thời gian bảo hành bắt đầu được tính tự động (12 - 24 tháng theo chính sách).
- **Giai đoạn Tra cứu & Bảo hành:** Khách hàng hoặc kỹ thuật viên chỉ cần nhập mã Serial/IMEI trên website (`GET /api/warranty/check`) là có thể kiểm tra chính xác ngày xuất kho, tên khách hàng và thời hạn bảo hành còn lại để làm thủ tục sửa chữa/đổi trả.

---

## 📁 CẤU TRÚC THƯ MỤC MÃ NGUỒN DỰ ÁN

```
NHOM1_BE_QLBTBDT/
├── app/
│   ├── Http/
│   │   ├── Controllers/             # Tầng điều khiển tiếp nhận & phản hồi HTTP API
│   │   │   ├── AuthController.php            # Đăng ký, Đăng nhập, JWT Token, Đăng xuất
│   │   │   ├── ProductController.php         # Danh sách, Chi tiết, Lọc cấu hình sản phẩm
│   │   │   ├── CategoryController.php        # Quản lý danh mục thiết bị cha - con
│   │   │   ├── BrandController.php           # Quản lý thương hiệu công nghệ
│   │   │   ├── CartController.php            # Thêm, sửa, xóa, tính tổng tiền giỏ hàng
│   │   │   ├── VoucherController.php         # Quản lý & áp dụng mã giảm giá khuyến mãi
│   │   │   ├── OrderController.php           # Tạo đơn hàng, Lịch sử mua & Hủy đơn
│   │   │   ├── VnPayController.php           # Khởi tạo URL, Xử lý Return URL & IPN Webhook
│   │   │   ├── ProductSerialController.php   # Quản lý dải Serial/IMEI & Tra cứu bảo hành
│   │   │   ├── AiChatController.php          # Tiếp nhận chat hội thoại tư vấn AI
│   │   │   └── AnalyticsController.php       # Báo cáo thống kê doanh số & BI Dashboard
│   │   └── Middleware/              # Kiểm tra xác thực Sanctum, phân quyền Role (RBAC)
│   ├── Models/                      # Tầng dữ liệu Eloquent ORM (17 Bảng quan hệ)
│   │   ├── User.php                 # Tài khoản (Admin, Staff, Customer)
│   │   ├── Product.php              # Sản phẩm cốt lõi
│   │   ├── ProductVariant.php       # Biến thể cấu hình RAM/SSD, Màu sắc, SKU
│   │   ├── ProductSpecification.php # Thông số kỹ thuật CPU, GPU, Màn hình, JSON
│   │   ├── ProductSerial.php        # Mã Serial/IMEI từng máy phục vụ bảo hành
│   │   ├── Order.php                # Đơn đặt hàng
│   │   ├── OrderItem.php            # Chi tiết từng mặt hàng trong đơn
│   │   ├── Payment.php              # Lịch sử giao dịch thanh toán
│   │   ├── Cart.php                 # Giỏ hàng người dùng
│   │   ├── Category.php             # Danh mục sản phẩm
│   │   ├── Brand.php                # Thương hiệu phần cứng
│   │   ├── Voucher.php              # Mã giảm giá
│   │   ├── Review.php               # Đánh giá 1-5 sao từ khách
│   │   ├── AiConversation.php       # Phiên hội thoại với Trợ lý ảo AI
│   │   └── AiMessage.php            # Chi tiết tin nhắn & gợi ý sản phẩm của AI
│   └── Services/                    # Tầng Business Logic chuyên sâu
│       ├── OrderService.php         # Nghiệp vụ tạo đơn hàng & khóa tồn kho
│       ├── VnPayService.php         # Thuật toán ký số HMAC-SHA512 & đối soát IPN
│       └── AiService.php            # Kết nối Google Gemini API & RAG Context
├── database/
│   ├── migrations/                  # 17 Migration scripts định nghĩa lược đồ CSDL chuẩn
│   ├── seeders/                     # 17 Seeders dữ liệu thực tế phong phú
│   └── database.sqlite              # CSDL SQLite phát triển nhanh
├── docs/
│   ├── images/                      # Sơ đồ quy trình & ảnh kiến trúc hệ thống
│   │   ├── system_architecture.png  # Ảnh sơ đồ phân tầng Backend
│   │   ├── flow_checkout_vnpay.png  # Ảnh quy trình đặt hàng và thanh toán VNPAY
│   │   ├── flow_ai_architecture.png # Ảnh kiến trúc Trợ lý ảo AI Gemini
│   │   └── flow_serial_warranty.png # Ảnh quy trình quản lý Serial/IMEI & bảo hành
│   └── generate_diagrams.py         # Script tự động sinh ảnh diagram độ phân giải cao
├── routes/
│   ├── api.php                      # Định tuyến toàn bộ RESTful API Endpoints
│   └── web.php                      # Trang chào đón Backend
├── tests/                           # Kiểm thử tự động PHPUnit (Unit & Feature Tests)
├── plan.md                          # Kế hoạch chi tiết & Checklist tiến độ Sprint Backlog
└── README.md                        # Tài liệu hướng dẫn dự án chuẩn Open-source
```

---

## 🗄️ CƠ SỞ DỮ LIỆU CHUẨN HÓA (17 BẢNG TIẾNG VIỆT)

Hệ thống được thiết kế theo chuẩn CSDL quan hệ (RDBMS) với 17 bảng được ánh xạ 100% bằng tiếng Việt chuẩn ngữ nghĩa:

| STT | Tên bảng | Chức năng nghiệp vụ chính |
|:---:|:---|:---|
| 1 | `nguoi_dung` | Lưu trữ tài khoản người dùng, email, mật khẩu mã hóa Bcrypt, vai trò (`admin`, `staff`, `customer`). |
| 2 | `dia_chi_nguoi_dung` | Sổ địa chỉ giao hàng của từng khách hàng (hỗ trợ nhiều địa chỉ, có cờ địa chỉ mặc định). |
| 3 | `thuong_hieu` | Danh sách các hãng công nghệ lớn: Apple, Asus, Dell, HP, Lenovo, MSI, Samsung... kèm logo. |
| 4 | `danh_muc` | Cây phân cấp danh mục thiết bị cha - con (Laptop, Điện thoại, Linh kiện, Phụ kiện). |
| 5 | `san_pham` | Thông tin gốc của sản phẩm: Tên, mã sản phẩm, giá niêm yết, thời gian bảo hành, mô tả chi tiết. |
| 6 | `hinh_anh_san_pham` | Bộ sưu tập hình ảnh chi tiết đa góc chụp của từng thiết bị. |
| 7 | `thong_so_ky_thuat` | Bảng cấu hình kỹ thuật: CPU, RAM, GPU, Ổ cứng, Màn hình, Pin, Trọng lượng & JSON mở rộng cho AI. |
| 8 | `bien_the_san_pham` | Các phiên bản tùy chọn cấu hình/màu sắc (VD: RAM 16GB vs 32GB, SSD 512GB vs 1TB), SKU, giá bán lẻ và tồn kho. |
| 9 | `serial_san_pham` | Quản lý chi tiết mã Serial/IMEI vật lý của từng chiếc máy trong kho phục vụ bảo hành điện tử. |
| 10 | `ma_giam_gia` | Quản lý Voucher khuyến mãi (loại giảm theo %, giảm tiền mặt cố định, hạn mức, ngày bắt đầu/kết thúc). |
| 11 | `don_hang` | Quản lý thông tin đơn đặt hàng, địa chỉ giao nhận, trạng thái xử lý, vận chuyển và tổng giá trị. |
| 12 | `chi_tiet_don_hang` | Danh sách từng sản phẩm/biến thể trong đơn hàng, giá bán tại thời điểm mua và số lượng. |
| 13 | `thanh_toan` | Nhật ký giao dịch tài chính: Mã giao dịch VNPAY, phương thức (COD/VNPAY), số tiền và trạng thái. |
| 14 | `gio_hang` | Giỏ hàng lưu trữ sản phẩm dự định mua của người dùng đã đăng nhập hoặc khách vãng lai. |
| 15 | `danh_gia` | Đánh giá 1 - 5 sao và nhận xét phản hồi thực tế từ khách hàng đã mua sản phẩm. |
| 16 | `hoi_thoai_ai` | Phiên trò chuyện tư vấn giữa khách hàng và Trợ lý ảo AI. |
| 17 | `tin_nhan_ai` | Chi tiết từng câu hỏi của khách và phản hồi thông minh từ AI kèm danh sách sản phẩm đề xuất. |

---

## 🔌 HỆ THỐNG DANH MỤC API (ENDPOINTS MAP)

### 1. Phân hệ Xác thực & Tài khoản (`/api/auth`)
- `POST /api/auth/register` : Đăng ký tài khoản khách hàng mới.
- `POST /api/auth/login` : Đăng nhập hệ thống, cấp Sanctum/JWT Token.
- `POST /api/auth/logout` : Đăng xuất, hủy bỏ phiên làm việc và thu hồi token.
- `GET  /api/auth/me` : Lấy thông tin tài khoản đang đăng nhập kèm phân quyền.
- `POST /api/auth/forgot-password` : Gửi mã OTP khôi phục mật khẩu qua Email.
- `POST /api/auth/reset-password` : Đặt lại mật khẩu mới thông qua mã OTP xác thực.

### 2. Phân hệ Khách hàng & Mua sắm (`/api`)
- `GET  /api/categories` : Lấy toàn bộ danh mục sản phẩm phân cấp.
- `GET  /api/brands` : Lấy danh sách thương hiệu và logo hãng.
- `GET  /api/products` : Danh sách sản phẩm phân trang, sắp xếp theo giá / bán chạy / mới nhất.
- `GET  /api/products/filter` : Bộ lọc kỹ thuật đa chiều (CPU, RAM, Card đồ họa, Tầm giá, Hãng).
- `GET  /api/products/{slug}` : Xem chi tiết sản phẩm, thông số phần cứng, các tùy chọn biến thể và đánh giá sao.
- `GET  /api/cart` : Lấy thông tin giỏ hàng hiện tại.
- `POST /api/cart/add` : Thêm sản phẩm/biến thể vào giỏ hàng (tự động kiểm tra tồn kho).
- `PUT  /api/cart/update` : Cập nhật số lượng sản phẩm trong giỏ hàng.
- `DELETE /api/cart/remove/{id}` : Xóa sản phẩm khỏi giỏ hàng.
- `POST /api/vouchers/apply` : Áp dụng mã giảm giá và tính toán số tiền chiết khấu.
- `POST /api/orders/checkout` : Tạo đơn đặt hàng mới qua Database Transaction an toàn.
- `POST /api/payment/vnpay/create-url` : Tạo URL thanh toán VNPAY mã hóa SHA512.
- `GET  /api/payment/vnpay/return` : Tiếp nhận kết quả thanh toán khi khách hàng quay lại Web.
- `GET  /api/payment/vnpay/ipn` : Webhook nhận thông báo giao dịch ngầm tự động từ VNPAY Server.
- `GET  /api/warranty/check?serial={code}` : Tra cứu bảo hành điện tử công khai theo số Serial/IMEI.

### 3. Phân hệ Trợ lý AI Tư vấn Cấu hình (`/api/ai`)
- `POST /api/ai/chat` : Gửi câu hỏi tư vấn, AI phân tích nhu cầu và trả lời kèm danh sách ID máy phù hợp.
- `GET  /api/ai/history` : Tải lại lịch sử hội thoại tư vấn của phiên người dùng.

### 4. Phân hệ Quản trị Viên & Nhân viên (`/api/admin`)
- `GET  /api/admin/analytics/overview` : Tổng quan doanh thu, số đơn hàng, tồn kho và khách hàng mới.
- `GET  /api/admin/analytics/revenue-chart` : Dữ liệu doanh số theo thời gian phục vụ vẽ biểu đồ BI.
- `CRUD /api/admin/products` : Quản lý thêm, sửa, xóa, ẩn sản phẩm và biến thể cấu hình.
- `CRUD /api/admin/categories` : Quản trị danh mục thiết bị.
- `CRUD /api/admin/brands` : Quản trị thương hiệu phần cứng.
- `CRUD /api/admin/vouchers` : Cấu hình chương trình khuyến mãi, mã giảm giá Flash Sale.
- `GET  /api/admin/orders` : Quản lý toàn bộ đơn hàng kèm bộ lọc trạng thái.
- `PUT  /api/admin/orders/{id}/status` : Cập nhật trạng thái xử lý và giao vận đơn hàng.
- `POST /api/admin/orders/{id}/assign-serial` : Gán mã Serial/IMEI vật lý từ kho trước khi xuất hàng.
- `POST /api/admin/serials/import` : Nhập kho lô hàng mới kèm dải mã Serial/IMEI.
- `CRUD /api/admin/users` : Quản lý danh sách người dùng, cấp quyền tài khoản nhân viên.

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & KHỞI CHẠY (QUICK START)

### 1. Yêu cầu Tiền đề (Prerequisites)
- **PHP:** >= 8.3 (yêu cầu các extension: `pdo`, `mbstring`, `openssl`, `curl`, `sqlite3` / `pdo_mysql`)
- **Composer:** >= 2.5
- **Node.js & NPM:** >= 18.x

### 2. Sao chép Mã nguồn & Cài đặt Thư viện
```bash
# Di chuyển vào thư mục dự án Backend
cd NHOM1_BE_QLBTBDT

# Cài đặt các gói phụ thuộc PHP qua Composer
composer install

# Cài đặt các gói phụ thuộc Node qua NPM
npm install
```

### 3. Thiết lập Môi trường Cấu hình (`.env`)
```bash
# Sao chép file cấu hình mẫu
copy .env.example .env

# Tạo mã khóa bí mật cho ứng dụng
php artisan key:generate
```

Cấu hình CSDL trong file `.env` (Mặc định hỗ trợ SQLite chạy ngay không cần cài MySQL):
```ini
DB_CONNECTION=sqlite
# Hoặc nếu sử dụng MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nhom1_qlbtbdt
# DB_USERNAME=root
# DB_PASSWORD=
```

Cấu hình cổng thanh toán VNPAY & AI Gemini API:
```ini
# Cấu hình VNPAY Sandbox
VNPAY_TMN_CODE=YOUR_TMN_CODE
VNPAY_HASH_SECRET=YOUR_HASH_SECRET
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

# Cấu hình Gemini AI
GEMINI_API_KEY=YOUR_GEMINI_API_KEY
```

### 4. Khởi tạo Cơ sở Dữ liệu & Nạp Dữ liệu Mẫu (Seeders)
```bash
# Chạy Migration 17 bảng CSDL và nạp toàn bộ dữ liệu mẫu thực tế
php artisan migrate:fresh --seed
```

### 5. Khởi chạy Server Phát triển
```bash
# Chạy Backend Server
php artisan serve
```
> 🌐 **Backend API Endpoint:** `http://127.0.0.1:8000`  
> 📑 **Tài liệu kiểm thử API:** Truy cập qua Postman Collection tại thư mục `/docs`

---

## 📂 TÀI LIỆU & KẾ HOẠCH DỰ ÁN
- 📋 **Kế hoạch chi tiết & Checklist tiến độ:** Xem tại **[plan.md](file:///E:/doantichhop/CodeNhom1/NHOM1_BE_QLBTBDT/plan.md)**
- 📘 **Đặc tả User Story:** Tham khảo tài liệu `0126_CS445_SE_02-UserStory.docx`
- 📊 **Product Backlog:** Tham khảo tài liệu `0126_CS445_SE_03-ProductBacklog.docx`
- 🗓️ **Project Plan WBS:** Tham khảo tài liệu `0126_CS445_SE_04-ProjectPlan.docx`

---

<p align="center">
  <b>© 2026 NHÓM 1 - ĐỒ ÁN PHÁT TRIỂN PHẦN MỀM CHUYÊN NGÀNH - ĐẠI HỌC DUY TÂN</b><br>
  <i>Xây dựng với niềm đam mê công nghệ & cam kết chất lượng phần mềm cao nhất.</i>
</p>
