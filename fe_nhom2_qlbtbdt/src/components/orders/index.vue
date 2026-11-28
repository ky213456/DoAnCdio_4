<template>
  <div class="orders-page-wrapper">
    <div v-if="liveToast" class="position-fixed top-0 end-0 m-3 px-3 py-2 rounded-3 shadow-lg bg-dark text-white d-flex align-items-center gap-2" style="z-index: 2000; font-size: 13px;">
      <i class="bi bi-broadcast text-info"></i>
      <span>{{ liveToast }}</span>
    </div>
    <!-- ========================================== -->
    <!-- ❶ HEADER TOPBAR & BREADCRUMB -->
    <!-- ========================================== -->
    <header class="orders-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">ĐƠN HÀNG</span>
          </router-link>
          <div class="breadcrumb-trail ms-4 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">Lịch sử đơn hàng của tôi</span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/warranty" class="btn-top-cart">
            <i class="bi bi-shield-check me-1 text-info"></i> Bảo hành
          </router-link>
          <router-link to="/profile" class="btn-top-cart" v-if="currentUser">
            <i class="bi bi-person me-1 text-primary"></i> Hồ sơ
          </router-link>
          <router-link to="/cart" class="btn-top-cart">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <div class="user-info-pill" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || currentUser.name || 'Khách hàng' }}</span>
          </div>
          <router-link to="/login?redirect=/my-orders" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- ❷ NỘI DUNG CHÍNH: TÌM KIẾM, TABS & DANH SÁCH -->
    <!-- ========================================== -->
    <main class="main-width orders-main-content">
      <!-- TIÊU ĐỀ & THANH TÌM KIẾM -->
      <div class="orders-headline-wrap mb-4">
        <div>
          <h1 class="page-title">
            <i class="bi bi-bag-check-fill text-primary me-2"></i> LỊCH SỬ ĐƠN HÀNG CỦA TÔI
          </h1>
          <p class="page-subtitle text-muted">
            Theo dõi tiến độ giao hàng, tra cứu mã Serial/IMEI kích hoạt bảo hành điện tử và quản lý đơn hàng đã mua.
          </p>
        </div>

        <!-- Ô tra cứu đơn theo SĐT hoặc Mã đơn -->
        <div class="search-orders-box">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input
              type="text"
              v-model="searchQuery"
              @input="filterOrders"
              placeholder="Tìm theo Mã đơn (#DH...) hoặc Tên máy..."
              class="form-control border-start-0 ps-0 shadow-none"
            />
            <button class="btn btn-outline-secondary" type="button" @click="loadOrders">
              <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
            </button>
          </div>
        </div>
      </div>

      <!-- BANNER TRA CỨU NHANH CHO KHÁCH VÃNG LAI (NẾU CHƯA ĐĂNG NHẬP HOẶC MUỐN TÌM THEO SĐT) -->
      <div class="guest-lookup-card mb-4" v-if="!currentUser || showGuestSearch">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center">
            <div class="lookup-icon text-warning me-3">
              <i class="bi bi-shield-lock-fill fs-2"></i>
            </div>
            <div>
              <strong class="d-block text-dark">Tra cứu đơn hàng bằng Số điện thoại nhận hàng:</strong>
              <span class="small text-muted">Nếu quý khách đặt hàng nhanh không đăng nhập, nhập số điện thoại để xem toàn bộ đơn đã đặt.</span>
            </div>
          </div>
          <div class="lookup-input-group d-flex gap-2">
            <input
              type="tel"
              v-model="lookupPhone"
              placeholder="Nhập số điện thoại (10 số)..."
              class="form-control"
              style="max-width: 260px;"
              @keyup.enter="lookupOrdersByPhone"
            />
            <button class="btn btn-primary px-3 text-nowrap" @click="lookupOrdersByPhone" :disabled="isLoading">
              <i class="bi bi-search me-1"></i> Tra cứu
            </button>
          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- TABS LỌC TRẠNG THÁI ĐƠN HÀNG (SHOPEE STYLE) -->
      <!-- ========================================== -->
      <div class="orders-status-tabs-wrapper mb-4">
        <div class="orders-tabs">
          <button
            class="tab-btn"
            :class="{ active: currentTab === 'all' }"
            @click="switchTab('all')"
          >
            Tất cả ({{ allOrders.length }})
          </button>
          <button
            class="tab-btn"
            :class="{ active: currentTab === 'cho_xac_nhan' }"
            @click="switchTab('cho_xac_nhan')"
          >
            Chờ xác nhận ({{ getCountByStatus('cho_xac_nhan') }})
          </button>
          <button
            class="tab-btn"
            :class="{ active: currentTab === 'dang_xu_ly' }"
            @click="switchTab('dang_xu_ly')"
          >
            Đang xử lý ({{ getCountByStatus('dang_xu_ly') }})
          </button>
          <button
            class="tab-btn"
            :class="{ active: currentTab === 'dang_giao' }"
            @click="switchTab('dang_giao')"
          >
            Đang giao hàng ({{ getCountByStatus('dang_giao') }})
          </button>
          <button
            class="tab-btn"
            :class="{ active: currentTab === 'da_giao' }"
            @click="switchTab('da_giao')"
          >
            Đã giao ({{ getCountByStatus('da_giao') }})
          </button>
          <button
            class="tab-btn"
            :class="{ active: currentTab === 'da_huy' }"
            @click="switchTab('da_huy')"
          >
            Đã hủy ({{ getCountByStatus('da_huy') }})
          </button>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- TRẠNG THÁI LOADING & TRỐNG -->
      <!-- ========================================== -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Đang tải đơn hàng...</span>
        </div>
        <p class="text-muted mt-2">Đang tải lịch sử đơn hàng từ máy chủ TechMart...</p>
      </div>

      <div v-else-if="filteredOrders.length === 0" class="empty-orders-card">
        <div class="empty-icon text-muted">
          <i class="bi bi-clipboard2-x"></i>
        </div>
        <h4 class="mt-3 fw-bold">Không tìm thấy đơn hàng nào!</h4>
        <p class="text-muted">
          {{ currentTab === 'all' ? 'Bạn chưa có đơn hàng nào trong tài khoản hoặc chưa có đơn trùng khớp với tìm kiếm.' : 'Không có đơn hàng nào ở trạng thái này.' }}
        </p>
        <router-link to="/" class="btn btn-primary px-4 py-2 mt-2">
          <i class="bi bi-cart-plus me-1"></i> Khám phá sản phẩm công nghệ ngay
        </router-link>
      </div>

      <!-- ========================================== -->
      <!-- DANH SÁCH CÁC THẺ ĐƠN HÀNG (ORDER CARDS) -->
      <!-- ========================================== -->
      <div v-else class="orders-list">
        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="order-card mb-4"
        >
          <!-- HEADER THẺ ĐƠN HÀNG -->
          <div class="order-card-header">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <span class="order-code-badge">
                <i class="bi bi-receipt me-1"></i>
                #{{ order.ma_don_hang }}
              </span>
              <button
                class="btn-copy-code"
                @click="copyToClipboard(order.ma_don_hang)"
                title="Sao chép mã đơn hàng"
              >
                <i class="bi bi-copy"></i>
              </button>
              <span class="order-date text-muted small ms-2">
                <i class="bi bi-clock me-1"></i> {{ order.ngay_dat }}
              </span>
            </div>

            <!-- Cụm Badge Trạng thái & Thanh toán -->
            <div class="d-flex align-items-center gap-2">
              <!-- Badge phương thức thanh toán -->
              <span class="payment-method-pill" :class="order.phuong_thuc_thanh_toan">
                <i :class="getPaymentIcon(order.phuong_thuc_thanh_toan)"></i>
                {{ getPaymentLabel(order.phuong_thuc_thanh_toan) }}
              </span>

              <!-- Badge trạng thái thanh toán -->
              <span class="payment-status-pill" :class="order.trang_thai_thanh_toan">
                <i :class="order.trang_thai_thanh_toan === 'da_thanh_toan' ? 'bi bi-check-circle-fill' : 'bi bi-hourglass-split'"></i>
                {{ order.trang_thai_thanh_toan === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
              </span>

              <!-- Badge trạng thái đơn hàng -->
              <span class="order-status-badge" :class="order.trang_thai_don_hang">
                <i :class="getStatusIcon(order.trang_thai_don_hang)"></i>
                {{ getStatusLabel(order.trang_thai_don_hang) }}
              </span>
            </div>
          </div>

          <!-- THÂN THẺ ĐƠN: DANH SÁCH MẶT HÀNG -->
          <div class="order-card-body">
            <div
              v-for="(item, idx) in order.items"
              :key="idx"
              class="order-item-line"
            >
              <div class="item-thumb-box">
                <img
                  :src="item.hinh_anh || '/laptop-placeholder.png'"
                  :alt="item.ten_san_pham"
                  class="item-thumb"
                  @error="$event.target.src = 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=200&auto=format&fit=crop&q=80'"
                />
              </div>

              <div class="item-info">
                <h5 class="item-title">{{ item.ten_san_pham }}</h5>
                <span class="item-variant text-muted small">
                  Phân loại: <strong>{{ item.ten_bien_the || 'Cấu hình tiêu chuẩn' }}</strong>
                </span>
                <span class="item-qty text-muted small ms-3">
                  Số lượng: ×{{ item.so_luong }}
                </span>
              </div>

              <div class="item-price text-end">
                <span class="unit-price text-muted small d-block">
                  {{ formatCurrency(item.don_gia) }}
                </span>
                <strong class="total-price text-primary">
                  {{ formatCurrency(item.thanh_tien || (item.don_gia * item.so_luong)) }}
                </strong>
              </div>
            </div>

            <!-- PHÂN ĐOẠN SERIAL/IMEI VẬT LÝ NẾU ĐƠN HÀNG ĐÃ XUẤT KHO (ĐIỂM NHẤN CÔNG NGHỆ) -->
            <div class="order-serials-highlight mt-3" v-if="order.serials && order.serials.length > 0">
              <div class="serial-header d-flex align-items-center text-success">
                <i class="bi bi-shield-check fs-5 me-2"></i>
                <strong class="small">MÃ SERIAL NUMBER / IMEI VẬT LÝ ĐÃ ĐƯỢC GÁN VÀO ĐƠN:</strong>
              </div>
              <div class="serial-tags-group mt-2 d-flex flex-wrap gap-2">
                <div
                  v-for="(s, sIdx) in order.serials"
                  :key="sIdx"
                  class="serial-chip"
                >
                  <span class="serial-code">
                    <i class="bi bi-upc-scan me-1 text-primary"></i>
                    Serial: <strong>{{ s.so_serial }}</strong>
                    <span v-if="s.imei" class="ms-2 text-secondary">| IMEI: <strong>{{ s.imei }}</strong></span>
                  </span>
                  <span class="serial-expiry text-muted small ms-2" v-if="s.han_bao_hanh">
                    (BH đến: {{ s.han_bao_hanh }})
                  </span>
                  <router-link
                    :to="'/warranty?serial=' + encodeURIComponent(s.so_serial)"
                    class="btn btn-sm btn-outline-primary py-0 px-2 ms-2"
                    style="font-size: 11.5px;"
                    title="Kiểm tra phiếu bảo hành điện tử"
                  >
                    <i class="bi bi-shield-check me-1"></i>Tra cứu BH
                  </router-link>
                  <router-link
                    :to="'/warranty?serial=' + encodeURIComponent(s.so_serial) + '&action=request'"
                    class="btn btn-sm btn-outline-warning text-dark py-0 px-2 ms-1"
                    style="font-size: 11.5px;"
                    title="Gửi yêu cầu bảo hành hoặc đổi trả thiết bị này"
                  >
                    <i class="bi bi-tools me-1"></i>Yêu cầu BH / Đổi trả
                  </router-link>
                </div>
              </div>
            </div>

            <!-- ĐỊA CHỈ GIAO HÀNG & GHI CHÚ -->
            <div class="order-shipping-summary mt-3 pt-3 border-top">
              <div class="row g-2 small text-secondary">
                <div class="col-md-7">
                  <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                  <strong>Giao đến:</strong> {{ order.ho_ten_nguoi_nhan }} ({{ order.so_dien_thoai_nhan }}) - {{ order.dia_chi_giao_hang }}
                </div>
                <div class="col-md-5 text-md-end" v-if="order.ghi_chu">
                  <i class="bi bi-chat-left-text me-1 text-info"></i>
                  <strong>Ghi chú:</strong> {{ order.ghi_chu }}
                </div>
              </div>
            </div>
          </div>

          <!-- FOOTER THẺ ĐƠN: TỔNG TIỀN & CÁC NÚT HÀNH ĐỘNG -->
          <div class="order-card-footer">
            <div class="footer-total">
              <span class="total-label text-muted">Thành tiền:</span>
              <strong class="total-value text-danger fs-5 ms-2">{{ formatCurrency(order.tong_thanh_toan) }}</strong>
              <span class="shipping-tag text-success ms-2 small">
                <i class="bi bi-truck me-1"></i> (Freeship 2H)
              </span>
            </div>

            <div class="footer-actions d-flex flex-wrap gap-2">
              <!-- Nút Xem chi tiết / In hóa đơn -->
              <button class="btn btn-sm btn-outline-secondary" @click="openOrderDetail(order)">
                <i class="bi bi-receipt-cutoff me-1"></i> Xem chi tiết
              </button>

              <!-- Nút Hủy đơn hàng (nếu đang ở chờ xác nhận) -->
              <button
                v-if="order.can_cancel"
                class="btn btn-sm btn-outline-danger"
                @click="openCancelModal(order)"
              >
                <i class="bi bi-x-circle me-1"></i> Hủy đơn
              </button>

              <!-- Nút Mua lại -->
              <button class="btn btn-sm btn-primary" @click="buyAgain(order)">
                <i class="bi bi-arrow-repeat me-1"></i> Mua lại
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ========================================== -->
    <!-- ❸ MODAL XEM CHI TIẾT ĐƠN HÀNG (RECEIPT) -->
    <!-- ========================================== -->
    <div class="modal fade show d-block" v-if="selectedOrderForDetail" style="background: rgba(0,0,0,0.65);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-dark text-white py-3">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-receipt-cutoff text-primary me-2"></i>
              CHI TIẾT ĐƠN HÀNG #{{ selectedOrderForDetail.ma_don_hang }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="selectedOrderForDetail = null"></button>
          </div>

          <div class="modal-body p-4">
            <!-- Thông tin khách & Địa chỉ -->
            <div class="row g-3 mb-4 p-3 bg-light rounded-3">
              <div class="col-md-6">
                <span class="text-muted small d-block">NGƯỜI NHẬN HÀNG:</span>
                <strong class="fs-6">{{ selectedOrderForDetail.ho_ten_nguoi_nhan }}</strong>
                <span class="d-block text-muted small mt-1">SĐT: {{ selectedOrderForDetail.so_dien_thoai_nhan }}</span>
              </div>
              <div class="col-md-6">
                <span class="text-muted small d-block">ĐỊA CHỈ GIAO HÀNG:</span>
                <span class="fw-semibold">{{ selectedOrderForDetail.dia_chi_giao_hang }}</span>
              </div>
              <div class="col-md-6">
                <span class="text-muted small d-block">THỜI GIAN ĐẶT:</span>
                <span>{{ selectedOrderForDetail.ngay_dat }}</span>
              </div>
              <div class="col-md-6">
                <span class="text-muted small d-block">PHƯƠNG THỨC THANH TOÁN:</span>
                <strong>{{ getPaymentLabel(selectedOrderForDetail.phuong_thuc_thanh_toan) }}</strong>
                <span class="badge ms-2" :class="selectedOrderForDetail.trang_thai_thanh_toan === 'da_thanh_toan' ? 'bg-success' : 'bg-warning text-dark'">
                  {{ selectedOrderForDetail.trang_thai_thanh_toan === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                </span>
              </div>
            </div>

            <!-- Bảng danh sách sản phẩm trong đơn -->
            <h6 class="fw-bold mb-2">Danh sách sản phẩm</h6>
            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle">
                <thead class="table-light small">
                  <tr>
                    <th>Sản phẩm</th>
                    <th class="text-center" style="width: 100px;">SL</th>
                    <th class="text-end" style="width: 150px;">Đơn giá</th>
                    <th class="text-end" style="width: 160px;">Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(it, i) in selectedOrderForDetail.items" :key="i">
                    <td>
                      <div class="fw-bold">{{ it.ten_san_pham }}</div>
                      <span class="text-muted small">Cấu hình: {{ it.ten_bien_the || 'Tiêu chuẩn' }}</span>
                    </td>
                    <td class="text-center">{{ it.so_luong }}</td>
                    <td class="text-end">{{ formatCurrency(it.don_gia) }}</td>
                    <td class="text-end fw-bold text-primary">{{ formatCurrency(it.thanh_tien) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Chi phí thanh toán -->
            <div class="row justify-content-end">
              <div class="col-md-6">
                <div class="d-flex justify-content-between py-1 small">
                  <span class="text-muted">Tạm tính hàng hóa:</span>
                  <span>{{ formatCurrency(selectedOrderForDetail.tong_tien_hang) }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small" v-if="selectedOrderForDetail.so_tien_giam > 0">
                  <span class="text-success">Giảm giá voucher:</span>
                  <span class="text-success fw-bold">-{{ formatCurrency(selectedOrderForDetail.so_tien_giam) }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                  <span class="text-muted">Phí giao hàng:</span>
                  <span class="text-success">0đ (Miễn phí)</span>
                </div>
                <hr class="my-2" />
                <div class="d-flex justify-content-between py-1">
                  <strong class="fs-5">TỔNG THANH TOÁN:</strong>
                  <strong class="fs-4 text-danger">{{ formatCurrency(selectedOrderForDetail.tong_thanh_toan) }}</strong>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light p-3 border-0">
            <button type="button" class="btn btn-secondary px-4" @click="selectedOrderForDetail = null">
              Đóng
            </button>
            <button type="button" class="btn btn-primary px-4" @click="printReceipt">
              <i class="bi bi-printer me-1"></i> In hóa đơn
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- ❹ MODAL XÁC NHẬN HỦY ĐƠN HÀNG -->
    <!-- ========================================== -->
    <div class="modal fade show d-block" v-if="orderToCancel" style="background: rgba(0,0,0,0.65);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-danger text-white py-3">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              XÁC NHẬN HỦY ĐƠN HÀNG #{{ orderToCancel.ma_don_hang }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="closeCancelModal"></button>
          </div>

          <div class="modal-body p-4">
            <p class="text-muted">
              Bạn có chắc chắn muốn hủy đơn hàng này không? Sau khi hủy, đơn hàng sẽ không thể phục hồi lại.
            </p>

            <label class="form-label fw-bold small text-secondary">Vui lòng chọn lý do hủy đơn (*):</label>
            <div class="cancel-reasons-list d-flex flex-column gap-2 mb-3">
              <label class="reason-option p-2 border rounded">
                <input type="radio" v-model="cancelReason" value="Muốn thay đổi địa chỉ nhận hàng" class="me-2" />
                <span>Muốn thay đổi địa chỉ nhận hàng</span>
              </label>
              <label class="reason-option p-2 border rounded">
                <input type="radio" v-model="cancelReason" value="Muốn đổi phương thức thanh toán khác" class="me-2" />
                <span>Muốn đổi phương thức thanh toán khác</span>
              </label>
              <label class="reason-option p-2 border rounded">
                <input type="radio" v-model="cancelReason" value="Muốn thay đổi cấu hình RAM/SSD của thiết bị" class="me-2" />
                <span>Muốn thay đổi cấu hình RAM/SSD của thiết bị</span>
              </label>
              <label class="reason-option p-2 border rounded">
                <input type="radio" v-model="cancelReason" value="Tìm thấy sản phẩm khác phù hợp hơn" class="me-2" />
                <span>Tìm thấy sản phẩm khác phù hợp hơn</span>
              </label>
              <label class="reason-option p-2 border rounded">
                <input type="radio" v-model="cancelReason" value="Lý do khác" class="me-2" />
                <span>Lý do khác</span>
              </label>
            </div>

            <div v-if="cancelReason === 'Lý do khác'">
              <textarea
                v-model="customCancelReason"
                rows="2"
                placeholder="Nhập lý do cụ thể..."
                class="form-control"
              ></textarea>
            </div>

            <div v-if="cancelError" class="alert alert-danger py-2 small mt-2">
              <i class="bi bi-exclamation-circle-fill me-1"></i> {{ cancelError }}
            </div>
          </div>

          <div class="modal-footer bg-light p-3 border-0">
            <button type="button" class="btn btn-secondary px-3" @click="closeCancelModal" :disabled="isCancelling">
              Hủy bỏ
            </button>
            <button type="button" class="btn btn-danger px-4" @click="submitCancelOrder" :disabled="isCancelling">
              <span v-if="isCancelling" class="spinner-border spinner-border-sm me-2"></span>
              Xác nhận hủy đơn
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { toast } from '@/utils/notify'

export default {
  name: 'TechMartMyOrders',
  data() {
    return {
      currentUser: null,
      isLoading: false,
      allOrders: [],
      filteredOrders: [],
      currentTab: 'all',
      searchQuery: '',

      // Tìm kiếm khách vãng lai bằng số điện thoại
      lookupPhone: '',
      showGuestSearch: false,

      // Modal chi tiết
      selectedOrderForDetail: null,

      // Modal hủy đơn
      orderToCancel: null,
      cancelReason: 'Muốn thay đổi địa chỉ nhận hàng',
      customCancelReason: '',
      isCancelling: false,
      cancelError: '',

      // Realtime sync
      liveToast: '',
      _liveToastTimer: null,
      _pollTimer: null,
      _bc: null,
      _storageHandler: null,
      _visHandler: null
    }
  },
  mounted() {
    this.loadUserData()
    this.loadOrders()
    this.initRealtimeSync()
  },
  beforeUnmount() {
    if (this._pollTimer) clearInterval(this._pollTimer)
    if (this._liveToastTimer) clearTimeout(this._liveToastTimer)
    if (this._bc) {
      try { this._bc.close() } catch (e) {}
    }
    if (this._storageHandler) window.removeEventListener('storage', this._storageHandler)
    if (this._visHandler) document.removeEventListener('visibilitychange', this._visHandler)
  },
  methods: {
    initRealtimeSync() {
      try {
        if (typeof BroadcastChannel !== 'undefined') {
          this._bc = new BroadcastChannel('techmart_realtime')
          this._bc.onmessage = (ev) => {
            if (ev.data?.type === 'ORDER_STATUS_UPDATED' || ev.data?.type === 'ORDER_CREATED') {
              this.loadOrders(true)
            }
          }
        }
      } catch (e) {}

      this._storageHandler = (ev) => {
        if (ev.key === 'techmart_realtime_ping') {
          this.loadOrders(true)
        }
      }
      window.addEventListener('storage', this._storageHandler)

      this._visHandler = () => {
        if (!document.hidden) this.loadOrders(true)
      }
      document.addEventListener('visibilitychange', this._visHandler)

      this._pollTimer = setInterval(() => {
        if (!document.hidden) this.loadOrders(true)
      }, 3000)
    },

    showLiveToast(msg) {
      this.liveToast = msg
      if (this._liveToastTimer) clearTimeout(this._liveToastTimer)
      this._liveToastTimer = setTimeout(() => { this.liveToast = '' }, 4000)
    },

    broadcastOrderEvent(type, payload = {}) {
      try {
        if (typeof BroadcastChannel !== 'undefined') {
          const bc = new BroadcastChannel('techmart_realtime')
          bc.postMessage({ type, ...payload, ts: Date.now() })
          bc.close()
        }
        localStorage.setItem('techmart_realtime_ping', JSON.stringify({ type, ...payload, ts: Date.now() }))
      } catch (e) {}
    },

    loadUserData() {
      const stored = localStorage.getItem('customer_user') || localStorage.getItem('admin_user')
      if (stored) {
        try {
          this.currentUser = JSON.parse(stored)
          this.lookupPhone = this.currentUser.so_dien_thoai || ''
        } catch (e) {
          console.error(e)
        }
      }
    },

    async loadOrders(silent = false) {
      if (!silent) this.isLoading = true
      const userId = localStorage.getItem('user_id') || (this.currentUser ? this.currentUser.id : null)
      const phone = this.lookupPhone || (this.currentUser ? this.currentUser.so_dien_thoai : '')
      const prevMap = new Map(this.allOrders.map(o => [o.id, o.trang_thai_don_hang]))

      try {
        const res = await axios.get('http://127.0.0.1:8000/api/client/don-hang/danh-sach', {
          params: {
            user_id: userId,
            phone: phone
          }
        })

        if (res.data && res.data.status && Array.isArray(res.data.data)) {
          const incoming = res.data.data
          if (silent && prevMap.size > 0) {
            for (const o of incoming) {
              const oldStatus = prevMap.get(o.id)
              if (oldStatus && oldStatus !== o.trang_thai_don_hang) {
                this.showLiveToast(`Đơn #${o.ma_don_hang} vừa cập nhật: ${this.getStatusLabel(o.trang_thai_don_hang)}`)
                break
              }
            }
          }
          this.allOrders = incoming
          if (this.selectedOrderForDetail) {
            const refreshed = incoming.find(o => o.id === this.selectedOrderForDetail.id)
            if (refreshed) this.selectedOrderForDetail = refreshed
          }
        } else if (!silent) {
          this.allOrders = []
        }
      } catch (err) {
        console.warn('Lỗi khi nạp đơn hàng:', err)
        if (!silent) this.allOrders = []
      } finally {
        if (!silent) this.isLoading = false
        this.filterOrders()
      }
    },

    lookupOrdersByPhone() {
      if (!this.lookupPhone.trim()) {
        toast.warning('Vui lòng nhập số điện thoại để tra cứu đơn hàng!')
        return
      }
      this.loadOrders()
    },

    switchTab(tab) {
      this.currentTab = tab
      this.filterOrders()
    },

    filterOrders() {
      let list = [...this.allOrders]

      // Lọc theo tab trạng thái
      if (this.currentTab !== 'all') {
        if (this.currentTab === 'dang_xu_ly') {
          list = list.filter(o => ['dang_xu_ly', 'da_xac_nhan', 'dang_dong_goi'].includes(o.trang_thai_don_hang))
        } else if (this.currentTab === 'da_giao') {
          list = list.filter(o => ['da_giao', 'giao_thanh_cong'].includes(o.trang_thai_don_hang))
        } else {
          list = list.filter(o => o.trang_thai_don_hang === this.currentTab)
        }
      }

      // Lọc theo từ khóa tìm kiếm (Mã đơn hoặc Tên sản phẩm)
      const q = this.searchQuery.trim().toLowerCase()
      if (q) {
        list = list.filter(o => {
          const matchCode = (o.ma_don_hang || '').toLowerCase().includes(q)
          const matchPhone = (o.so_dien_thoai_nhan || '').includes(q)
          const matchItem = o.items && o.items.some(it => (it.ten_san_pham || '').toLowerCase().includes(q))
          return matchCode || matchPhone || matchItem
        })
      }

      this.filteredOrders = list
    },

    getCountByStatus(status) {
      if (status === 'dang_xu_ly') {
        return this.allOrders.filter(o => ['dang_xu_ly', 'da_xac_nhan', 'dang_dong_goi'].includes(o.trang_thai_don_hang)).length
      }
      if (status === 'da_giao') {
        return this.allOrders.filter(o => ['da_giao', 'giao_thanh_cong'].includes(o.trang_thai_don_hang)).length
      }
      return this.allOrders.filter(o => o.trang_thai_don_hang === status).length
    },

    getStatusLabel(status) {
      const map = {
        'cho_xac_nhan': 'Chờ xác nhận',
        'pending': 'Chờ xử lý',
        'da_xac_nhan': 'Đã xác nhận',
        'dang_dong_goi': 'Đang đóng gói',
        'dang_xu_ly': 'Đang xử lý',
        'dang_giao': 'Đang giao hàng',
        'da_giao': 'Giao thành công',
        'giao_thanh_cong': 'Giao thành công',
        'da_huy': 'Đã hủy'
      }
      return map[status] || 'Đang xử lý'
    },

    getStatusIcon(status) {
      const map = {
        'cho_xac_nhan': 'bi bi-clock-history',
        'pending': 'bi bi-hourglass-split',
        'da_xac_nhan': 'bi bi-check2-circle',
        'dang_dong_goi': 'bi bi-box-seam',
        'dang_xu_ly': 'bi bi-box-seam',
        'dang_giao': 'bi bi-truck',
        'da_giao': 'bi bi-check-circle-fill',
        'giao_thanh_cong': 'bi bi-check-circle-fill',
        'da_huy': 'bi bi-x-circle-fill'
      }
      return map[status] || 'bi bi-info-circle'
    },

    getPaymentLabel(method) {
      const map = {
        'vnpay': 'VNPAY Sandbox',
        'payos': 'PayOS VietQR',
        'cod': 'COD (Tiền mặt)'
      }
      return map[method] || method?.toUpperCase() || 'COD'
    },

    getPaymentIcon(method) {
      const map = {
        'vnpay': 'bi bi-wallet2 text-danger',
        'payos': 'bi bi-qr-code text-primary',
        'cod': 'bi bi-cash-stack text-success'
      }
      return map[method] || 'bi bi-credit-card'
    },

    openOrderDetail(order) {
      this.selectedOrderForDetail = order
    },

    openCancelModal(order) {
      this.orderToCancel = order
      this.cancelReason = 'Muốn thay đổi địa chỉ nhận hàng'
      this.customCancelReason = ''
      this.cancelError = ''
    },

    closeCancelModal() {
      if (this.isCancelling) return
      this.orderToCancel = null
    },

    async submitCancelOrder() {
      if (!this.orderToCancel) return

      const reason = this.cancelReason === 'Lý do khác'
        ? (this.customCancelReason.trim() || 'Lý do khác')
        : this.cancelReason

      this.isCancelling = true
      this.cancelError = ''

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/client/don-hang/huy-don', {
          id: this.orderToCancel.id,
          user_id: localStorage.getItem('user_id'),
          ly_do: reason
        })

        if (res.data && res.data.status) {
          const cancelledCode = this.orderToCancel.ma_don_hang
          const cancelledId = this.orderToCancel.id
          this.broadcastOrderEvent('ORDER_CANCELLED', { orderId: cancelledId, maDonHang: cancelledCode })
          toast.success(`Hủy đơn hàng #${cancelledCode} thành công!`)
          this.closeCancelModal()
          await this.loadOrders()
        } else {
          this.cancelError = res.data?.message || 'Không thể hủy đơn hàng.'
        }
      } catch (err) {
        this.cancelError = err.response?.data?.message || 'Lỗi kết nối khi hủy đơn.'
      } finally {
        this.isCancelling = false
      }
    },

    repayVnpay(order) {
      // Có thể chuyển hướng sang trang thanh toán lại hoặc tạo phiên mới
      toast.info(`Đang mở lại cổng thanh toán VNPAY cho đơn hàng #${order.ma_don_hang}...`)
      this.$router.push('/checkout')
    },

    buyAgain(order) {
      if (!order || !order.items || order.items.length === 0) return
      // Đặt các items vào checkout_items và chuyển sang checkout
      const checkoutItems = order.items.map(it => ({
        gio_hang_id: null,
        bien_the_id: it.bien_the_id,
        ten_san_pham: it.ten_san_pham,
        ten_bien_the: it.ten_bien_the || 'Tiêu chuẩn',
        quantity: it.so_luong || 1,
        price: it.don_gia,
        image: it.hinh_anh
      }))

      localStorage.setItem('techmart_checkout_items', JSON.stringify(checkoutItems))
      this.$router.push('/checkout')
    },

    copyToClipboard(text) {
      if (!text) return
      navigator.clipboard.writeText(text).then(() => {
        toast.success(`Đã sao chép mã đơn hàng: #${text}`)
      })
    },

    printReceipt() {
      window.print()
    },

    formatCurrency(val) {
      if (!val || isNaN(val)) return '0đ'
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
    }
  }
}
</script>

<style scoped>
/* ==========================================
   STYLE LỊCH SỬ ĐƠN HÀNG (TECHMART AI)
   ========================================== */
.orders-page-wrapper {
  background-color: #f4f6fa;
  min-height: 100vh;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 16px;
}

/* Header */
.orders-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  height: 70px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: nowrap;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.brand-logo {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  font-weight: 800;
  font-size: 1.25rem;
  color: #0f172a;
  white-space: nowrap;
}

.logo-icon {
  width: 38px;
  height: 38px;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  border-radius: 10px;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
  font-size: 1.1rem;
}

.badge-sub {
  background: #eff6ff;
  color: #2563eb;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
}

.breadcrumb-trail {
  align-items: center;
  gap: 8px;
  font-size: 0.88rem;
}

.breadcrumb-item-link {
  color: #64748b;
  text-decoration: none;
}

.breadcrumb-item-link:hover {
  color: #2563eb;
}

.separator {
  color: #cbd5e1;
  font-size: 0.75rem;
}

.breadcrumb-current {
  color: #0f172a;
  font-weight: 600;
}

.btn-top-cart {
  display: inline-flex;
  align-items: center;
  color: #0f172a;
  text-decoration: none;
  font-size: 0.88rem;
  font-weight: 600;
  padding: 7px 14px;
  border-radius: 8px;
  background: #f1f5f9;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.btn-top-cart:hover {
  background: #e2e8f0;
  color: #2563eb;
}

.user-info-pill {
  display: inline-flex;
  align-items: center;
  background: #f1f5f9;
  padding: 7px 14px;
  border-radius: 20px;
  font-size: 0.88rem;
  font-weight: 600;
  white-space: nowrap;
  border: 1px solid #e2e8f0;
}

.btn-login-link {
  display: inline-flex;
  align-items: center;
  background: #2563eb;
  color: #ffffff;
  padding: 7px 16px;
  border-radius: 8px;
  font-size: 0.88rem;
  font-weight: 600;
  text-decoration: none;
  white-space: nowrap;
}

/* Headline */
.orders-main-content {
  margin-top: 24px;
}

.orders-headline-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.page-title {
  font-size: 1.45rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
}

.page-subtitle {
  font-size: 0.88rem;
  margin: 4px 0 0;
}

.search-orders-box {
  width: 100%;
  max-width: 440px;
  min-width: 340px;
}

/* Guest lookup card */
.guest-lookup-card {
  background: #ffffff;
  border: 1px solid #fed7aa;
  border-left: 4px solid #f97316;
  border-radius: 12px;
  padding: 14px 20px;
}

/* Tabs */
.orders-status-tabs-wrapper {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  padding: 4px;
  overflow-x: auto;
}

.orders-tabs {
  display: flex;
  min-width: 600px;
  gap: 4px;
}

.tab-btn {
  flex: 1;
  background: none;
  border: none;
  padding: 12px 16px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #64748b;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  text-align: center;
}

.tab-btn:hover {
  background: #f8fafc;
  color: #0f172a;
}

.tab-btn.active {
  background: #2563eb;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
}

/* Order Card */
.order-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
  overflow: hidden;
  transition: box-shadow 0.2s;
}

.order-card:hover {
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
}

.order-card-header {
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.order-code-badge {
  font-family: monospace;
  font-weight: 700;
  font-size: 0.98rem;
  color: #0f172a;
}

.btn-copy-code {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 2px 6px;
  font-size: 0.85rem;
  transition: color 0.2s;
}

.btn-copy-code:hover {
  color: #2563eb;
}

/* Pills */
.payment-method-pill {
  font-size: 0.78rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
  background: #f1f5f9;
  color: #334155;
}

.payment-method-pill.vnpay {
  background: #fee2e2;
  color: #dc2626;
}

.payment-method-pill.payos {
  background: #e0f2fe;
  color: #0284c7;
}

.payment-method-pill.cod {
  background: #dcfce7;
  color: #16a34a;
}

.payment-status-pill {
  font-size: 0.78rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
  background: #fef3c7;
  color: #b45309;
}

.payment-status-pill.da_thanh_toan {
  background: #dcfce7;
  color: #15803d;
}

.order-status-badge {
  font-size: 0.78rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
}

.order-status-badge.cho_xac_nhan,
.order-status-badge.pending {
  background: #fef3c7;
  color: #b45309;
}

.order-status-badge.dang_xu_ly {
  background: #e0f2fe;
  color: #0284c7;
}

.order-status-badge.dang_giao {
  background: #f3e8ff;
  color: #7e22ce;
}

.order-status-badge.da_giao {
  background: #dcfce7;
  color: #15803d;
}

.order-status-badge.da_huy {
  background: #fee2e2;
  color: #b91c1c;
}

/* Card Body */
.order-card-body {
  padding: 20px;
}

.order-item-line {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 0;
  border-bottom: 1px solid #f8fafc;
}

.item-thumb-box {
  width: 64px;
  height: 64px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.item-thumb {
  max-width: 90%;
  max-height: 90%;
  object-fit: contain;
}

.item-info {
  flex: 1;
}

.item-title {
  font-size: 0.95rem;
  font-weight: 700;
  margin: 0 0 4px;
  color: #0f172a;
}

/* Serials Highlight */
.order-serials-highlight {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 10px;
  padding: 12px 16px;
}

.serial-chip {
  background: #ffffff;
  border: 1px solid #86efac;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.82rem;
}

.serial-code {
  font-family: monospace;
  font-weight: 700;
  color: #166534;
}

/* Card Footer */
.order-card-footer {
  padding: 16px 20px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
}

.total-label {
  font-size: 0.92rem;
}

.shipping-tag {
  background: #ecfdf5;
  color: #059669;
  padding: 2px 8px;
  border-radius: 6px;
  font-weight: 600;
}

/* Empty state */
.empty-orders-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  padding: 60px 20px;
  text-align: center;
  max-width: 500px;
  margin: 40px auto;
}

.empty-icon {
  font-size: 4rem;
}
</style>
