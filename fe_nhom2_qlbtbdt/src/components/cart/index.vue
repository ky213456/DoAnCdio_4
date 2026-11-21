<template>
  <div class="cart-page-wrapper">
    <!-- ========================================== -->
    <!-- ❶ HEADER GIỎ HÀNG: TOPBAR & BREADCRUMB -->
    <!-- ========================================== -->
    <header class="cart-header">
      <div class="main-width cart-header-inner">
        <!-- Logo & Breadcrumb -->
        <div class="header-left">
          <router-link to="/" class="cart-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
          </router-link>
          <div class="cart-breadcrumb">
            <router-link to="/" class="breadcrumb-link"><i class="bi bi-house-door me-1"></i>Trang chủ</router-link>
            <i class="bi bi-chevron-right breadcrumb-separator"></i>
            <span class="breadcrumb-current">Giỏ hàng của bạn</span>
          </div>
        </div>

        <!-- Hotline hỗ trợ & Thông tin khách hàng -->
        <div class="header-right">
          <div class="hotline-box d-none d-md-flex">
            <i class="bi bi-headset me-2 text-danger fs-5"></i>
            <div class="hotline-text">
              <span class="hotline-label">Hotline hỗ trợ đặt hàng:</span>
              <strong class="hotline-number">1900.6789</strong>
              <span class="hotline-time">(8h - 21h30)</span>
            </div>
          </div>
          <!-- Trạng thái tài khoản người dùng -->
          <div class="cart-auth-box ms-3" v-if="currentUser">
            <router-link to="/my-orders" class="me-2 text-decoration-none small text-dark fw-bold">
              <i class="bi bi-bag-check text-primary me-1"></i>Đơn hàng
            </router-link>
            <span class="cart-user-name"><i class="bi bi-person-circle text-primary me-1"></i>{{ currentUser.ho_ten }}</span>
            <button class="btn-cart-logout ms-2" @click="handleLogout" title="Đăng xuất"><i class="bi bi-box-arrow-right"></i></button>
          </div>
          <div class="cart-auth-box ms-3" v-else>
            <router-link to="/login?redirect=/cart" class="btn-cart-login">
              <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- ❷ THÂN TRANG GIỎ HÀNG & BẢNG THANH TOÁN -->
    <!-- ========================================== -->
    <main class="main-width cart-main-content">
      <!-- Tiêu đề trang (Callout 1) -->
      <div class="cart-page-title-wrap">
        <h1 class="cart-page-title">
          <i class="bi bi-cart-check-fill me-2 text-primary"></i>
          GIỎ HÀNG CỦA BẠN ({{ cartItems.length }} THIẾT BỊ)
        </h1>
        <p class="cart-page-subtitle">
          Vui lòng kiểm tra lại cấu hình biến thể (RAM/SSD), số lượng và áp dụng voucher ưu đãi trước khi đặt hàng.
        </p>
      </div>

      <!-- Khung chứa 2 cột: Bảng thiết bị (trái) và Tóm tắt thanh toán (phải) -->
      <div class="cart-layout-grid" v-if="cartItems.length > 0">
        <!-- ========================================== -->
        <!-- CỘT TRÁI: BẢNG SẢN PHẨM TRONG GIỎ -->
        <!-- ========================================== -->
        <div class="cart-left-col">
          <div class="cart-table-card">
            <!-- Header của bảng (Callout 2) -->
            <div class="cart-table-header">
              <div class="col-checkbox-prod">
                <label class="custom-checkbox-wrap">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                  />
                  <span class="checkbox-checkmark"></span>
                  <span class="label-all-text">TẤT CẢ ({{ cartItems.length }} SẢN PHẨM)</span>
                </label>
              </div>
              <div class="col-price-hdr text-center">ĐƠN GIÁ</div>
              <div class="col-qty-hdr text-center">SỐ LƯỢNG</div>
              <div class="col-total-hdr text-center">THÀNH TIỀN</div>
              <div class="col-action-hdr text-center">XÓA</div>
            </div>

            <!-- Danh sách từng dòng sản phẩm trong giỏ (Callouts 3 - 13) -->
            <div class="cart-items-list">
              <div
                v-for="(item, idx) in cartItems"
                :key="item.id || idx"
                class="cart-item-row"
                :class="{ 'item-gift-row': item.isGift }"
              >
                <!-- Checkbox & Ảnh & Thông tin chi tiết -->
                <div class="item-info-col">
                  <!-- Checkbox từng món -->
                  <label class="custom-checkbox-wrap item-checkbox">
                    <input
                      type="checkbox"
                      v-model="item.selected"
                      :disabled="item.isGift"
                    />
                    <span class="checkbox-checkmark"></span>
                  </label>

                  <!-- ẢNH SẢN PHẨM (Callout 3, 8) -->
                  <div class="item-img-box">
                    <img :src="item.image" :alt="item.name" />
                    <span v-if="item.isGift" class="gift-badge">QUÀ TẶNG</span>
                  </div>

                  <!-- THÔNG TIN CHI TIẾT SẢN PHẨM -->
                  <div class="item-details">
                    <h3 class="item-name" :title="item.name">
                      {{ item.name }}
                    </h3>

                    <!-- Mã Biến thể SKU -->
                    <div class="item-sku">
                      <span class="sku-label">Biến thể SKU:</span>
                      <strong class="sku-val">{{ item.sku }}</strong>
                    </div>

                    <!-- Cấu hình chi tiết -->
                    <div class="item-specs">
                      <span class="specs-label">Cấu hình:</span>
                      <span class="specs-val">{{ item.specs }}</span>
                    </div>

                    <!-- Ghi chú bảo hành / quà tặng -->
                    <div class="item-warranty" :class="{ 'warranty-gift': item.isGift }">
                      <i :class="item.isGift ? 'bi bi-gift-fill text-danger me-1' : 'bi bi-shield-check text-success me-1'"></i>
                      <span>{{ item.warrantyNote }}</span>
                    </div>
                  </div>
                </div>

                <!-- ĐƠN GIÁ (Callout 4) -->
                <div class="item-price-col text-center">
                  <div class="price-current">{{ formatCurrency(item.price) }}</div>
                  <div v-if="item.originalPrice" class="price-original">
                    {{ formatCurrency(item.originalPrice) }}
                  </div>
                </div>

                <!-- SỐ LƯỢNG (Callouts 5, 6) -->
                <div class="item-qty-col text-center">
                  <div class="qty-control-box" v-if="!item.isGift">
                    <button class="btn-qty" @click="decreaseQty(idx)" :disabled="item.quantity <= 1">
                      <i class="bi bi-dash"></i>
                    </button>
                    <input
                      type="number"
                      v-model.number="item.quantity"
                      min="1"
                      :max="item.stock"
                      class="input-qty"
                      @change="validateQty(idx)"
                    />
                    <button class="btn-qty" @click="increaseQty(idx)" :disabled="item.quantity >= item.stock">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                  <div v-else class="gift-qty-fixed">
                    <span class="badge bg-light text-dark border px-2 py-1">SL: 1</span>
                  </div>

                  <!-- Tồn kho -->
                  <div class="stock-note">
                    Kho: Còn <strong>{{ item.stock }}</strong> máy
                  </div>
                </div>

                <!-- THÀNH TIỀN (Callout 7, 12, 13) -->
                <div class="item-total-col text-center">
                  <div class="item-subtotal" :class="{ 'subtotal-gift': item.isGift }">
                    <template v-if="!item.isGift">
                      {{ formatCurrency(item.price * item.quantity) }}
                    </template>
                    <template v-else>
                      <span class="text-danger fw-bold">0đ (Quà tặng)</span>
                    </template>
                  </div>
                </div>

                <!-- NÚT XÓA (Callout 9, 10, 11) -->
                <div class="item-action-col text-center">
                  <button
                    class="btn-delete-item"
                    @click="deleteItem(idx)"
                    :title="item.isGift ? 'Quà tặng kèm theo sản phẩm chính' : 'Xóa sản phẩm'"
                    :disabled="item.isGift"
                  >
                    <i class="bi bi-trash3 me-1"></i> Xóa
                  </button>
                </div>
              </div>
            </div>

            <!-- Thanh điều hướng đáy bảng: Tiếp tục mua & Xóa tất cả (Callout 14) -->
            <div class="cart-table-footer">
              <router-link to="/" class="btn-continue-shopping">
                <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua hàng
              </router-link>
              <button class="btn-clear-cart" @click="clearAllCart">
                <i class="bi bi-trash3-fill me-1"></i> Xóa tất cả giỏ hàng
              </button>
            </div>
          </div>

          <!-- DÒNG CAM KẾT VẬT LÝ SERIAL/IMEI (Dưới chân bảng sản phẩm) -->
          <div class="serial-commitment-card">
            <div class="commitment-icon text-success">
              <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="commitment-text">
              <strong>CAM KẾT CHÍNH HÃNG:</strong> Toàn bộ thiết bị điện tử bán ra đều được dán mã
              <strong>Serial Number / IMEI vật lý trên thân máy</strong>. Khách hàng nhận máy được quyền kiểm tra đối chiếu
              trùng khớp mã serial trước khi ký nhận bàn giao và kích hoạt bảo hành điện tử.
            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- CỘT PHẢI: BẢNG TÓM TẮT ĐƠN HÀNG & THANH TOÁN -->
        <!-- ========================================== -->
        <div class="cart-right-col">
          <div class="order-summary-card">
            <!-- Tiêu đề tóm tắt -->
            <h3 class="summary-heading">
              <i class="bi bi-receipt-cutoff me-2 text-primary"></i>
              TÓM TẮT ĐƠN HÀNG & THANH TOÁN
            </h3>

            <!-- Ô NHẬP MÃ GIẢM GIÁ (COUPON / VOUCHER) -->
            <div class="voucher-input-section">
              <label class="voucher-label">
                <i class="bi bi-ticket-perforated-fill me-1 text-danger"></i> Mã giảm giá (Coupon / Voucher):
              </label>
              <div class="voucher-input-group">
                <input
                  type="text"
                  v-model="voucherCode"
                  placeholder="Nhập mã (ví dụ: TECH500)"
                  class="input-voucher"
                  :disabled="isVoucherApplied"
                />
                <button
                  class="btn-apply-voucher"
                  :class="{ 'btn-remove-voucher': isVoucherApplied }"
                  @click="handleVoucherAction"
                >
                  {{ isVoucherApplied ? 'Hủy mã' : 'Áp dụng' }}
                </button>
              </div>

              <!-- Nút gợi ý voucher có sẵn -->
              <button class="btn-suggest-vouchers" @click="showVoucherModal = true">
                <i class="bi bi-gift-fill me-1 text-warning"></i>
                Bấm để chọn Voucher khuyến mãi có sẵn &gt;&gt;
              </button>

              <!-- Thông báo voucher -->
              <div v-if="voucherMessage" :class="['voucher-alert', isVoucherApplied ? 'alert-success' : 'alert-danger']">
                <i :class="isVoucherApplied ? 'bi bi-check-circle-fill me-1' : 'bi bi-exclamation-triangle-fill me-1'"></i>
                {{ voucherMessage }}
              </div>
            </div>

            <!-- BẢNG TÍNH TIỀN CHI TIẾT -->
            <div class="summary-breakdown">
              <div class="breakdown-row">
                <span class="row-label">Tạm tính giỏ hàng ({{ selectedCount }} món):</span>
                <span class="row-value">{{ formatCurrency(subtotalAmount) }}</span>
              </div>

              <div class="breakdown-row" v-if="voucherDiscount > 0">
                <span class="row-label text-success">
                  <i class="bi bi-tag-fill me-1"></i> Mã giảm giá Voucher ({{ appliedVoucherName }}):
                </span>
                <span class="row-value text-success fw-bold">
                  -{{ formatCurrency(voucherDiscount) }}
                </span>
              </div>

              <div class="breakdown-row">
                <span class="row-label">Phí vận chuyển (Giao hỏa tốc 2H):</span>
                <span class="row-value text-success fw-bold">
                  0đ (Miễn phí)
                </span>
              </div>

              <div class="breakdown-row">
                <span class="row-label">Thuế giá trị gia tăng (VAT 10%):</span>
                <span class="row-value text-muted">Đã bao gồm</span>
              </div>

              <div class="breakdown-row">
                <span class="row-label">Bảo hiểm hàng hóa giá trị cao:</span>
                <span class="row-value text-primary">0đ (Tài trợ 100%)</span>
              </div>
            </div>

            <hr class="summary-divider" />

            <!-- TỔNG THANH TOÁN LỚN -->
            <div class="final-total-box">
              <div class="total-row">
                <span class="total-label">TỔNG THANH TOÁN:</span>
                <span class="total-amount">{{ formatCurrency(finalTotalAmount) }}</span>
              </div>
              <div class="saving-note" v-if="totalSavings > 0">
                (Đã tiết kiệm được: <strong>{{ formatCurrency(totalSavings) }}</strong> so với giá niêm yết)
              </div>
            </div>

            <!-- NÚT TIẾN HÀNH ĐẶT HÀNG & THANH TOÁN -->
            <button
              class="btn-proceed-checkout"
              :disabled="selectedCount === 0"
              @click="handleCheckout"
            >
              TIẾN HÀNH ĐẶT HÀNG & THANH TOÁN &gt;&gt;
            </button>
            <p v-if="selectedCount === 0" class="text-danger small text-center mt-2">
              Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!
            </p>

            <!-- HỖ TRỢ THANH TOÁN BẢO MẬT (Callout 15) -->
            <div class="payment-partners-section">
              <span class="partners-label">
                <i class="bi bi-shield-check me-1 text-success"></i> Hỗ trợ thanh toán bảo mật:
              </span>
              <div class="partners-badges-grid">
                <div class="pay-badge pay-vietqr">
                  <i class="bi bi-qr-code-scan me-1 text-primary"></i> VietQR / PayOS
                </div>
                <div class="pay-badge pay-vnpay">
                  <i class="bi bi-wallet2 me-1 text-danger"></i> Cổng VNPAY QR
                </div>
              
                <div class="pay-badge pay-cod">
                  <i class="bi bi-cash-stack me-1 text-success"></i> COD Tiền mặt
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TRƯỜNG HỢP GIỎ HÀNG TRỐNG -->
      <div v-else class="empty-cart-card text-center">
        <div class="empty-cart-icon">
          <i class="bi bi-cart-x"></i>
        </div>
        <h3 class="empty-cart-title">Giỏ hàng của bạn đang trống!</h3>
        <p class="empty-cart-desc">
          Hãy quay lại trang chủ để khám phá hàng ngàn thiết bị công nghệ chính hãng giá tốt.
        </p>
        <router-link to="/" class="btn-shop-now">
          <i class="bi bi-bag-check-fill me-2"></i> Mua sắm ngay
        </router-link>
      </div>
    </main>

    <!-- ========================================== -->
    <!-- MODAL CHỌN VOUCHER KHUYẾN MÃI -->
    <!-- ========================================== -->
    <div v-if="showVoucherModal" class="modal-backdrop-custom" @click.self="showVoucherModal = false">
      <div class="modal-voucher-dialog">
        <div class="modal-header-custom">
          <h5><i class="bi bi-ticket-perforated-fill text-danger me-2"></i>Chọn Voucher TechMart Ưu Đãi</h5>
          <button class="btn-close-modal" @click="showVoucherModal = false"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-body-custom">
          <div
            v-for="v in availableVouchers"
            :key="v.code"
            class="voucher-card-item"
            :class="{ active: voucherCode === v.code && isVoucherApplied }"
            @click="applyVoucherPreset(v.code)"
          >
            <div class="v-left">
              <span class="v-badge">{{ v.code }}</span>
              <strong class="v-discount">{{ v.title }}</strong>
              <p class="v-condition">{{ v.condition }}</p>
            </div>
            <div class="v-right">
              <button class="btn-select-v">
                {{ voucherCode === v.code && isVoucherApplied ? 'Đang dùng' : 'Chọn' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL XÁC NHẬN ĐẶT HÀNG & THANH TOÁN PAYOS / COD -->
    <!-- ========================================== -->
    <div v-if="showCheckoutModal" class="modal-backdrop-custom" @click.self="closeCheckoutModal">
      <div class="modal-checkout-dialog">
        <div class="modal-header-custom bg-primary text-white">
          <h5>
            <i class="bi bi-shield-check me-2"></i>
            {{ codSuccessOrder ? 'Đặt Hàng Thành Công' : 'Thông Tin Giao Hàng & Thanh Toán' }}
          </h5>
          <button class="btn-close-modal text-white" @click="closeCheckoutModal"><i class="bi bi-x-lg"></i></button>
        </div>

        <!-- Màn hình kết quả COD -->
        <div v-if="codSuccessOrder" class="modal-body-custom p-4">
          <div class="text-center mb-3">
            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
            <h4 class="mt-2 fw-bold text-dark">Đặt Hàng COD Thành Công!</h4>
            <p class="text-muted small">
              Mã đơn hàng của bạn là <strong class="text-primary">#{{ codSuccessOrder.ma_don_hang }}</strong>.
              Tổng thanh toán khi nhận hàng: <strong class="text-danger">{{ formatCurrency(codSuccessOrder.tong_thanh_toan) }}</strong>.
            </p>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-4">
            <button class="btn btn-secondary btn-sm px-3" @click="closeCheckoutModal">Đóng</button>
            <router-link to="/" class="btn btn-primary btn-sm px-4">Về Trang Chủ</router-link>
          </div>
        </div>

        <!-- Form nhập thông tin & chọn cổng PayOS / COD -->
        <div v-else class="modal-body-custom p-4">
          <div v-if="checkoutError" class="alert alert-danger small py-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ checkoutError }}
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold mb-1">Họ tên người nhận <span class="text-danger">*</span></label>
              <input
                type="text"
                v-model="checkoutForm.ho_ten_nguoi_nhan"
                class="form-control form-control-sm"
                placeholder="VD: Nguyễn Văn Kỳ"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold mb-1">Số điện thoại <span class="text-danger">*</span></label>
              <input
                type="text"
                v-model="checkoutForm.so_dien_thoai_nhan"
                class="form-control form-control-sm"
                placeholder="VD: 0905123456"
              />
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold mb-1">Địa chỉ giao hàng <span class="text-danger">*</span></label>
              <input
                type="text"
                v-model="checkoutForm.dia_chi_giao_hang"
                class="form-control form-control-sm"
                placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/TP"
              />
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold mb-1">Ghi chú đơn hàng</label>
              <input
                type="text"
                v-model="checkoutForm.ghi_chu"
                class="form-control form-control-sm"
                placeholder="Giao giờ hành chính, gọi trước khi đến..."
              />
            </div>
          </div>

          <!-- Chọn phương thức thanh toán -->
          <div class="mb-3">
            <label class="form-label small fw-bold mb-2">Chọn phương thức thanh toán</label>
            <div class="d-flex flex-column gap-2">
              <label
                class="p-2 rounded border d-flex align-items-center justify-content-between cursor-pointer"
                :class="checkoutForm.phuong_thuc_thanh_toan === 'payos' ? 'border-primary bg-primary-subtle' : 'bg-light'"
              >
                <div class="d-flex align-items-center gap-2">
                  <input type="radio" value="payos" v-model="checkoutForm.phuong_thuc_thanh_toan" />
                  <div>
                    <div class="fw-bold small text-dark">
                      <i class="bi bi-qr-code-scan text-primary me-1"></i>
                      Thanh toán VietQR qua PayOS (Tự động xác nhận)
                    </div>
                    <div class="text-muted" style="font-size: 11px;">Quét mã QR bằng App Ngân hàng bất kỳ — Xác thực tức thì 24/7</div>
                  </div>
                </div>
                <span class="badge bg-primary">Khuyên dùng</span>
              </label>

              <label
                class="p-2 rounded border d-flex align-items-center justify-content-between cursor-pointer"
                :class="checkoutForm.phuong_thuc_thanh_toan === 'vnpay' ? 'border-danger bg-danger-subtle' : 'bg-light'"
              >
                <div class="d-flex align-items-center gap-2">
                  <input type="radio" value="vnpay" v-model="checkoutForm.phuong_thuc_thanh_toan" />
                  <div>
                    <div class="fw-bold small text-dark">
                      <i class="bi bi-wallet2 text-danger me-1"></i>
                      Thanh toán qua Cổng VNPAY Sandbox (ATM / QR / Visa)
                    </div>
                    <div class="text-muted" style="font-size: 11px;">Thẻ test NCB: 9704198526191432198 • NGUYEN VAN A • 07/15 • OTP 123456 (Tối thiểu 5.000đ)</div>
                  </div>
                </div>
                <span class="badge bg-danger">VNPAY 2.1</span>
              </label>

              <label
                class="p-2 rounded border d-flex align-items-center justify-content-between cursor-pointer"
                :class="checkoutForm.phuong_thuc_thanh_toan === 'cod' ? 'border-success bg-success-subtle' : 'bg-light'"
              >
                <div class="d-flex align-items-center gap-2">
                  <input type="radio" value="cod" v-model="checkoutForm.phuong_thuc_thanh_toan" />
                  <div>
                    <div class="fw-bold small text-dark">
                      <i class="bi bi-cash-stack text-success me-1"></i>
                      Thanh toán tiền mặt khi nhận hàng (COD)
                    </div>
                    <div class="text-muted" style="font-size: 11px;">Kiểm tra máy & mã Serial trước khi thanh toán cho shipper</div>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center mb-3">
            <span class="small fw-semibold">Tổng tiền cần thanh toán:</span>
            <strong class="text-danger fs-5">{{ formatCurrency(finalTotalAmount) }}</strong>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-secondary btn-sm px-3" :disabled="isSubmittingOrder" @click="closeCheckoutModal">Hủy</button>
            <button
              class="btn btn-primary btn-sm px-4 fw-bold"
              :disabled="isSubmittingOrder"
              @click="submitCheckoutOrder"
            >
              <span v-if="isSubmittingOrder" class="spinner-border spinner-border-sm me-1"></span>
              {{
                isSubmittingOrder
                  ? 'Đang kết nối cổng thanh toán...'
                  : (checkoutForm.phuong_thuc_thanh_toan === 'payos'
                    ? 'Thanh Toán PayOS Ngay'
                    : (checkoutForm.phuong_thuc_thanh_toan === 'vnpay' ? 'Thanh Toán VNPAY Ngay' : 'Xác Nhận Đặt Hàng COD'))
              }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CartIndex',
  data() {
    return {
      loading: false,
      currentUser: null,
      // Dữ liệu giỏ hàng lấy hoàn toàn từ API Backend
      cartItems: [],

      // Voucher
      voucherCode: '',
      appliedVoucherName: '',
      voucherDiscount: 0,
      isVoucherApplied: false,
      voucherMessage: '',
      showVoucherModal: false,

      // Danh sách voucher có sẵn (khớp DB ma_giam_gia + preset demo)
      availableVouchers: [
        {
          code: 'WELCOME100',
          title: 'Giảm 10% tối đa 200.000đ',
          discount: 200000,
          condition: 'Đơn hàng từ 500.000đ'
        },
        {
          code: 'SALEOFF50K',
          title: 'Giảm 50.000đ',
          discount: 50000,
          condition: 'Đơn hàng từ 1.000.000đ'
        },
        {
          code: 'VIPLAPTOP',
          title: 'Giảm 1.000.000đ',
          discount: 1000000,
          condition: 'Đơn hàng Laptop trên 20.000.000đ'
        },
        {
          code: 'TECH500',
          title: 'Giảm 500.000đ',
          discount: 500000,
          condition: 'Đơn hàng từ 10.000.000đ'
        }
      ],

      showCheckoutModal: false,
      isSubmittingOrder: false,
      checkoutError: '',
      codSuccessOrder: null,
      checkoutForm: {
        ho_ten_nguoi_nhan: '',
        so_dien_thoai_nhan: '',
        dia_chi_giao_hang: '',
        ghi_chu: '',
        phuong_thuc_thanh_toan: 'payos'
      }
    }
  },
  computed: {
    isAllSelected() {
      if (this.cartItems.length === 0) return false;
      return this.cartItems.filter(i => !i.isGift).every(i => i.selected);
    },
    selectedCount() {
      return this.cartItems.filter(i => i.selected).length;
    },
    subtotalAmount() {
      return this.cartItems
        .filter(i => i.selected)
        .reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    finalTotalAmount() {
      const total = this.subtotalAmount - (this.isVoucherApplied ? this.voucherDiscount : 0);
      return Math.max(0, total);
    },
    totalSavings() {
      // Tiết kiệm: (Giá gốc - Giá bán) * SL + Voucher discount
      let savings = 0;
      this.cartItems.forEach(i => {
        if (i.selected && i.originalPrice) {
          savings += (i.originalPrice - i.price) * i.quantity;
        }
      });
      if (this.isVoucherApplied) {
        savings += this.voucherDiscount;
      }
      return savings;
    }
  },
  mounted() {
    this.checkCurrentUser();
    window.addEventListener('auth-change', this.checkCurrentUser);
    this.loadCartData();
  },
  beforeUnmount() {
    window.removeEventListener('auth-change', this.checkCurrentUser);
  },
  methods: {
    checkCurrentUser() {
      const stored = localStorage.getItem('customer_user');
      const token = localStorage.getItem('customer_token');
      if (stored && token) {
        try {
          this.currentUser = JSON.parse(stored);
        } catch (e) {
          this.currentUser = null;
        }
      } else {
        this.currentUser = null;
      }
    },
    handleLogout() {
      if (confirm('Bạn có chắc chắn muốn đăng xuất tài khoản?')) {
        const token = localStorage.getItem('customer_token');
        if (token) {
          axios.post('http://127.0.0.1:8000/api/client/dang-xuat', {}, {
            headers: { Authorization: `Bearer ${token}` }
          }).catch(() => {});
        }
        localStorage.removeItem('customer_token');
        localStorage.removeItem('customer_user');
        localStorage.removeItem('user_id');
        localStorage.removeItem('user_name');
        this.currentUser = null;
        window.dispatchEvent(new Event('auth-change'));
      }
    },
    async loadCartData() {
      this.loading = true;
      const sessionId = localStorage.getItem('techmart_session_id') || '';
      const userId = localStorage.getItem('user_id') || null;

      if (!userId && !sessionId) {
        this.cartItems = [];
        this.saveCartToStorage();
        this.loading = false;
        return;
      }

      try {
        const res = await axios.get('http://127.0.0.1:8000/api/client/gio-hang/get-data', {
          params: { session_id: sessionId, user_id: userId }
        });
        if (res.data && res.data.status && Array.isArray(res.data.data)) {
          this.cartItems = res.data.data;
          this.saveCartToStorage();
        } else {
          this.cartItems = [];
          this.saveCartToStorage();
        }
      } catch (e) {
        console.warn('API get-data error:', e);
        this.cartItems = [];
      } finally {
        this.loading = false;
      }
    },
    toggleSelectAll(e) {
      const checked = e.target.checked;
      this.cartItems.forEach(item => {
        item.selected = checked;
      });
      this.saveCartToStorage();
    },
    async syncUpdateQty(item) {
      const targetId = item.gio_hang_id || item.id;
      if (!targetId) return;
      try {
        await axios.post('http://127.0.0.1:8000/api/client/gio-hang/update', {
          id: targetId,
          so_luong: item.quantity
        });
      } catch (e) {
        console.warn('Sync update quantity failed:', e);
      }
    },
    increaseQty(idx) {
      const item = this.cartItems[idx];
      if (item.quantity < item.stock) {
        item.quantity++;
        this.saveCartToStorage();
        this.syncUpdateQty(item);
      }
    },
    decreaseQty(idx) {
      const item = this.cartItems[idx];
      if (item.quantity > 1) {
        item.quantity--;
        this.saveCartToStorage();
        this.syncUpdateQty(item);
      }
    },
    validateQty(idx) {
      const item = this.cartItems[idx];
      if (item.quantity < 1 || isNaN(item.quantity)) item.quantity = 1;
      if (item.quantity > item.stock) item.quantity = item.stock;
      this.saveCartToStorage();
      this.syncUpdateQty(item);
    },
    async deleteItem(idx) {
      const item = this.cartItems[idx];
      if (confirm(`Bạn có chắc chắn muốn xóa "${item.name}" khỏi giỏ hàng?`)) {
        const removed = this.cartItems.splice(idx, 1)[0];
        this.saveCartToStorage();
        const targetId = removed ? (removed.gio_hang_id || removed.id) : null;
        if (targetId) {
          try {
            await axios.post('http://127.0.0.1:8000/api/client/gio-hang/delete', {
              id: targetId
            });
          } catch (e) {
            console.warn('Sync delete item failed:', e);
          }
        }
      }
    },
    async clearAllCart() {
      if (confirm('Bạn có chắc chắn muốn xóa toàn bộ sản phẩm trong giỏ hàng?')) {
        this.cartItems = [];
        this.saveCartToStorage();
        const sessionId = localStorage.getItem('techmart_session_id');
        const userId = localStorage.getItem('user_id');
        try {
          await axios.post('http://127.0.0.1:8000/api/client/gio-hang/clear', {
            session_id: sessionId,
            user_id: userId
          });
        } catch (e) {
          console.warn('Sync clear cart failed:', e);
        }
      }
    },
    handleVoucherAction() {
      if (this.isVoucherApplied) {
        // Hủy mã
        this.isVoucherApplied = false;
        this.appliedVoucherName = '';
        this.voucherDiscount = 0;
        this.voucherMessage = 'Đã hủy áp dụng mã giảm giá.';
        this.voucherCode = '';
      } else {
        // Áp dụng
        const code = (this.voucherCode || '').trim().toUpperCase();
        if (!code) {
          this.voucherMessage = 'Vui lòng nhập mã voucher!';
          return;
        }
        const found = this.availableVouchers.find(v => v.code === code);
        if (found) {
          this.isVoucherApplied = true;
          this.appliedVoucherName = found.code;
          this.voucherDiscount = found.discount;
          this.voucherMessage = `Áp dụng thành công mã ${found.code}: ${found.title}!`;
        } else {
          this.voucherMessage = 'Mã giảm giá không tồn tại hoặc đã hết hạn!';
        }
      }
    },
    applyVoucherPreset(code) {
      this.voucherCode = code;
      this.showVoucherModal = false;
      this.isVoucherApplied = false;
      this.handleVoucherAction();
    },
    handleCheckout() {
      if (this.selectedCount === 0) {
        alert('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
        return;
      }
      const token = localStorage.getItem('customer_token');
      if (!token) {
        if (confirm('Quý khách cần đăng nhập tài khoản TechMart để tiến hành thanh toán đơn hàng. Bạn có muốn chuyển tới trang Đăng nhập ngay không?')) {
          this.$router.push('/login?redirect=/checkout');
        }
        return;
      }

      // Lưu trữ danh sách sản phẩm được chọn & voucher vào localStorage để trang Checkout sử dụng
      const selectedItems = this.cartItems
        .filter(i => i.selected && !i.isGift)
        .map(i => ({
          gio_hang_id: i.gio_hang_id || i.id || null,
          bien_the_id: i.bien_the_id || null,
          ten_san_pham: i.name || i.ten_san_pham,
          ten_bien_the: i.ten_bien_the || i.specs || 'Tiêu chuẩn',
          quantity: i.quantity || 1,
          price: i.price || 0,
          image: i.image || i.hinh_anh || ''
        }));

      localStorage.setItem('techmart_checkout_items', JSON.stringify(selectedItems));

      if (this.isVoucherApplied && this.appliedVoucherName) {
        localStorage.setItem('techmart_checkout_voucher', JSON.stringify({
          code: this.appliedVoucherName,
          discount: this.voucherDiscount
        }));
      } else {
        localStorage.removeItem('techmart_checkout_voucher');
      }

      // Chuyển hướng người dùng sang trang Đặt hàng & Bản đồ (/checkout)
      this.$router.push('/checkout');
    },
    closeCheckoutModal() {
      if (this.isSubmittingOrder) return;
      this.showCheckoutModal = false;
      this.codSuccessOrder = null;
      this.checkoutError = '';
    },
    async submitCheckoutOrder() {
      this.checkoutError = '';
      if (!this.checkoutForm.ho_ten_nguoi_nhan.trim() || !this.checkoutForm.so_dien_thoai_nhan.trim() || !this.checkoutForm.dia_chi_giao_hang.trim()) {
        this.checkoutError = 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ giao hàng!';
        return;
      }

      const selectedItems = this.cartItems
        .filter(i => i.selected && !i.isGift)
        .map(i => ({
          gio_hang_id: i.gio_hang_id || null,
          bien_the_id: i.bien_the_id || null,
          ten_san_pham: i.name || i.ten_san_pham,
          ten_bien_the: i.ten_bien_the || i.specs || 'Tiêu chuẩn',
          quantity: i.quantity || 1,
          price: i.price || 0
        }));

      if (selectedItems.length === 0) {
        this.checkoutError = 'Vui lòng chọn ít nhất 1 sản phẩm hợp lệ!';
        return;
      }

      this.isSubmittingOrder = true;
      const sessionId = localStorage.getItem('techmart_session_id') || null;
      const userId = localStorage.getItem('user_id') || (this.currentUser ? this.currentUser.id : null);

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/client/don-hang/checkout', {
          user_id: userId,
          session_id: sessionId,
          ho_ten_nguoi_nhan: this.checkoutForm.ho_ten_nguoi_nhan.trim(),
          so_dien_thoai_nhan: this.checkoutForm.so_dien_thoai_nhan.trim(),
          dia_chi_giao_hang: this.checkoutForm.dia_chi_giao_hang.trim(),
          ghi_chu: this.checkoutForm.ghi_chu.trim(),
          phuong_thuc_thanh_toan: this.checkoutForm.phuong_thuc_thanh_toan,
          ma_voucher: this.isVoucherApplied ? this.appliedVoucherName : '',
          so_tien_giam: this.isVoucherApplied ? this.voucherDiscount : 0,
          items: selectedItems
        });

        if (res.data && res.data.status) {
          // Xóa các món vừa đặt khỏi mảng cartItems local
          this.cartItems = this.cartItems.filter(i => !i.selected || i.isGift);
          this.saveCartToStorage();

          if (res.data.payment_method === 'payos' && res.data.checkout_url) {
            window.location.href = res.data.checkout_url;
            return;
          }

          if (res.data.payment_method === 'vnpay' && res.data.payment_url) {
            window.location.href = res.data.payment_url;
            return;
          }

          this.codSuccessOrder = res.data.don_hang || {
            ma_don_hang: 'MOI',
            tong_thanh_toan: this.finalTotalAmount
          };
        } else {
          this.checkoutError = (res.data && res.data.message) || 'Không thể khởi tạo đơn hàng.';
        }
      } catch (err) {
        this.checkoutError = err?.response?.data?.message || 'Lỗi kết nối máy chủ thanh toán (http://127.0.0.1:8000).';
      } finally {
        this.isSubmittingOrder = false;
      }
    },
    formatCurrency(val) {
      if (!val) return '0đ';
      return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
    },
    saveCartToStorage() {
      try {
        localStorage.setItem('techmart_cart', JSON.stringify(this.cartItems));
        localStorage.setItem('techmart_cart_count', String(this.cartItems.length));
      } catch (e) {
        console.warn('Cannot save cart to localStorage', e);
      }
    }
  }
}
</script>

<style scoped>
/* ==========================================
   CSS GIAO DIỆN GIỎ HÀNG CHUẨN WIREFRAME 07
   ========================================== */
.cart-page-wrapper {
  background-color: #f8fafc;
  min-height: 100vh;
  color: #0f172a;
  font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 16px;
}

/* ❶ HEADER TOP */
.cart-header {
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.cart-header-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.cart-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.cart-logo .logo-icon {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #fbbf24;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.cart-logo .logo-text {
  font-size: 20px;
  font-weight: 900;
  color: #0f172a;
  letter-spacing: -0.5px;
}

.cart-breadcrumb {
  display: flex;
  align-items: center;
  font-size: 13px;
  color: #64748b;
  border-left: 1px solid #cbd5e1;
  padding-left: 18px;
}

.breadcrumb-link {
  color: #475569;
  text-decoration: none;
  transition: color 0.15s;
}

.breadcrumb-link:hover {
  color: #2563eb;
}

.breadcrumb-separator {
  font-size: 11px;
  margin: 0 8px;
  color: #94a3b8;
}

.breadcrumb-current {
  color: #0f172a;
  font-weight: 700;
}

.hotline-box {
  display: flex;
  align-items: center;
}

.hotline-text {
  font-size: 12.5px;
  color: #475569;
}

.hotline-number {
  color: #dc2626;
  font-size: 15px;
  margin: 0 4px;
}

.hotline-time {
  font-size: 11.5px;
  color: #94a3b8;
}

.cart-auth-box {
  display: flex;
  align-items: center;
  border-left: 1px solid #e2e8f0;
  padding-left: 14px;
}

.cart-user-name {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.btn-cart-logout {
  background: transparent;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  color: #ef4444;
  padding: 3px 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cart-logout:hover {
  background: #fef2f2;
  border-color: #fca5a5;
}

.btn-cart-login {
  background: #2563eb;
  color: #ffffff !important;
  font-size: 12.5px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: background 0.2s;
}

.btn-cart-login:hover {
  background: #1d4ed8;
}

/* ❷ THÂN TRANG & TIÊU ĐỀ */
.cart-main-content {
  margin-top: 24px;
}

.cart-page-title-wrap {
  margin-bottom: 20px;
}

.cart-page-title {
  font-size: 22px;
  font-weight: 900;
  color: #0f172a;
  display: flex;
  align-items: center;
  margin-bottom: 4px;
}

.cart-page-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

/* BỐ CỤC 2 CỘT */
.cart-layout-grid {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 24px;
  align-items: flex-start;
}

/* CỘT TRÁI: BẢNG SẢN PHẨM */
.cart-table-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  overflow: hidden;
}

.cart-table-header {
  display: grid;
  grid-template-columns: 1fr 120px 130px 130px 70px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 16px;
  font-size: 12.5px;
  font-weight: 800;
  color: #475569;
  letter-spacing: 0.3px;
  align-items: center;
}

/* Custom Checkbox */
.custom-checkbox-wrap {
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  position: relative;
  user-select: none;
}

.custom-checkbox-wrap input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkbox-checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #cbd5e1;
  border-radius: 4px;
  display: inline-block;
  margin-right: 10px;
  position: relative;
  transition: all 0.15s;
  background: #ffffff;
}

.custom-checkbox-wrap input:checked ~ .checkbox-checkmark {
  background-color: #2563eb;
  border-color: #2563eb;
}

.custom-checkbox-wrap input:checked ~ .checkbox-checkmark::after {
  content: '';
  position: absolute;
  left: 5px;
  top: 2px;
  width: 5px;
  height: 9px;
  border: solid #ffffff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.label-all-text {
  font-size: 12.5px;
  font-weight: 800;
  color: #0f172a;
}

/* DÒNG SẢN PHẨM */
.cart-items-list {
  divide-y: 1px solid #f1f5f9;
}

.cart-item-row {
  display: grid;
  grid-template-columns: 1fr 120px 130px 130px 70px;
  padding: 18px 16px;
  align-items: center;
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}

.cart-item-row:hover {
  background-color: #fafcff;
}

.item-gift-row {
  background-color: #fffbfb;
}

.item-info-col {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.item-checkbox {
  margin-top: 28px;
}

.item-img-box {
  width: 90px;
  height: 90px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
  position: relative;
  flex-shrink: 0;
}

.item-img-box img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.gift-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #dc2626;
  color: #ffffff;
  font-size: 9px;
  font-weight: 800;
  padding: 1px 5px;
  border-radius: 4px;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-size: 14.5px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.35;
  margin: 0 0 4px;
}

.item-sku,
.item-specs {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 3px;
  line-height: 1.3;
}

.sku-val {
  color: #334155;
  margin-left: 4px;
}

.specs-val {
  color: #0f172a;
  margin-left: 4px;
  font-weight: 600;
}

.item-warranty {
  font-size: 11.5px;
  color: #16a34a;
  font-weight: 600;
  margin-top: 4px;
  display: flex;
  align-items: center;
}

.warranty-gift {
  color: #dc2626;
}

/* GIÁ TIỀN & SỐ LƯỢNG */
.item-price-col .price-current {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
}

.item-price-col .price-original {
  font-size: 11.5px;
  color: #94a3b8;
  text-decoration: line-through;
  margin-top: 2px;
}

.qty-control-box {
  display: inline-flex;
  align-items: center;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  overflow: hidden;
  background: #ffffff;
}

.btn-qty {
  background: #f8fafc;
  border: none;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #334155;
  transition: background 0.15s;
}

.btn-qty:hover:not(:disabled) {
  background: #e2e8f0;
}

.btn-qty:disabled {
  color: #cbd5e1;
  cursor: not-allowed;
}

.input-qty {
  width: 38px;
  height: 28px;
  text-align: center;
  border: none;
  border-left: 1px solid #cbd5e1;
  border-right: 1px solid #cbd5e1;
  font-size: 13px;
  font-weight: 700;
  outline: none;
  -moz-appearance: textfield;
}

.input-qty::-webkit-inner-spin-button,
.input-qty::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.stock-note {
  font-size: 11px;
  color: #64748b;
  margin-top: 4px;
}

.item-total-col .item-subtotal {
  font-size: 15px;
  font-weight: 800;
  color: #dc2626;
}

.btn-delete-item {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 12px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.15s;
}

.btn-delete-item:hover:not(:disabled) {
  color: #dc2626;
  background: #fee2e2;
}

.btn-delete-item:disabled {
  color: #cbd5e1;
  cursor: not-allowed;
}

/* ĐÁY BẢNG: NÚT TIẾP TỤC & XÓA TẤT CẢ */
.cart-table-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.btn-continue-shopping {
  color: #2563eb;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: transform 0.15s;
}

.btn-continue-shopping:hover {
  transform: translateX(-3px);
  text-decoration: underline;
}

.btn-clear-cart {
  background: transparent;
  border: 1px solid #cbd5e1;
  color: #dc2626;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-clear-cart:hover {
  background: #dc2626;
  color: #ffffff;
  border-color: #dc2626;
}

/* DÒNG CAM KẾT SERIAL/IMEI VẬT LÝ */
.serial-commitment-card {
  margin-top: 16px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 10px;
  padding: 14px 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.commitment-icon {
  font-size: 22px;
  line-height: 1;
}

.commitment-text {
  font-size: 12.5px;
  color: #166534;
  line-height: 1.45;
}

/* CỘT PHẢI: TÓM TẮT ĐƠN HÀNG */
.order-summary-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.summary-heading {
  font-size: 15.5px;
  font-weight: 900;
  color: #0f172a;
  letter-spacing: -0.3px;
  margin: 0 0 16px;
  display: flex;
  align-items: center;
}

/* VOUCHER SECTION */
.voucher-input-section {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  padding: 14px;
  margin-bottom: 16px;
}

.voucher-label {
  display: block;
  font-size: 12.5px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 8px;
}

.voucher-input-group {
  display: flex;
  gap: 8px;
}

.input-voucher {
  flex: 1;
  padding: 8px 12px;
  font-size: 13px;
  font-weight: 700;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  text-transform: uppercase;
  outline: none;
}

.input-voucher:focus {
  border-color: #2563eb;
}

.btn-apply-voucher {
  background: #0f172a;
  color: #ffffff;
  border: none;
  font-size: 12.5px;
  font-weight: 700;
  padding: 0 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-apply-voucher:hover {
  background: #2563eb;
}

.btn-remove-voucher {
  background: #dc2626 !important;
}

.btn-suggest-vouchers {
  background: transparent;
  border: none;
  color: #2563eb;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 0 0;
  cursor: pointer;
  text-align: left;
  display: block;
}

.btn-suggest-vouchers:hover {
  text-decoration: underline;
}

.voucher-alert {
  font-size: 11.5px;
  margin-top: 8px;
  padding: 6px 10px;
  border-radius: 6px;
}

.alert-success {
  background: #dcfce7;
  color: #15803d;
}

.alert-danger {
  background: #fee2e2;
  color: #b91c1c;
}

/* CHI TIẾT TÍNH TIỀN */
.summary-breakdown {
  display: flex;
  flex-direction: column;
  gap: 10px;
  font-size: 13px;
}

.breakdown-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.row-label {
  color: #64748b;
}

.row-value {
  color: #0f172a;
  font-weight: 700;
}

.summary-divider {
  border: 0;
  border-top: 1px solid #e2e8f0;
  margin: 16px 0;
}

/* TỔNG THANH TOÁN */
.final-total-box {
  margin-bottom: 18px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
}

.total-label {
  font-size: 14.5px;
  font-weight: 800;
  color: #0f172a;
}

.total-amount {
  font-size: 22px;
  font-weight: 900;
  color: #dc2626;
  letter-spacing: -0.5px;
}

.saving-note {
  font-size: 11.5px;
  color: #16a34a;
  text-align: right;
  margin-top: 2px;
}

/* NÚT TIẾN HÀNH THANH TOÁN (LỚN) */
.btn-proceed-checkout {
  width: 100%;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 14px 16px;
  font-size: 14px;
  font-weight: 800;
  letter-spacing: 0.5px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-proceed-checkout:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
}

.btn-proceed-checkout:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
  box-shadow: none;
}

/* HỖ TRỢ THANH TOÁN BẢO MẬT (CALLOUT 15) */
.payment-partners-section {
  margin-top: 22px;
  padding-top: 16px;
  border-top: 1px dashed #e2e8f0;
}

.partners-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  margin-bottom: 10px;
}

.partners-badges-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.pay-badge {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 7px 6px;
  font-size: 11.5px;
  font-weight: 700;
  color: #334155;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  transition: border-color 0.15s;
}

.pay-badge:hover {
  border-color: #2563eb;
  background: #eff6ff;
}

/* EMPTY CART */
.empty-cart-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 60px 20px;
  max-width: 600px;
  margin: 30px auto;
}

.empty-cart-icon {
  font-size: 64px;
  color: #cbd5e1;
  margin-bottom: 16px;
}

.empty-cart-title {
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 8px;
}

.empty-cart-desc {
  font-size: 13.5px;
  color: #64748b;
  margin-bottom: 24px;
}

.btn-shop-now {
  background: #2563eb;
  color: #ffffff;
  text-decoration: none;
  font-size: 14px;
  font-weight: 800;
  padding: 10px 28px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
  transition: all 0.2s;
}

.btn-shop-now:hover {
  background: #1d4ed8;
  transform: translateY(-2px);
}

/* MODAL TÙY CHỌN VOUCHER */
.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-voucher-dialog,
.modal-checkout-dialog {
  background: #ffffff;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
  animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalFadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.modal-header-custom {
  padding: 14px 18px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header-custom h5 {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
}

.btn-close-modal {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #64748b;
}

.modal-body-custom {
  padding: 16px;
  max-height: 420px;
  overflow-y: auto;
}

.voucher-card-item {
  border: 1px dashed #cbd5e1;
  border-radius: 8px;
  padding: 12px 14px;
  margin-bottom: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  transition: all 0.15s;
}

.voucher-card-item:hover {
  border-color: #2563eb;
  background: #eff6ff;
}

.voucher-card-item.active {
  border-color: #16a34a;
  background: #f0fdf4;
}

.v-badge {
  background: #fee2e2;
  color: #dc2626;
  font-size: 11px;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 4px;
}

.v-discount {
  display: block;
  font-size: 14px;
  color: #0f172a;
  margin-top: 4px;
}

.v-condition {
  font-size: 11.5px;
  color: #64748b;
  margin: 2px 0 0;
}

.btn-select-v {
  background: #0f172a;
  color: #ffffff;
  border: none;
  font-size: 11.5px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 4px;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .cart-layout-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .cart-table-header {
    display: none;
  }
  .cart-item-row {
    grid-template-columns: 1fr;
    gap: 12px;
    position: relative;
    padding-bottom: 20px;
  }
  .item-price-col,
  .item-qty-col,
  .item-total-col {
    text-align: left !important;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .item-price-col::before {
    content: 'Đơn giá:';
    font-size: 12px;
    color: #64748b;
  }
  .item-qty-col::before {
    content: 'Số lượng:';
    font-size: 12px;
    color: #64748b;
  }
  .item-total-col::before {
    content: 'Thành tiền:';
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
  }
  .item-action-col {
    position: absolute;
    top: 14px;
    right: 14px;
  }
}
</style>
