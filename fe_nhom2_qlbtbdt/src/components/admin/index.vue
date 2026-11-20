<template>
  <div class="admin-layout">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar shadow-sm">
      <div class="sidebar-brand">
        <i class="bi bi-cpu-fill text-warning fs-3 me-2"></i>
        <div>
          <div class="brand-title">TECHMART ADMIN</div>
          <div class="brand-sub">Hệ Thống Quản Trị BI & Kho</div>
        </div>
      </div>

      <nav class="sidebar-menu">
        <div class="menu-label">TỔNG QUAN</div>
        <router-link to="/admin" class="menu-item active">
          <i class="bi bi-speedometer2 me-2"></i> Tổng Quan BI (Dashboard)
        </router-link>

        <div class="menu-label">QUẢN TRỊ NGHIỆP VỤ</div>
        <router-link to="/admin/orders" class="menu-item">
          <i class="bi bi-bag-check me-2"></i> Quản Lý Đơn Hàng
          <span v-if="orderStats.cho_xac_nhan > 0" class="badge bg-danger ms-auto rounded-pill">
            {{ orderStats.cho_xac_nhan }}
          </span>
        </router-link>
        <router-link to="/admin/categories" class="menu-item">
          <i class="bi bi-folder-fill me-2"></i> Cây Danh Mục & Thương Hiệu
        </router-link>
        <a href="#san-pham" class="menu-item" @click.prevent="scrollTo('top-products')">
          <i class="bi bi-laptop me-2"></i> Quản Lý Sản Phẩm
        </a>
        <router-link to="/admin/serial" class="menu-item">
          <i class="bi bi-upc-scan me-2 text-success"></i> Kho Serial & IMEI
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-auto small">Page riêng</span>
        </router-link>
        <a href="#voucher" class="menu-item" @click.prevent="alertFeature('Quản lý Voucher Khuyến Mãi')">
          <i class="bi bi-ticket-perforated me-2"></i> Quản Lý Voucher
        </a>

        <div class="menu-label">HỆ THỐNG & HẬU MÃI</div>
        <a href="#doi-soat-thanh-toan" class="menu-item" @click.prevent="scrollTo('doi-soat-thanh-toan')">
          <i class="bi bi-qr-code-scan me-2 text-info"></i> Đối Soát PayOS & Giao Dịch
        </a>
        <a href="#giam-sat-ai" class="menu-item" @click.prevent="scrollTo('giam-sat-ai')">
          <i class="bi bi-robot me-2 text-warning"></i> Giám Sát Trợ Lý AI
        </a>
        <a href="#nguoi-dung" class="menu-item" @click.prevent="alertFeature('Quản lý Người Dùng & Phân Quyền')">
          <i class="bi bi-people-fill me-2"></i> Người Dùng & Phân Quyền
        </a>
        <a href="#bao-hanh" class="menu-item" @click.prevent="alertFeature('Tiếp Nhận & Xử Lý Bảo Hành RMA')">
          <i class="bi bi-shield-check me-2"></i> Bảo Hành Điện Tử (RMA)
        </a>
        <router-link to="/" class="menu-item text-warning mt-4">
          <i class="bi bi-box-arrow-left me-2"></i> Về Website Khách Hàng
        </router-link>
      </nav>
    </aside>

    <!-- Admin Main Content -->
    <main class="admin-main">
      <!-- Admin Topbar -->
      <header class="admin-topbar">
        <div class="d-flex align-items-center">
          <div class="topbar-search me-3">
            <i class="bi bi-search text-muted me-2"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Tìm nhanh theo mã đơn, tên khách, SĐT..."
              class="search-input"
            />
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
            <i class="bi bi-circle-fill me-1 small" style="font-size: 8px;"></i> API Realtime
          </span>
        </div>

        <div class="topbar-actions d-flex align-items-center gap-3">
          <button
            class="btn btn-sm btn-outline-primary"
            @click="loadData(true)"
            :disabled="isLoading"
            title="Làm mới dữ liệu từ máy chủ"
          >
            <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin-anim': isLoading }"></i>
            {{ isLoading ? 'Đang tải...' : 'Làm mới' }}
          </button>

          <div class="admin-profile d-flex align-items-center">
            <div class="avatar-circle me-2 bg-primary text-white fw-bold">
              {{ adminInitial }}
            </div>
            <div>
              <div class="fw-bold small text-dark">{{ adminName }}</div>
              <div class="text-muted" style="font-size: 11px;">Quản Trị Viên Hệ Thống</div>
            </div>
          </div>
        </div>
      </header>

      <!-- Admin Page Content -->
      <div class="admin-content">
        <!-- Toast / Alert Notification -->
        <div v-if="alertMessage" :class="['alert', 'alert-' + alertType, 'alert-dismissible', 'fade', 'show', 'shadow-sm']" role="alert">
          <i :class="['bi', alertType === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill', 'me-2']"></i>
          {{ alertMessage }}
          <button type="button" class="btn-close" @click="alertMessage = ''"></button>
        </div>

        <!-- Page Header & Action Buttons -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
          <div>
            <h2 class="h4 fw-bold text-dark mb-1">
              <i class="bi bi-speedometer2 text-primary me-2"></i>BẢNG ĐIỀU KHIỂN TỔNG QUAN BI & DOANH THU
            </h2>
            <p class="text-muted small mb-0">
              Đồ án CDIO 4 - Hệ thống quản lý bán lẻ thiết bị điện tử tích hợp AI tư vấn (KhanhStore)
            </p>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" @click="exportCSV()">
              <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> Xuất Báo Cáo CSV
            </button>
            <button class="btn btn-primary btn-sm" @click="loadData(true)">
              <i class="bi bi-arrow-repeat me-1"></i> Đồng Bộ API
            </button>
          </div>
        </div>

        <!-- 4 KPI Cards (Bootstrap Grid) -->
        <div class="row g-3 mb-4">
          <!-- Card 1: Doanh Thu -->
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 kpi-card h-100 bg-white">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Tổng Doanh Thu</span>
                <span class="p-2 rounded-3 bg-success-subtle text-success">
                  <i class="bi bi-cash-stack fs-5"></i>
                </span>
              </div>
              <div class="h3 fw-bold text-dark mb-1">
                {{ formatCurrency(kpis.tong_doanh_thu) }}
              </div>
              <div class="small text-muted d-flex align-items-center">
                <span class="badge bg-success-subtle text-success me-2">
                  <i class="bi bi-graph-up me-1"></i>Doanh số tích lũy
                </span>
              </div>
            </div>
          </div>

          <!-- Card 2: Đơn Hàng -->
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 kpi-card h-100 bg-white">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Tổng Đơn Hàng</span>
                <span class="p-2 rounded-3 bg-primary-subtle text-primary">
                  <i class="bi bi-bag-check-fill fs-5"></i>
                </span>
              </div>
              <div class="h3 fw-bold text-dark mb-1">
                {{ kpis.tong_don_hang }} <span class="fs-6 fw-normal text-muted">đơn</span>
              </div>
              <div class="small text-muted d-flex align-items-center">
                <span v-if="orderStats.cho_xac_nhan > 0" class="badge bg-warning text-dark me-2">
                  {{ orderStats.cho_xac_nhan }} đơn chờ duyệt
                </span>
                <span v-else class="text-success small">Đã duyệt tất cả</span>
              </div>
            </div>
          </div>

          <!-- Card 3: Khách Hàng -->
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 kpi-card h-100 bg-white">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Khách Hàng</span>
                <span class="p-2 rounded-3 bg-info-subtle text-info">
                  <i class="bi bi-people-fill fs-5"></i>
                </span>
              </div>
              <div class="h3 fw-bold text-dark mb-1">
                {{ kpis.tong_khach_hang }} <span class="fs-6 fw-normal text-muted">thành viên</span>
              </div>
              <div class="small text-muted">
                <i class="bi bi-person-check text-success me-1"></i>Tài khoản hoạt động
              </div>
            </div>
          </div>

          <!-- Card 4: Sản Phẩm -->
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 kpi-card h-100 bg-white">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Sản Phẩm Trong Kho</span>
                <span class="p-2 rounded-3 bg-warning-subtle text-warning">
                  <i class="bi bi-laptop fs-5"></i>
                </span>
              </div>
              <div class="h3 fw-bold text-dark mb-1">
                {{ kpis.tong_san_pham }} <span class="fs-6 fw-normal text-muted">dòng máy</span>
              </div>
              <div class="small text-muted">
                <i class="bi bi-cpu text-primary me-1"></i>Sẵn sàng kinh doanh
              </div>
            </div>
          </div>
        </div>

        <!-- Order Status Pipeline (Tiến Trình Đơn Hàng) -->
        <div class="card border-0 shadow-sm p-3 mb-4 bg-white">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">
              <i class="bi bi-funnel-fill text-primary me-2"></i>Tiến Trình Xử Lý Đơn Hàng (Order Pipeline)
            </h6>
            <span class="small text-muted">Phân loại vòng đời đơn hàng</span>
          </div>

          <div class="row g-2 text-center">
            <div class="col">
              <div class="p-2 rounded bg-light border">
                <div class="small text-muted">Chờ xác nhận</div>
                <div class="fw-bold text-warning fs-5">{{ orderStats.cho_xac_nhan }}</div>
              </div>
            </div>
            <div class="col">
              <div class="p-2 rounded bg-light border">
                <div class="small text-muted">Đã xác nhận</div>
                <div class="fw-bold text-info fs-5">{{ orderStats.da_xac_nhan }}</div>
              </div>
            </div>
            <div class="col">
              <div class="p-2 rounded bg-light border">
                <div class="small text-muted">Đang đóng gói</div>
                <div class="fw-bold text-primary fs-5">{{ orderStats.dang_dong_goi }}</div>
              </div>
            </div>
            <div class="col">
              <div class="p-2 rounded bg-light border">
                <div class="small text-muted">Đang giao</div>
                <div class="fw-bold text-primary fs-5">{{ orderStats.dang_giao }}</div>
              </div>
            </div>
            <div class="col">
              <div class="p-2 rounded bg-light border">
                <div class="small text-muted">Giao thành công</div>
                <div class="fw-bold text-success fs-5">{{ orderStats.giao_thanh_cong }}</div>
              </div>
            </div>
            <div class="col">
              <div class="p-2 rounded bg-light border">
                <div class="small text-muted">Đã hủy</div>
                <div class="fw-bold text-danger fs-5">{{ orderStats.da_huy }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Orders Table Card -->
        <div class="card border-0 shadow-sm p-3 mb-4 bg-white">
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
              <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-receipt-cutoff text-primary me-2"></i>Danh Sách Đơn Hàng Gần Đây
              </h5>
              <span class="small text-muted">Quản lý và duyệt đơn theo thời gian thực</span>
            </div>

            <!-- Filter Status Buttons -->
            <div class="btn-group btn-group-sm" role="group">
              <button
                type="button"
                :class="['btn', filterStatus === 'ALL' ? 'btn-primary' : 'btn-outline-secondary']"
                @click="filterStatus = 'ALL'"
              >
                Tất cả ({{ allOrders.length }})
              </button>
              <button
                type="button"
                :class="['btn', filterStatus === 'cho_xac_nhan' ? 'btn-warning text-dark' : 'btn-outline-secondary']"
                @click="filterStatus = 'cho_xac_nhan'"
              >
                Chờ duyệt
              </button>
              <button
                type="button"
                :class="['btn', filterStatus === 'dang_giao' ? 'btn-primary' : 'btn-outline-secondary']"
                @click="filterStatus = 'dang_giao'"
              >
                Đang giao
              </button>
              <button
                type="button"
                :class="['btn', filterStatus === 'giao_thanh_cong' ? 'btn-success' : 'btn-outline-secondary']"
                @click="filterStatus = 'giao_thanh_cong'"
              >
                Hoàn tất
              </button>
              <button
                type="button"
                :class="['btn', filterStatus === 'da_huy' ? 'btn-danger' : 'btn-outline-secondary']"
                @click="filterStatus = 'da_huy'"
              >
                Đã hủy
              </button>
            </div>
          </div>

          <!-- Loading Spinner -->
          <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-2 text-muted small">Đang nạp dữ liệu đơn hàng từ máy chủ...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredOrders.length === 0" class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-2 text-muted">Không tìm thấy đơn hàng nào phù hợp với bộ lọc.</p>
          </div>

          <!-- Table Responsive -->
          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 130px;">Mã Đơn</th>
                  <th>Khách Hàng</th>
                  <th>Liên Hệ (SĐT)</th>
                  <th>Tổng Tiền</th>
                  <th>Thanh Toán</th>
                  <th>Trạng Thái Đơn</th>
                  <th>Thời Gian</th>
                  <th class="text-center" style="width: 120px;">Thao Tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ord in filteredOrders" :key="ord.id">
                  <td>
                    <span class="fw-bold text-primary font-monospace">
                      {{ ord.ma_don_hang || ('DH-' + ord.id) }}
                    </span>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ ord.ten_khach_hang || ord.ho_ten_nguoi_nhan }}</div>
                    <div class="small text-muted" style="font-size: 11px;">
                      {{ ord.dia_chi_giao_hang ? truncateText(ord.dia_chi_giao_hang, 30) : 'Địa chỉ mặc định' }}
                    </div>
                  </td>
                  <td>
                    <span class="small font-monospace">{{ ord.so_dien_thoai_nhan || ord.sdt_khach_hang || 'Chưa cập nhật' }}</span>
                  </td>
                  <td>
                    <span class="fw-bold text-dark">{{ formatCurrency(ord.tong_thanh_toan) }}</span>
                  </td>
                  <td>
                    <span :class="['badge', getPaymentBadge(ord)]">
                      {{ formatPaymentMethod(ord.phuong_thuc_thanh_toan) }}
                    </span>
                  </td>
                  <td>
                    <span :class="['badge', getOrderStatusBadge(ord.trang_thai_don_hang)]">
                      {{ formatOrderStatus(ord.trang_thai_don_hang) }}
                    </span>
                  </td>
                  <td class="small text-muted">
                    {{ formatDate(ord.ngay_dat || ord.created_at) }}
                  </td>
                  <td class="text-center">
                    <button
                      class="btn btn-outline-primary btn-sm px-2 py-1"
                      @click="openOrderModal(ord)"
                      title="Xem chi tiết và đổi trạng thái"
                    >
                      <i class="bi bi-pencil-square me-1"></i> Xử lý
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Top Products & Inventory Alert Section -->
        <div class="row g-3" id="top-products">
          <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                  <i class="bi bi-fire text-danger me-2"></i>Sản Phẩm Được Quan Tâm Nhiều Nhất
                </h6>
                <span class="small text-muted">Cập nhật lượt xem realtime</span>
              </div>

              <div v-if="topProducts.length === 0" class="text-muted small py-3 text-center">
                Chưa có dữ liệu sản phẩm
              </div>
              <div v-else class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Sản phẩm</th>
                      <th>Giá niêm yết</th>
                      <th>Lượt xem</th>
                      <th>Trạng thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="sp in topProducts" :key="sp.id">
                      <td class="fw-semibold text-dark">{{ sp.ten_san_pham }}</td>
                      <td class="text-primary fw-bold">{{ formatCurrency(sp.gia_khuyen_mai || sp.gia_goc) }}</td>
                      <td>
                        <span class="badge bg-light text-dark border">
                          <i class="bi bi-eye me-1"></i>{{ sp.luot_xem || 0 }}
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-success-subtle text-success">Đang bán</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
              <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-info-circle-fill text-info me-2"></i>Thông Tin Hệ Thống
              </h6>
              <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between px-0">
                  <span>Phiên bản Backend</span>
                  <span class="fw-bold font-monospace text-primary">Laravel 11.x</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                  <span>Giao diện Quản trị</span>
                  <span class="fw-bold font-monospace text-success">Vue 3 + Bootstrap 5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                  <span>Cổng thanh toán</span>
                  <span class="badge bg-primary-subtle text-primary">PayOS VietQR, VNPAY & COD</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                  <span>Trợ lý Trí tuệ Nhân tạo</span>
                  <span class="badge bg-warning-subtle text-warning">Gemini API RAG</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0">
                  <span>Trạng thái kết nối API</span>
                  <span class="badge bg-success">Connected 200 OK</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Phân hệ Đối soát Giao dịch Thanh toán (PayOS / VNPAY / COD) & Giám sát AI -->
        <div class="row g-3 mt-3">
          <div class="col-12 col-lg-8" id="doi-soat-thanh-toan">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                  <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-qr-code-scan text-primary me-2"></i>Quản Lý & Đối Soát Giao Dịch Thanh Toán (PayOS)
                  </h6>
                  <small class="text-muted">
                    Tổng thu qua cổng thành công: <strong class="text-success">{{ formatCurrency(paymentSummary.tong_tien_cong) }}</strong>
                    (PayOS: <strong class="text-primary">{{ formatCurrency(paymentSummary.tong_tien_payos) }}</strong>)
                  </small>
                </div>
                <select v-model="paymentGatewayFilter" class="form-select form-select-sm w-auto">
                  <option value="ALL">Tất cả cổng</option>
                  <option value="payos">PayOS VietQR</option>
                  <option value="vnpay">VNPAY</option>
                  <option value="cod">COD</option>
                </select>
              </div>

              <div v-if="filteredPayments.length === 0" class="text-muted small py-3 text-center">
                Chưa có lịch sử giao dịch thanh toán nào.
              </div>
              <div v-else class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Mã Giao Dịch</th>
                      <th>Mã Đơn</th>
                      <th>Cổng</th>
                      <th>Số Tiền</th>
                      <th>Trạng Thái</th>
                      <th class="text-end">Đối Soát</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="tx in filteredPayments.slice(0, 8)" :key="tx.id">
                      <td class="font-monospace small fw-semibold">{{ tx.ma_giao_dich }}</td>
                      <td class="small text-primary">#{{ tx.ma_don_hang || tx.don_hang_id }}</td>
                      <td>
                        <span :class="['badge', tx.cong_thanh_toan === 'payos' ? 'bg-primary-subtle text-primary' : (tx.cong_thanh_toan === 'vnpay' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary')]">
                          {{ (tx.cong_thanh_toan || '').toUpperCase() }}
                        </span>
                      </td>
                      <td class="fw-bold small">{{ formatCurrency(tx.so_tien) }}</td>
                      <td>
                        <span :class="['badge', tx.trang_thai === 'success' ? 'bg-success' : (tx.trang_thai === 'failed' ? 'bg-danger' : 'bg-warning text-dark')]">
                          {{ tx.trang_thai === 'success' ? 'Thành công' : (tx.trang_thai === 'failed' ? 'Thất bại' : 'Chờ TT') }}
                        </span>
                      </td>
                      <td class="text-end">
                        <button
                          v-if="tx.cong_thanh_toan === 'payos'"
                          class="btn btn-outline-primary btn-xs py-0 px-2"
                          style="font-size: 11px;"
                          :disabled="reconcilingId === tx.id"
                          @click="reconcilePayOsTx(tx)"
                        >
                          <i class="bi bi-arrow-repeat me-1"></i>
                          {{ reconcilingId === tx.id ? 'Đang check...' : 'Đối soát' }}
                        </button>
                        <span v-else class="text-muted small">—</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-4" id="giam-sat-ai">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
              <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-robot text-warning me-2"></i>Giám Sát Trợ Lý AI (Gemini RAG)
              </h6>
              <div class="row g-2 mb-3 text-center">
                <div class="col-4">
                  <div class="p-2 rounded bg-light border">
                    <div class="fw-bold text-primary fs-6">{{ aiAnalytics.total_sessions }}</div>
                    <div class="text-muted" style="font-size: 11px;">Phiên chat</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="p-2 rounded bg-light border">
                    <div class="fw-bold text-success fs-6">{{ aiAnalytics.total_messages }}</div>
                    <div class="text-muted" style="font-size: 11px;">Tin nhắn</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="p-2 rounded bg-light border">
                    <div class="fw-bold text-danger fs-6">{{ aiAnalytics.total_tokens }}</div>
                    <div class="text-muted" style="font-size: 11px;">Tokens</div>
                  </div>
                </div>
              </div>

              <div class="small fw-bold text-muted mb-2">Xu hướng SP được AI đề xuất nhiều:</div>
              <div v-if="!aiAnalytics.top_recommended_products || aiAnalytics.top_recommended_products.length === 0" class="text-muted small">
                Chưa có dữ liệu đề xuất AI.
              </div>
              <ul v-else class="list-group list-group-flush small">
                <li
                  v-for="sp in aiAnalytics.top_recommended_products"
                  :key="sp.id"
                  class="list-group-item d-flex justify-content-between align-items-center px-0 py-1"
                >
                  <span class="text-truncate me-2" :title="sp.ten_san_pham">{{ sp.ten_san_pham }}</span>
                  <span class="badge bg-warning-subtle text-dark">{{ sp.so_lan_de_xuat }} lần</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Thẻ liên kết nhanh tới Phân hệ Kho Serial & IMEI (UC08) -->
          <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm p-4 bg-white d-flex flex-row justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="bg-primary text-white p-3 rounded-3 me-3">
                  <i class="bi bi-upc-scan fs-3"></i>
                </div>
                <div>
                  <h6 class="fw-bold text-dark mb-1 fs-6">Phân Hệ Quản Trị Kho Serial & IMEI Từng Chiếc Máy (UC08 - FIFO)</h6>
                  <p class="text-muted small mb-0">Đã được xây dựng thành trang quản trị chuyên sâu độc lập: quản lý định danh vật lý độc bản, nhập lô tự động, và theo dõi bảo hành điện tử.</p>
                </div>
              </div>
              <router-link to="/admin/serial" class="btn btn-primary btn-sm px-3 text-nowrap shadow-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Mở Trang Kho Serial & IMEI
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </main>


    <!-- Modal Xử Lý / Cập Nhật Trạng Thái Đơn Hàng & GÁN SERIAL (UC09) -->
    <div
      v-if="selectedOrder"
      class="modal fade show d-block"
      tabindex="-1"
      style="background-color: rgba(0,0,0,0.65);"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-dark text-white py-3">
            <h5 class="modal-title h6 fw-bold">
              <i class="bi bi-pencil-square me-2 text-warning"></i>
              Xử Lý Đơn Hàng & Gán Mã Serial/IMEI Xuất Kho: #{{ selectedOrder.ma_don_hang || selectedOrder.id }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="selectedOrder = null"></button>
          </div>

          <div class="modal-body p-4">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded border">
                  <h6 class="fw-bold text-primary mb-2"><i class="bi bi-person-fill me-1"></i>Thông Tin Người Nhận</h6>
                  <p class="mb-1 small"><strong>Họ tên:</strong> {{ selectedOrder.ten_khach_hang || selectedOrder.ho_ten_nguoi_nhan }}</p>
                  <p class="mb-1 small"><strong>Điện thoại:</strong> {{ selectedOrder.so_dien_thoai_nhan || selectedOrder.sdt_khach_hang || 'N/A' }}</p>
                  <p class="mb-1 small"><strong>Địa chỉ:</strong> {{ selectedOrder.dia_chi_giao_hang || 'N/A' }}</p>
                  <p class="mb-0 small text-muted"><strong>Ghi chú:</strong> {{ selectedOrder.ghi_chu || 'Không có ghi chú' }}</p>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 bg-light rounded border">
                  <h6 class="fw-bold text-success mb-2"><i class="bi bi-cash me-1"></i>Thông Tin Tài Chính</h6>
                  <p class="mb-1 small"><strong>Tổng tiền hàng:</strong> {{ formatCurrency(selectedOrder.tong_tien_hang || selectedOrder.tong_thanh_toan) }}</p>
                  <p class="mb-1 small"><strong>Giảm giá Voucher:</strong> {{ formatCurrency(selectedOrder.so_tien_giam || 0) }}</p>
                  <p class="mb-1 small"><strong>Phí vận chuyển:</strong> {{ formatCurrency(selectedOrder.phi_van_chuyen || 0) }}</p>
                  <p class="mb-0 small fw-bold text-danger"><strong>Thực thanh toán:</strong> {{ formatCurrency(selectedOrder.tong_thanh_toan) }}</p>
                </div>
              </div>
            </div>

            <!-- BẢNG MẶT HÀNG TRONG ĐƠN & BỘ GÁN MÃ SERIAL VẬT LÝ (UC09) -->
            <div class="card p-3 border mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-dark mb-0">
                  <i class="bi bi-upc-scan text-primary me-1"></i>
                  Danh Sách Thiết Bị Cần Xuất Kho & Gán Mã Serial/IMEI
                </h6>
                <span class="badge bg-info-subtle text-info small">Định danh vật lý độc bản</span>
              </div>

              <div v-if="!selectedOrder.items || selectedOrder.items.length === 0" class="text-muted small py-2">
                Đang tải chi tiết thiết bị trong đơn...
              </div>

              <div v-else class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                  <thead class="table-light small">
                    <tr>
                      <th>Sản phẩm / Cấu hình</th>
                      <th class="text-center" style="width: 70px;">SL</th>
                      <th>Mã Serial Vật Lý Gán Cho Máy</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in selectedOrder.items" :key="item.id">
                      <td>
                        <strong class="small text-dark">{{ item.ten_san_pham }}</strong>
                        <div class="text-muted" style="font-size: 11px;">Phân loại: {{ item.ten_bien_the || 'Tiêu chuẩn' }}</div>
                      </td>
                      <td class="text-center fw-bold">{{ item.so_luong }}</td>
                      <td>
                        <!-- Trường hợp 1: Nếu đơn ĐÃ ĐƯỢC GÁN SERIAL -->
                        <div v-if="item.danh_sach_serial && item.danh_sach_serial.length > 0" class="d-flex flex-wrap gap-1">
                          <span
                            v-for="(code, cIdx) in item.danh_sach_serial"
                            :key="cIdx"
                            class="badge bg-success font-monospace"
                          >
                            <i class="bi bi-check2-circle me-1"></i>{{ code }}
                          </span>
                        </div>

                        <!-- Trường hợp 2: Nếu CHƯA GÁN SERIAL -> Hiện bộ chọn từ kho -->
                        <div v-else>
                          <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="small text-danger fw-semibold">
                              Cần chọn {{ item.so_luong }} mã serial từ kho:
                            </span>
                            <button
                              class="btn btn-outline-primary btn-xs py-0 px-2"
                              style="font-size: 11px;"
                              @click="autoPickFifo(item)"
                            >
                              <i class="bi bi-magic me-1"></i> Chọn tự động FIFO
                            </button>
                          </div>

                          <div v-if="availableSerialsMap[item.bien_the_id] && availableSerialsMap[item.bien_the_id].length > 0">
                            <div class="d-flex flex-wrap gap-2">
                              <label
                                v-for="ser in availableSerialsMap[item.bien_the_id]"
                                :key="ser.id"
                                class="badge border text-dark font-monospace p-2 cursor-pointer"
                                :class="{ 'bg-primary text-white border-primary': isSerialSelected(item.id, ser.id) }"
                                style="cursor: pointer;"
                                @click="toggleSerialSelection(item.id, ser.id, item.so_luong)"
                              >
                                <input
                                  type="checkbox"
                                  class="form-check-input me-1"
                                  :checked="isSerialSelected(item.id, ser.id)"
                                  @click.stop
                                  @change="toggleSerialSelection(item.id, ser.id, item.so_luong)"
                                />
                                {{ ser.so_serial }}
                              </label>
                            </div>
                          </div>
                          <div v-else class="text-danger small">
                            <i class="bi bi-exclamation-circle me-1"></i> Kho biến thể này hiện không còn mã Serial 'trong_kho'. Vui lòng nhập thêm vào kho!
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Form cập nhật trạng thái -->
            <div class="card p-3 border">
              <h6 class="fw-bold text-dark mb-3"><i class="bi bi-gear-fill me-1 text-primary"></i>Điều Chỉnh Trạng Thái Đơn Hàng</h6>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Trạng Thái Vận Hành Đơn</label>
                  <select v-model="editForm.trang_thai_don_hang" class="form-select form-select-sm">
                    <option value="cho_xac_nhan">1. Chờ xác nhận (Mới tạo)</option>
                    <option value="da_xac_nhan">2. Đã xác nhận đơn</option>
                    <option value="dang_dong_goi">3. Đang đóng gói & Gán Serial</option>
                    <option value="dang_giao">4. Đang giao hàng</option>
                    <option value="giao_thanh_cong">5. Giao thành công (Hoàn tất)</option>
                    <option value="da_huy">6. Đã hủy đơn hàng</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Trạng Thái Thanh Toán</label>
                  <select v-model="editForm.trang_thai_thanh_toan" class="form-select form-select-sm">
                    <option value="chua_thanh_toan">Chưa thanh toán (Chờ COD / Chờ chuyển khoản)</option>
                    <option value="da_thanh_toan">Đã thanh toán (VNPAY / Đã thu tiền)</option>
                    <option value="that_bai">Thanh toán thất bại</option>
                    <option value="hoan_tien">Đã hoàn tiền (Refund)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light d-flex justify-content-between">
            <button type="button" class="btn btn-secondary btn-sm" @click="selectedOrder = null">Đóng</button>
            <div class="d-flex gap-2">
              <button
                v-if="hasUnassignedItems(selectedOrder)"
                type="button"
                class="btn btn-success btn-sm"
                @click="submitAssignSerialsAndFulfill()"
                :disabled="isAssigningSerial"
              >
                <span v-if="isAssigningSerial" class="spinner-border spinner-border-sm me-1"></span>
                <i v-else class="bi bi-box-arrow-up-right me-1"></i>
                Gán Serial & Xuất Kho Giao Hàng
              </button>
              <button
                type="button"
                class="btn btn-primary btn-sm"
                @click="submitUpdateStatus()"
                :disabled="isUpdating"
              >
                <i class="bi bi-save me-1"></i>
                {{ isUpdating ? 'Đang lưu...' : 'Lưu Thay Đổi' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import apiUrl from '../../utils/api'

export default {
  name: 'AdminDashboard',
  data() {
    return {
      isLoading: false,
      isUpdating: false,
      alertMessage: '',
      alertType: 'success',
      searchQuery: '',
      filterStatus: 'ALL',

      // Dữ liệu KPI động gọi từ API
      kpis: {
        tong_doanh_thu: 0,
        tong_don_hang: 0,
        tong_khach_hang: 0,
        tong_san_pham: 0
      },

      // Thống kê phân loại đơn hàng
      orderStats: {
        cho_xac_nhan: 0,
        da_xac_nhan: 0,
        dang_dong_goi: 0,
        dang_giao: 0,
        giao_thanh_cong: 0,
        da_huy: 0
      },

      // Danh sách đơn hàng từ API
      allOrders: [],
      topProducts: [],

      // Đối soát thanh toán PayOS & Giám sát AI (Nguyễn Văn Kỳ)
      allPayments: [],
      paymentGatewayFilter: 'ALL',
      reconcilingId: null,
      paymentSummary: {
        tong_giao_dich: 0,
        thanh_cong: 0,
        dang_cho: 0,
        that_bai: 0,
        tong_tien_payos: 0,
        tong_tien_cong: 0
      },
      aiAnalytics: {
        total_sessions: 0,
        total_messages: 0,
        total_tokens: 0,
        top_recommended_products: []
      },

      // Modal xử lý đơn hàng
      selectedOrder: null,
      editForm: {
        id: null,
        trang_thai_don_hang: 'cho_xac_nhan',
        trang_thai_thanh_toan: 'chua_thanh_toan'
      },

      // Gán Serial Đơn Hàng (UC09)
      availableSerialsMap: {},
      selectedSerialsMap: {}, // { [itemId]: [serialId1, ...] }
      isAssigningSerial: false
    }
  },
  computed: {
    adminName() {
      const stored = localStorage.getItem('admin_user') || localStorage.getItem('customer_user');
      if (stored) {
        try {
          const user = JSON.parse(stored);
          return user.ho_ten || user.name || 'Nguyễn Văn Kỳ';
        } catch (e) {
          return 'Nguyễn Văn Kỳ';
        }
      }
      return 'Nguyễn Văn Kỳ (Lead)';
    },
    adminInitial() {
      return (this.adminName.trim().charAt(0) || 'A').toUpperCase();
    },
    filteredOrders() {
      let orders = this.allOrders;

      // 1. Lọc theo trạng thái tab
      if (this.filterStatus !== 'ALL') {
        orders = orders.filter(o => o.trang_thai_don_hang === this.filterStatus);
      }

      // 2. Lọc theo thanh tìm kiếm
      if (this.searchQuery.trim() !== '') {
        const q = this.searchQuery.toLowerCase();
        orders = orders.filter(o => {
          const ma = String(o.ma_don_hang || o.id || '').toLowerCase();
          const ten = String(o.ten_khach_hang || o.ho_ten_nguoi_nhan || '').toLowerCase();
          const sdt = String(o.so_dien_thoai_nhan || o.sdt_khach_hang || '').toLowerCase();
          return ma.includes(q) || ten.includes(q) || sdt.includes(q);
        });
      }

      return orders;
    },
    filteredPayments() {
      if (this.paymentGatewayFilter === 'ALL') return this.allPayments;
      return this.allPayments.filter(tx => tx.cong_thanh_toan === this.paymentGatewayFilter);
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    // Gọi API lấy toàn bộ dữ liệu thật từ Backend Laravel
    async loadData(isManual = false) {
      this.isLoading = true;

      try {
        // 1. Gọi API Thống kê KPI Dashboard
        const kpiUrl = apiUrl('admin/thong-ke/kpi-dashboard');
        const resKpi = await axios.get(kpiUrl);

        if (resKpi.data && resKpi.data.status && resKpi.data.data) {
          const d = resKpi.data.data;
          this.kpis.tong_doanh_thu = d.tong_doanh_thu || 0;
          this.kpis.tong_don_hang = d.tong_don_hang || 0;
          this.kpis.tong_khach_hang = d.tong_khach_hang || 0;
          this.kpis.tong_san_pham = d.tong_san_pham || 0;

          if (d.don_theo_trang_thai) {
            this.orderStats = { ...this.orderStats, ...d.don_theo_trang_thai };
          }

          if (d.top_san_pham && Array.isArray(d.top_san_pham)) {
            this.topProducts = d.top_san_pham;
          }
        }

        // 2. Gọi API Lấy Danh Sách Đơn Hàng Admin
        const orderUrl = apiUrl('admin/don-hang/get-data');
        const resOrder = await axios.get(orderUrl);

        if (resOrder.data && resOrder.data.status && Array.isArray(resOrder.data.data)) {
          this.allOrders = resOrder.data.data;

          // Cập nhật lại số liệu đơn theo trạng thái nếu chưa có từ KPI
          const stats = {
            cho_xac_nhan: 0,
            da_xac_nhan: 0,
            dang_dong_goi: 0,
            dang_giao: 0,
            giao_thanh_cong: 0,
            da_huy: 0
          };
          this.allOrders.forEach(o => {
            if (stats[o.trang_thai_don_hang] !== undefined) {
              stats[o.trang_thai_don_hang]++;
            }
          });
          this.orderStats = stats;
        }

        // 3. Gọi API Lịch sử & Đối soát Giao dịch Thanh toán (PayOS)
        const payRes = await axios.get(apiUrl('admin/thanh-toan/get-data')).catch(() => null);
        if (payRes && payRes.data && payRes.data.status) {
          this.allPayments = payRes.data.data || [];
          if (payRes.data.summary) {
            this.paymentSummary = { ...this.paymentSummary, ...payRes.data.summary };
          }
        }

        // 4. Gọi API Thống kê Trợ lý AI RAG
        const aiRes = await axios.get(apiUrl('admin/ai/analytics')).catch(() => null);
        if (aiRes && aiRes.data && aiRes.data.status && aiRes.data.data) {
          this.aiAnalytics = { ...this.aiAnalytics, ...aiRes.data.data };
        }

        if (isManual) {
          this.showAlert('Đã đồng bộ dữ liệu thời gian thực thành công từ API Backend!', 'success');
        }
      } catch (error) {
        console.error('Lỗi khi tải dữ liệu từ máy chủ API:', error);
        if (isManual) {
          this.showAlert('Không thể kết nối đến máy chủ backend Laravel (127.0.0.1:8000). Vui lòng kiểm tra php artisan serve!', 'warning');
        }
      } finally {
        this.isLoading = false;
      }
    },

    // Mở Modal chi tiết đơn hàng & Tải Serial khả dụng cho từng thiết bị
    openOrderModal(order) {
      this.selectedOrder = order;
      this.editForm = {
        id: order.id,
        trang_thai_don_hang: order.trang_thai_don_hang || 'cho_xac_nhan',
        trang_thai_thanh_toan: order.trang_thai_thanh_toan || 'chua_thanh_toan'
      };
      this.selectedSerialsMap = {};
      this.availableSerialsMap = {};

      if (order && order.items && order.items.length > 0) {
        order.items.forEach(async (it) => {
          if (it.bien_the_id && (!it.danh_sach_serial || it.danh_sach_serial.length === 0)) {
            try {
              const res = await axios.get(apiUrl(`admin/serial/available-by-variant/${it.bien_the_id}`));
              if (res && res.data && res.data.status) {
                this.availableSerialsMap = {
                  ...this.availableSerialsMap,
                  [it.bien_the_id]: res.data.data || []
                };
              }
            } catch (err) {
              console.error('Lỗi khi tải mã serial khả dụng:', err);
            }
          }
        });
      }
    },


    // ==========================================
    // CÁC HÀM GÁN SERIAL ĐƠN HÀNG XUẤT KHO (UC09)
    // ==========================================
    isSerialSelected(itemId, serialId) {
      const list = this.selectedSerialsMap[itemId] || [];
      return list.includes(serialId);
    },

    toggleSerialSelection(itemId, serialId, maxQty) {
      const current = [...(this.selectedSerialsMap[itemId] || [])];
      const idx = current.indexOf(serialId);
      if (idx > -1) {
        current.splice(idx, 1);
      } else {
        if (current.length >= maxQty) {
          this.showAlert(`Chỉ cần chọn đúng ${maxQty} mã serial cho sản phẩm này!`, 'warning');
          return;
        }
        current.push(serialId);
      }
      this.selectedSerialsMap = {
        ...this.selectedSerialsMap,
        [itemId]: current
      };
    },

    autoPickFifo(item) {
      const available = this.availableSerialsMap[item.bien_the_id] || [];
      if (available.length < item.so_luong) {
        this.showAlert(`Kho chỉ còn ${available.length} mã khả dụng, không đủ ${item.so_luong} mã để chọn tự động!`, 'warning');
        return;
      }
      // FIFO: Chọn `item.so_luong` serial đầu tiên (đã được sắp xếp id ASC)
      const picked = available.slice(0, item.so_luong).map(s => s.id);
      this.selectedSerialsMap = {
        ...this.selectedSerialsMap,
        [item.id]: picked
      };
      this.showAlert(`Đã tự động chọn ${picked.length} mã Serial FIFO cho sản phẩm!`, 'info');
    },

    hasUnassignedItems(order) {
      if (!order || !order.items || order.items.length === 0) return false;
      return order.items.some(it => !it.danh_sach_serial || it.danh_sach_serial.length === 0);
    },

    async submitAssignSerialsAndFulfill() {
      if (!this.selectedOrder) return;
      const items = this.selectedOrder.items || [];
      const assignments = [];

      for (const it of items) {
        if (!it.danh_sach_serial || it.danh_sach_serial.length === 0) {
          const selected = this.selectedSerialsMap[it.id] || [];
          if (selected.length !== it.so_luong) {
            this.showAlert(`Vui lòng chọn đủ ${it.so_luong} mã serial cho "${it.ten_san_pham}"! (Hiện chọn: ${selected.length})`, 'warning');
            return;
          }
          assignments.push({
            chi_tiet_don_hang_id: it.id,
            serial_ids: selected
          });
        }
      }

      if (assignments.length === 0) {
        this.showAlert('Đơn hàng này đã được gán đầy đủ mã Serial!', 'info');
        return;
      }

      this.isAssigningSerial = true;
      try {
        const res = await axios.post(apiUrl('admin/don-hang/gan-serial'), {
          don_hang_id: this.selectedOrder.id,
          assignments: assignments
        });
        if (res.data && res.data.status) {
          this.showAlert(res.data.message || 'Gán mã Serial và xuất kho thành công!', 'success');
          this.selectedOrder = null;
          await this.loadData();
          await this.loadSerialData();
        } else {
          this.showAlert(res.data.message || 'Không thể gán mã serial', 'danger');
        }
      } catch (err) {
        this.showAlert(err?.response?.data?.message || 'Lỗi khi gán mã serial xuất kho!', 'danger');
      } finally {
        this.isAssigningSerial = false;
      }
    },

    // Cập nhật trạng thái đơn hàng gọi API
    async submitUpdateStatus() {
      if (!this.editForm.id) return;
      this.isUpdating = true;

      try {
        const updateUrl = apiUrl('admin/don-hang/change-status');
        const res = await axios.post(updateUrl, this.editForm);

        if (res.data && res.data.status) {
          this.showAlert(res.data.message || 'Cập nhật trạng thái đơn hàng thành công!', 'success');

          // Cập nhật ngay trên mảng local
          const idx = this.allOrders.findIndex(o => o.id === this.editForm.id);
          if (idx !== -1) {
            this.allOrders[idx].trang_thai_don_hang = this.editForm.trang_thai_don_hang;
            this.allOrders[idx].trang_thai_thanh_toan = this.editForm.trang_thai_thanh_toan;
          }

          this.selectedOrder = null;
          // Reload lại số liệu KPI
          this.loadData();
        } else {
          this.showAlert(res.data.message || 'Không thể cập nhật trạng thái đơn hàng', 'danger');
        }
      } catch (error) {
        console.error('Lỗi cập nhật trạng thái:', error);
        this.showAlert('Lỗi khi gửi yêu cầu cập nhật lên máy chủ API!', 'danger');
      } finally {
        this.isUpdating = false;
      }
    },

    // Xuất báo cáo CSV
    exportCSV() {
      if (this.allOrders.length === 0) {
        this.showAlert('Chưa có dữ liệu đơn hàng để xuất báo cáo!', 'warning');
        return;
      }

      let csv = 'Mã Đơn,Khách Hàng,SĐT,Tổng Tiền,Phương Thức,Trạng Thái Đơn,Ngày Đặt\n';
      this.allOrders.forEach(o => {
        csv += `"${o.ma_don_hang || o.id}","${o.ten_khach_hang || o.ho_ten_nguoi_nhan}","${o.so_dien_thoai_nhan || ''}","${o.tong_thanh_toan}","${o.phuong_thuc_thanh_toan}","${o.trang_thai_don_hang}","${o.ngay_dat || ''}"\n`;
      });

      const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `Bao_Cao_Don_Hang_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      this.showAlert('Đã xuất báo cáo danh sách đơn hàng thành công (file CSV)!', 'success');
    },

    // Hiển thị thông báo Toast
    showAlert(msg, type = 'success') {
      this.alertMessage = msg;
      this.alertType = type;
      setTimeout(() => {
        if (this.alertMessage === msg) {
          this.alertMessage = '';
        }
      }, 5000);
    },

    alertFeature(name) {
      this.showAlert(`Chức năng "${name}" đang được kết nối với module chuyên trách của nhóm!`, 'info');
    },

    async reconcilePayOsTx(tx) {
      if (!tx || !tx.id) return;
      this.reconcilingId = tx.id;
      try {
        const res = await axios.post(apiUrl('admin/thanh-toan/doi-soat'), { id: tx.id });
        if (res.data && res.data.status) {
          this.showAlert(`Đối soát PayOS #${tx.ma_giao_dich}: ${res.data.payos_status} — ${res.data.message}`, 'success');
          this.loadData();
        } else {
          this.showAlert((res.data && res.data.message) || 'Không đối soát được giao dịch này.', 'warning');
        }
      } catch (err) {
        this.showAlert(err?.response?.data?.message || 'Giao dịch chưa tồn tại trên cổng PayOS thực tế.', 'warning');
      } finally {
        this.reconcilingId = null;
      }
    },

    scrollTo(id) {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    },

    // Helper formatters
    formatCurrency(val) {
      if (!val || isNaN(val)) return '0 ₫';
      return new Intl.NumberFormat('vi-VN').format(val) + ' ₫';
    },

    formatDate(d) {
      if (!d) return '--/--/----';
      try {
        const date = new Date(d);
        return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
      } catch (e) {
        return d;
      }
    },

    formatPaymentMethod(m) {
      if (!m) return 'COD';
      const map = {
        cod: 'COD (Tiền mặt)',
        vnpay: 'VNPAY QR',
        payos: 'PayOS'
      };
      return map[m.toLowerCase()] || m.toUpperCase();
    },

    formatOrderStatus(st) {
      const map = {
        cho_xac_nhan: 'Chờ xác nhận',
        da_xac_nhan: 'Đã xác nhận',
        dang_dong_goi: 'Đang đóng gói',
        dang_giao: 'Đang giao hàng',
        giao_thanh_cong: 'Giao thành công',
        da_huy: 'Đã hủy đơn'
      };
      return map[st] || st || 'Chờ xử lý';
    },

    getOrderStatusBadge(st) {
      const map = {
        cho_xac_nhan: 'bg-warning text-dark',
        da_xac_nhan: 'bg-info text-dark',
        dang_dong_goi: 'bg-primary',
        dang_giao: 'bg-primary',
        giao_thanh_cong: 'bg-success',
        da_huy: 'bg-danger'
      };
      return map[st] || 'bg-secondary';
    },

    getPaymentBadge(ord) {
      if (ord.trang_thai_thanh_toan === 'da_thanh_toan') {
        return 'bg-success-subtle text-success border border-success-subtle';
      }
      return 'bg-warning-subtle text-warning border border-warning-subtle';
    },

    truncateText(str, len = 25) {
      if (!str) return '';
      return str.length > len ? str.slice(0, len) + '...' : str;
    }
  }
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

/* Sidebar */
.admin-sidebar {
  width: 270px;
  background-color: #0f172a;
  color: #cbd5e1;
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-brand {
  padding: 22px 20px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #1e293b;
}

.brand-title {
  font-size: 15px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.5px;
}

.brand-sub {
  font-size: 11px;
  color: #64748b;
}

.sidebar-menu {
  padding: 16px 12px;
  flex: 1;
}

.menu-label {
  font-size: 10px;
  font-weight: 800;
  color: #64748b;
  letter-spacing: 0.8px;
  margin: 18px 0 6px 10px;
  text-transform: uppercase;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #94a3b8;
  text-decoration: none;
  margin-bottom: 4px;
  transition: all 0.2s ease;
}

.menu-item:hover {
  background-color: #1e293b;
  color: #38bdf8;
}

.menu-item.active {
  background-color: #0284c7;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
}

/* Main Content Area */
.admin-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  min-width: 0;
}

.admin-topbar {
  background: #ffffff;
  padding: 14px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 10;
}

.search-input {
  border: 1px solid #cbd5e1;
  padding: 7px 14px;
  border-radius: 8px;
  font-size: 13px;
  width: 340px;
  outline: none;
  transition: border-color 0.2s;
}

.search-input:focus {
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.avatar-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.admin-content {
  padding: 28px;
}

.kpi-card {
  border-radius: 12px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>
