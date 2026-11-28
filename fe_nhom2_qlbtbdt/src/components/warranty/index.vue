<template>
  <div class="warranty-page-wrapper">
    <!-- HEADER TOPBAR & BREADCRUMB -->
    <header class="warranty-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">BẢO HÀNH & ĐỔI TRẢ</span>
          </router-link>
          <div class="breadcrumb-trail ms-4 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">Trung tâm Bảo hành & Đổi trả</span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/my-orders" class="btn-top-action me-2">
            <i class="bi bi-bag-check me-1"></i> Đơn hàng
          </router-link>
          <router-link to="/cart" class="btn-top-action me-2">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <div class="user-info-pill" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || 'Khách hàng' }}</span>
          </div>
          <router-link to="/login?redirect=/warranty" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <main class="main-width warranty-main-content">
      <!-- NAVIGATION TABS -->
      <div class="warranty-nav-tabs card border-0 shadow-sm p-2 mb-4 d-flex flex-row flex-wrap gap-2">
        <button
          type="button"
          class="btn tab-pill-btn flex-grow-1"
          :class="activeTab === 'lookup' ? 'btn-primary shadow-sm' : 'btn-light text-secondary'"
          @click="activeTab = 'lookup'"
        >
          <i class="bi bi-upc-scan me-2"></i>1. Tra cứu Bảo hành Điện tử
        </button>
        <button
          type="button"
          class="btn tab-pill-btn flex-grow-1"
          :class="activeTab === 'request' ? 'btn-primary shadow-sm' : 'btn-light text-secondary'"
          @click="activeTab = 'request'"
        >
          <i class="bi bi-tools me-2"></i>2. Gửi Yêu cầu Bảo hành / Đổi trả
        </button>
        <button
          type="button"
          class="btn tab-pill-btn flex-grow-1"
          :class="activeTab === 'track' ? 'btn-primary shadow-sm' : 'btn-light text-secondary'"
          @click="switchToTrackTab"
        >
          <i class="bi bi-clock-history me-2"></i>3. Theo dõi Tiến độ Phiếu
          <span v-if="myTickets.length > 0" class="badge bg-danger ms-1 rounded-pill">{{ myTickets.length }}</span>
        </button>
      </div>

      <!-- ==================================================== -->
      <!-- TAB 1: TRA CỨU BẢO HÀNH ĐIỆN TỬ (SERIAL/IMEI) -->
      <!-- ==================================================== -->
      <div v-show="activeTab === 'lookup'">
        <div class="warranty-hero-card card border-0 shadow-sm p-4 p-md-5 text-center mb-4">
          <div class="hero-shield-badge mx-auto mb-3">
            <i class="bi bi-shield-check fs-1"></i>
          </div>
          <h1 class="page-title mb-2">TRA CỨU BẢO HÀNH ĐIỆN TỬ TECHMART AI</h1>
          <p class="text-muted mx-auto mb-4" style="max-width: 620px;">
            Nhập mã <strong>Serial / IMEI</strong> in trên vỏ hộp, tem máy hoặc trong chi tiết đơn hàng để kiểm tra thời hạn bảo hành chính hãng và gửi yêu cầu bảo hành / đổi trả trực tuyến.
          </p>

          <form @submit.prevent="handleLookup" class="mx-auto w-100" style="max-width: 640px;">
            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
              <span class="input-group-text bg-white border-0 ps-3">
                <i class="bi bi-upc-scan text-primary fs-4"></i>
              </span>
              <input
                type="text"
                v-model="serialQuery"
                class="form-control border-0 shadow-none"
                placeholder="Nhập mã Serial hoặc IMEI (VD: LG-27GR75Q-003)..."
              />
              <button type="submit" class="btn btn-primary px-4 fw-bold" :disabled="isLoading">
                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-search me-1"></i> Tra cứu ngay
              </button>
            </div>
          </form>

          <!-- MÃ MẪU ĐỂ THỬ NHANH -->
          <div class="mt-3 d-flex flex-wrap justify-content-center align-items-center gap-2 small">
            <span class="text-muted">Thử nhanh với mã mẫu:</span>
            <button
              v-for="sample in ['LG-27GR75Q-003', 'LG-27GR75Q-001', 'ROG-G16-4060-2026-SN04']"
              :key="sample"
              type="button"
              class="btn btn-sm btn-outline-secondary rounded-pill px-3"
              @click="serialQuery = sample; handleLookup();"
            >
              {{ sample }}
            </button>
          </div>
        </div>

        <!-- THÔNG BÁO LỖI KHÔNG TÌM THẤY -->
        <div v-if="errorMessage" class="alert alert-danger border-0 shadow-sm rounded-4 p-4 d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
            <div>
              <strong class="d-block">Không tìm thấy thông tin bảo hành</strong>
              <span class="small">{{ errorMessage }}</span>
            </div>
          </div>
          <button class="btn btn-outline-danger btn-sm fw-semibold" @click="activeTab = 'request'">
            <i class="bi bi-pencil-square me-1"></i> Vẫn gửi yêu cầu thủ công
          </button>
        </div>

        <!-- KẾT QUẢ BẢO HÀNH ĐIỆN TỬ -->
        <div v-if="warrantyResult" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
          <div class="p-4 text-white d-flex flex-wrap justify-content-between align-items-center gap-2" :class="statusHeaderClass">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-patch-check-fill fs-2"></i>
              <div>
                <div class="small opacity-75 text-uppercase fw-semibold">Phiếu bảo hành điện tử chính hãng</div>
                <h4 class="mb-0 fw-bold">{{ warrantyResult.ten_san_pham }}</h4>
              </div>
            </div>
            <span class="badge bg-white text-dark fs-6 px-3 py-2 rounded-pill fw-bold">
              {{ statusLabel }}
            </span>
          </div>

          <div class="p-4">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 h-100">
                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-cpu text-primary me-1"></i> Thông tin thiết bị
                  </h6>
                  <div class="info-row d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Phiên bản / Biến thể</span>
                    <strong class="text-dark small">{{ warrantyResult.ten_bien_the || 'Tiêu chuẩn' }}</strong>
                  </div>
                  <div class="info-row d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Số Serial (S/N)</span>
                    <strong class="text-primary small">{{ warrantyResult.so_serial || '-' }}</strong>
                  </div>
                  <div class="info-row d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Mã IMEI</span>
                    <strong class="text-dark small">{{ warrantyResult.imei || 'Không áp dụng' }}</strong>
                  </div>
                  <div class="info-row d-flex justify-content-between py-2">
                    <span class="text-muted small">Mã đơn hàng liên kết</span>
                    <strong class="text-dark small">{{ warrantyResult.ma_don_hang ? '#' + warrantyResult.ma_don_hang : 'Chưa xuất bán (Trong kho)' }}</strong>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 h-100">
                  <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-calendar-check text-success me-1"></i> Thời hạn & Chính sách bảo hành
                  </h6>
                  <div class="info-row d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Thời gian bảo hành chuẩn</span>
                    <strong class="text-dark small">{{ warrantyResult.thoi_gian_bao_hanh || 24 }} tháng chính hãng</strong>
                  </div>
                  <div class="info-row d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Ngày kích hoạt / Xuất kho</span>
                    <strong class="text-dark small">{{ formatDate(warrantyResult.ngay_xuat_kho) }}</strong>
                  </div>
                  <div class="info-row d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Ngày hết hạn bảo hành</span>
                    <strong class="small" :class="isExpired ? 'text-danger' : 'text-success'">
                      {{ warrantyResult.han_bao_hanh ? formatDate(warrantyResult.han_bao_hanh) : 'Chưa kích hoạt (Tính từ ngày mua)' }}
                    </strong>
                  </div>
                  <div class="info-row d-flex justify-content-between py-2">
                    <span class="text-muted small">Trung tâm tiếp nhận</span>
                    <strong class="text-dark small">TechMart Care - 1900.6789</strong>
                  </div>
                </div>
              </div>
            </div>

            <!-- HÀNH ĐỘNG: GỬI YÊU CẦU BẢO HÀNH / ĐỔI TRẢ CHO THIẾT BỊ NÀY -->
            <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
              <div class="small text-muted">
                <i class="bi bi-info-circle text-primary me-1"></i>
                Thiết bị gặp sự cố kỹ thuật hoặc cần đổi trả? Gửi phiếu tiếp nhận trực tuyến ngay để được KTV hỗ trợ nhanh nhất.
              </div>
              <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-warning fw-bold px-3" @click="prefillRequestFromLookup('doi_tra')">
                  <i class="bi bi-arrow-repeat me-1"></i> Yêu cầu Đổi trả
                </button>
                <button class="btn btn-primary fw-bold px-4" @click="prefillRequestFromLookup('bao_hanh')">
                  <i class="bi bi-tools me-1"></i> Gửi Yêu cầu Bảo hành
                </button>
              </div>
            </div>

            <!-- LỊCH SỬ PHIẾU CỦA SERIAL NÀY (NẾU CÓ) -->
            <div v-if="serialTickets.length > 0" class="mt-4 pt-3 border-top">
              <h6 class="fw-bold text-dark mb-3"><i class="bi bi-receipt me-2 text-primary"></i>Lịch sử tiếp nhận bảo hành của Serial này</h6>
              <div class="d-flex flex-column gap-2">
                <div v-for="t in serialTickets" :key="t.id" class="p-3 bg-light rounded-3 border d-flex flex-wrap justify-content-between align-items-center gap-2">
                  <div>
                    <span class="fw-bold text-primary me-2">#{{ t.ma_phieu }}</span>
                    <span class="badge me-2" :class="t.loai_yeu_cau === 'doi_tra' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis'">
                      {{ t.loai_yeu_cau === 'doi_tra' ? 'Đổi trả' : 'Bảo hành' }}
                    </span>
                    <span class="small text-dark">{{ t.mo_ta_loi }}</span>
                  </div>
                  <span class="badge rounded-pill px-3 py-2" :class="ticketBadgeClass(t.trang_thai)">
                    {{ ticketStatusLabel(t.trang_thai) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==================================================== -->
      <!-- TAB 2: GỬI YÊU CẦU BẢO HÀNH / ĐỔI TRẢ ONLINE -->
      <!-- ==================================================== -->
      <div v-show="activeTab === 'request'">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
          <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4 gap-2">
            <div>
              <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-clipboard2-pulse text-primary me-2"></i>Phiếu Yêu cầu Bảo hành & Đổi trả Trực tuyến
              </h3>
              <p class="text-muted small mb-0">Yêu cầu của bạn sẽ được chuyển thẳng đến bộ phận Kỹ thuật & CSKH TechMart để xử lý ngay.</p>
            </div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
              Phản hồi trong 24h làm việc
            </span>
          </div>

          <!-- ALERT KẾT QUẢ GỬI -->
          <div v-if="submitSuccessMsg" class="alert alert-success border-0 rounded-4 p-4 d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-check-circle-fill fs-2 text-success"></i>
              <div>
                <strong class="d-block fs-6">{{ submitSuccessMsg }}</strong>
                <span class="small">Bạn có thể theo dõi tiến độ KTV kiểm tra theo thời gian thực ở tab Theo dõi.</span>
              </div>
            </div>
            <button type="button" class="btn btn-success fw-bold px-3" @click="switchToTrackTab">
              <i class="bi bi-eye me-1"></i> Theo dõi phiếu ngay
            </button>
          </div>

          <div v-if="submitErrorMsg" class="alert alert-danger border-0 rounded-3 p-3 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle-fill fs-5"></i>
            <span class="small fw-semibold">{{ submitErrorMsg }}</span>
          </div>

          <form @submit.prevent="submitWarrantyRequest">
            <!-- CHỌN LOẠI YÊU CẦU -->
            <div class="mb-4">
              <label class="form-label fw-bold text-dark small text-uppercase">1. Chọn hình thức yêu cầu <span class="text-danger">*</span></label>
              <div class="row g-3">
                <div class="col-md-6">
                  <div
                    class="request-type-card p-3 rounded-3 border cursor-pointer d-flex align-items-center gap-3"
                    :class="requestForm.loai_yeu_cau === 'bao_hanh' ? 'active-type border-primary bg-primary-subtle' : 'bg-white'"
                    @click="requestForm.loai_yeu_cau = 'bao_hanh'"
                  >
                    <div class="type-icon bg-primary text-white rounded-3 p-2">
                      <i class="bi bi-tools fs-4"></i>
                    </div>
                    <div>
                      <div class="fw-bold text-dark">Bảo hành kỹ thuật (Sửa chữa)</div>
                      <div class="text-muted small">Thiết bị lỗi phần cứng, màn hình, nguồn, linh kiện trong thời gian bảo hành.</div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div
                    class="request-type-card p-3 rounded-3 border cursor-pointer d-flex align-items-center gap-3"
                    :class="requestForm.loai_yeu_cau === 'doi_tra' ? 'active-type border-warning bg-warning-subtle' : 'bg-white'"
                    @click="requestForm.loai_yeu_cau = 'doi_tra'"
                  >
                    <div class="type-icon bg-warning text-dark rounded-3 p-2">
                      <i class="bi bi-arrow-repeat fs-4"></i>
                    </div>
                    <div>
                      <div class="fw-bold text-dark">Đổi trả / Đổi mới sản phẩm</div>
                      <div class="text-muted small">Lỗi NSX trong 15 ngày đầu, giao nhầm mẫu, móp méo khi vận chuyển.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <!-- SỐ SERIAL / IMEI -->
              <div class="col-md-6">
                <label class="form-label fw-semibold small text-dark">Số Serial / IMEI thiết bị (nếu có)</label>
                <div class="input-group">
                  <input
                    v-model="requestForm.so_serial"
                    type="text"
                    class="form-control"
                    placeholder="VD: LG-27GR75Q-003 hoặc ROG-G16-4060-2026-SN04"
                  />
                  <button type="button" class="btn btn-outline-primary fw-semibold" @click="quickCheckSerialForForm" :disabled="isCheckingFormSerial">
                    <span v-if="isCheckingFormSerial" class="spinner-border spinner-border-sm"></span>
                    <span v-else><i class="bi bi-magic me-1"></i>Điền tự động</span>
                  </button>
                </div>
                <div class="form-text small" v-if="formSerialHint">{{ formSerialHint }}</div>
              </div>

              <!-- TÊN THIẾT BỊ -->
              <div class="col-md-6">
                <label class="form-label fw-semibold small text-dark">Tên thiết bị / Sản phẩm <span class="text-danger">*</span></label>
                <input
                  v-model="requestForm.ten_thiet_bi"
                  type="text"
                  class="form-control"
                  required
                  placeholder="VD: Laptop ASUS ROG Strix G16 / Màn hình LG 27GR75Q"
                />
              </div>

              <!-- HỌ TÊN KHÁCH HÀNG -->
              <div class="col-md-6">
                <label class="form-label fw-semibold small text-dark">Họ tên khách hàng <span class="text-danger">*</span></label>
                <input
                  v-model="requestForm.ten_khach_hang"
                  type="text"
                  class="form-control"
                  required
                  placeholder="Nhập họ tên người gửi yêu cầu"
                />
              </div>

              <!-- SỐ ĐIỆN THOẠI -->
              <div class="col-md-6">
                <label class="form-label fw-semibold small text-dark">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                <input
                  v-model="requestForm.so_dien_thoai"
                  type="tel"
                  class="form-control"
                  required
                  placeholder="Nhập SĐT để KTV liên hệ & tra cứu tiến độ"
                />
              </div>
            </div>

            <!-- GỢI Ý NHANH MÔ TẢ LỖI -->
            <div class="mb-3">
              <label class="form-label fw-semibold small text-dark">Mô tả chi tiết lỗi / Lý do đổi trả <span class="text-danger">*</span></label>
              <div class="d-flex flex-wrap gap-2 mb-2">
                <button
                  v-for="chip in quickIssueChips"
                  :key="chip"
                  type="button"
                  class="btn btn-sm btn-outline-secondary rounded-pill"
                  @click="appendIssueChip(chip)"
                >
                  + {{ chip }}
                </button>
              </div>
              <textarea
                v-model="requestForm.mo_ta_loi"
                rows="3"
                class="form-control"
                required
                placeholder="Mô tả rõ tình trạng thiết bị gặp phải (VD: Màn hình xuất hiện sọc dọc màu xanh bên phải sau 2 ngày sử dụng...)"
              ></textarea>
            </div>

            <!-- GHI CHÚ THÊM -->
            <div class="mb-4">
              <label class="form-label fw-semibold small text-dark">Ghi chú / Hình thức gửi máy (tùy chọn)</label>
              <input
                v-model="requestForm.ghi_chu"
                type="text"
                class="form-control"
                placeholder="VD: Mang trực tiếp qua cửa hàng Đà Nẵng / Nhờ shipper đến thu hồi..."
              />
            </div>

            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-light px-4" @click="resetRequestForm">Làm mới</button>
              <button type="submit" class="btn btn-primary fw-bold px-5 py-2" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-send-check me-2"></i>Gửi Yêu cầu Ngay
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ==================================================== -->
      <!-- TAB 3: THEO DÕI TIẾN ĐỘ PHIẾU BẢO HÀNH / ĐỔI TRẢ -->
      <!-- ==================================================== -->
      <div v-show="activeTab === 'track'">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
              <h4 class="fw-bold text-dark mb-1"><i class="bi bi-kanban text-primary me-2"></i>Theo dõi Tiến độ Xử lý Bảo hành & Đổi trả</h4>
              <p class="text-muted small mb-0">Tra cứu trạng thái xử lý theo thời gian thực khi Kỹ thuật viên & Admin cập nhật.</p>
            </div>
            <button class="btn btn-outline-primary btn-sm fw-semibold" @click="fetchMyTickets" :disabled="isLoadingTickets">
              <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin-icon': isLoadingTickets }"></i> Làm mới tiến độ
            </button>
          </div>

          <form @submit.prevent="fetchMyTickets" class="input-group mb-2">
            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
            <input
              v-model="trackKeyword"
              type="text"
              class="form-control"
              placeholder="Nhập Số điện thoại, Mã phiếu (WAR-...) hoặc Số Serial để xem tiến độ..."
            />
            <button type="submit" class="btn btn-primary fw-bold px-4" :disabled="isLoadingTickets">
              Tìm phiếu
            </button>
          </form>
        </div>

        <!-- DANH SÁCH PHIẾU THEO DÕI -->
        <div v-if="isLoadingTickets" class="text-center py-5 text-muted">
          <div class="spinner-border text-primary mb-2"></div>
          <div>Đang tải danh sách phiếu bảo hành...</div>
        </div>

        <div v-else-if="myTickets.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
          <i class="bi bi-inbox fs-1 mb-2 opacity-50"></i>
          <h6 class="fw-bold text-dark">Chưa tìm thấy phiếu bảo hành / đổi trả nào</h6>
          <p class="small mb-3">Nhập đúng Số điện thoại bạn đã đăng ký hoặc gửi yêu cầu mới.</p>
          <div>
            <button class="btn btn-primary btn-sm fw-bold px-4" @click="activeTab = 'request'">
              <i class="bi bi-plus-circle me-1"></i> Gửi yêu cầu ngay
            </button>
          </div>
        </div>

        <div v-else class="d-flex flex-column gap-4">
          <div v-for="t in myTickets" :key="t.id" class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="p-3 px-4 bg-light border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
              <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="fw-bold text-primary fs-6">#{{ t.ma_phieu }}</span>
                <span class="badge" :class="t.loai_yeu_cau === 'doi_tra' ? 'bg-warning text-dark' : 'bg-info text-dark'">
                  {{ t.loai_yeu_cau === 'doi_tra' ? 'Yêu cầu Đổi trả' : 'Bảo hành sửa chữa' }}
                </span>
                <span class="text-muted small">• Ngày gửi: {{ formatDate(t.created_at) }}</span>
              </div>
              <span class="badge rounded-pill px-3 py-2 fs-6" :class="ticketBadgeClass(t.trang_thai)">
                {{ ticketStatusLabel(t.trang_thai) }}
              </span>
            </div>

            <div class="p-4">
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <div class="fw-bold text-dark fs-6 mb-1">{{ t.ten_thiet_bi }}</div>
                  <div class="small text-primary fw-semibold" v-if="t.so_serial">Serial/IMEI: {{ t.so_serial }}</div>
                  <div class="small text-muted mt-1">
                    <strong>Khách hàng:</strong> {{ t.ten_khach_hang }} ({{ t.so_dien_thoai }})
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="small text-dark"><strong>Mô tả sự cố:</strong> {{ t.mo_ta_loi }}</div>
                  <div class="small text-muted mt-1">
                    <strong>KTV phụ trách:</strong> {{ t.ky_thuat_vien || 'Chờ phân công' }}
                    <span v-if="t.han_tra"> • <strong>Hẹn trả dự kiến:</strong> {{ formatDate(t.han_tra) }}</span>
                  </div>
                  <div class="small text-info-emphasis mt-1" v-if="t.ghi_chu">
                    <strong>Ghi chú:</strong> {{ t.ghi_chu }}
                  </div>
                </div>
              </div>

              <!-- THANH TIẾN ĐỘ 5 BƯỚC -->
              <div class="stepper-wrapper">
                <div
                  v-for="(step, idx) in statusSteps"
                  :key="step.key"
                  class="stepper-item"
                  :class="{
                    completed: getStepIndex(t.trang_thai) > idx,
                    active: getStepIndex(t.trang_thai) === idx
                  }"
                >
                  <div class="step-counter">
                    <i v-if="getStepIndex(t.trang_thai) > idx" class="bi bi-check-lg"></i>
                    <span v-else>{{ idx + 1 }}</span>
                  </div>
                  <div class="step-name">{{ step.label }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';

export default {
  name: 'WarrantyLookupView',
  data() {
    return {
      activeTab: 'lookup', // 'lookup' | 'request' | 'track'
      serialQuery: '',
      warrantyResult: null,
      serialTickets: [],
      errorMessage: '',
      isLoading: false,
      currentUser: null,

      // Request form state
      requestForm: {
        loai_yeu_cau: 'bao_hanh',
        so_serial: '',
        ten_thiet_bi: '',
        ten_khach_hang: '',
        so_dien_thoai: '',
        mo_ta_loi: '',
        ghi_chu: ''
      },
      isCheckingFormSerial: false,
      formSerialHint: '',
      isSubmitting: false,
      submitSuccessMsg: '',
      submitErrorMsg: '',
      quickIssueChips: [
        'Không lên nguồn / Sập nguồn đột ngột',
        'Màn hình bị sọc, loang mực hoặc điểm chết',
        'Lỗi bàn phím / Touchpad không phản hồi',
        'Quạt kêu to / Quá nhiệt khi chạy nhẹ',
        'Sản phẩm lỗi NSX - Yêu cầu đổi mới'
      ],

      // Tracking state
      trackKeyword: '',
      myTickets: [],
      isLoadingTickets: false,
      statusSteps: [
        { key: 'tiep_nhan', label: 'Đã tiếp nhận' },
        { key: 'dang_kiem_tra', label: 'Đang kiểm tra KT' },
        { key: 'cho_linh_kien', label: 'Chờ linh kiện' },
        { key: 'da_sua_xong', label: 'Đã sửa xong' },
        { key: 'da_tra_hang', label: 'Đã trả / Đổi hàng' }
      ]
    };
  },
  computed: {
    isExpired() {
      if (!this.warrantyResult || !this.warrantyResult.han_bao_hanh) return false;
      return new Date(this.warrantyResult.han_bao_hanh) < new Date();
    },
    statusLabel() {
      if (!this.warrantyResult) return '';
      if (this.warrantyResult.trang_thai_may === 'trong_kho') return 'Chưa kích hoạt (Trong kho)';
      if (this.isExpired) return 'Đã hết hạn bảo hành';
      return 'Đang còn bảo hành';
    },
    statusHeaderClass() {
      if (!this.warrantyResult) return 'bg-primary';
      if (this.warrantyResult.trang_thai_may === 'trong_kho') return 'bg-info text-dark';
      if (this.isExpired) return 'bg-danger';
      return 'bg-success';
    }
  },
  created() {
    this.checkCurrentUser();
    if (this.$route.query.serial) {
      this.serialQuery = String(this.$route.query.serial);
      this.handleLookup();
    }
    if (this.$route.query.tab === 'request') {
      this.activeTab = 'request';
    } else if (this.$route.query.tab === 'track') {
      this.switchToTrackTab();
    }
  },
  methods: {
    checkCurrentUser() {
      try {
        const u = localStorage.getItem('customer_user') || localStorage.getItem('techmart_user') || localStorage.getItem('khach_hang_info');
        if (u) {
          this.currentUser = JSON.parse(u);
          this.requestForm.ten_khach_hang = this.currentUser.ho_ten || '';
          this.requestForm.so_dien_thoai = this.currentUser.so_dien_thoai || '';
          if (this.currentUser.so_dien_thoai) {
            this.trackKeyword = this.currentUser.so_dien_thoai;
          }
        }
      } catch (e) {}
    },
    async handleLookup() {
      const code = (this.serialQuery || '').trim();
      if (!code) {
        this.errorMessage = 'Vui lòng nhập số Serial hoặc mã IMEI để tra cứu!';
        this.warrantyResult = null;
        return;
      }

      this.isLoading = true;
      this.errorMessage = '';
      this.warrantyResult = null;
      this.serialTickets = [];

      try {
        const res = await axios.get(apiUrl('/client/tra-cuu-bao-hanh'), {
          params: { serial: code }
        });
        if (res.data && res.data.status) {
          this.warrantyResult = res.data.data;
          this.serialTickets = res.data.tickets || [];
          // Nếu đang mở action=request từ trang đơn hàng -> chuyển thẳng vào form đã điền sẵn
          if (this.$route.query.action === 'request') {
            this.prefillRequestFromLookup(this.$route.query.type || 'bao_hanh');
          }
        } else {
          this.errorMessage = res.data?.message || 'Không tìm thấy thông tin bảo hành cho mã này.';
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Không tìm thấy thông tin bảo hành cho Serial/IMEI này.';
      } finally {
        this.isLoading = false;
      }
    },
    prefillRequestFromLookup(type = 'bao_hanh') {
      if (!this.warrantyResult) return;
      this.requestForm.loai_yeu_cau = type;
      this.requestForm.so_serial = this.warrantyResult.so_serial || this.warrantyResult.imei || this.serialQuery;
      this.requestForm.ten_thiet_bi =
        this.warrantyResult.ten_san_pham +
        (this.warrantyResult.ten_bien_the ? ` (${this.warrantyResult.ten_bien_the})` : '');
      if (this.warrantyResult.ho_ten_nguoi_nhan && !this.requestForm.ten_khach_hang) {
        this.requestForm.ten_khach_hang = this.warrantyResult.ho_ten_nguoi_nhan;
      }
      if (this.warrantyResult.so_dien_thoai_nhan && !this.requestForm.so_dien_thoai) {
        this.requestForm.so_dien_thoai = this.warrantyResult.so_dien_thoai_nhan;
      }
      this.formSerialHint = 'Đã liên kết tự động với dữ liệu thiết bị chính hãng.';
      this.submitSuccessMsg = '';
      this.submitErrorMsg = '';
      this.activeTab = 'request';
    },
    async quickCheckSerialForForm() {
      const code = (this.requestForm.so_serial || '').trim();
      if (!code) {
        this.formSerialHint = 'Vui lòng nhập số Serial/IMEI trước khi kiểm tra.';
        return;
      }
      this.isCheckingFormSerial = true;
      this.formSerialHint = '';
      try {
        const res = await axios.get(apiUrl('/client/tra-cuu-bao-hanh'), { params: { serial: code } });
        if (res.data?.status && res.data.data) {
          const d = res.data.data;
          this.requestForm.ten_thiet_bi = d.ten_san_pham + (d.ten_bien_the ? ` (${d.ten_bien_the})` : '');
          if (d.ho_ten_nguoi_nhan && !this.requestForm.ten_khach_hang) this.requestForm.ten_khach_hang = d.ho_ten_nguoi_nhan;
          if (d.so_dien_thoai_nhan && !this.requestForm.so_dien_thoai) this.requestForm.so_dien_thoai = d.so_dien_thoai_nhan;
          this.formSerialHint = `✓ Khớp thiết bị: ${d.ten_san_pham}`;
        }
      } catch (e) {
        this.formSerialHint = 'Không tìm thấy Serial trong kho, bạn vẫn có thể nhập tên thiết bị thủ công.';
      } finally {
        this.isCheckingFormSerial = false;
      }
    },
    appendIssueChip(chip) {
      if (!this.requestForm.mo_ta_loi) {
        this.requestForm.mo_ta_loi = chip;
      } else if (!this.requestForm.mo_ta_loi.includes(chip)) {
        this.requestForm.mo_ta_loi += '; ' + chip;
      }
    },
    async submitWarrantyRequest() {
      this.isSubmitting = true;
      this.submitSuccessMsg = '';
      this.submitErrorMsg = '';
      try {
        const res = await axios.post(apiUrl('/client/bao-hanh/gui-yeu-cau'), this.requestForm);
        if (res.data && res.data.status) {
          this.submitSuccessMsg = res.data.message;
          const created = res.data.data;
          this.trackKeyword = created?.ma_phieu || this.requestForm.so_dien_thoai;
          this.requestForm.mo_ta_loi = '';
          this.requestForm.ghi_chu = '';
          await this.fetchMyTickets();
        } else {
          this.submitErrorMsg = res.data?.message || 'Không thể gửi yêu cầu.';
        }
      } catch (err) {
        this.submitErrorMsg = err.response?.data?.message || 'Có lỗi xảy ra khi gửi yêu cầu bảo hành/đổi trả.';
      } finally {
        this.isSubmitting = false;
      }
    },
    resetRequestForm() {
      this.requestForm.so_serial = '';
      this.requestForm.ten_thiet_bi = '';
      this.requestForm.mo_ta_loi = '';
      this.requestForm.ghi_chu = '';
      this.formSerialHint = '';
      this.submitSuccessMsg = '';
      this.submitErrorMsg = '';
    },
    switchToTrackTab() {
      this.activeTab = 'track';
      if (this.trackKeyword) {
        this.fetchMyTickets();
      }
    },
    async fetchMyTickets() {
      const kw = (this.trackKeyword || '').trim();
      if (!kw) return;
      this.isLoadingTickets = true;
      try {
        const res = await axios.get(apiUrl('/client/bao-hanh/lich-su'), {
          params: { keyword: kw }
        });
        if (res.data && res.data.status) {
          this.myTickets = res.data.data || [];
        }
      } catch (e) {
        this.myTickets = [];
      } finally {
        this.isLoadingTickets = false;
      }
    },
    getStepIndex(status) {
      const map = {
        tiep_nhan: 0,
        dang_kiem_tra: 1,
        cho_linh_kien: 2,
        da_sua_xong: 3,
        da_tra_hang: 4
      };
      return map[status] ?? 0;
    },
    ticketStatusLabel(status) {
      const labels = {
        tiep_nhan: 'Đã tiếp nhận',
        dang_kiem_tra: 'Đang kiểm tra KT',
        cho_linh_kien: 'Chờ linh kiện',
        da_sua_xong: 'Đã sửa xong',
        da_tra_hang: 'Đã trả hàng / Đổi mới'
      };
      return labels[status] || status;
    },
    ticketBadgeClass(status) {
      const classes = {
        tiep_nhan: 'bg-secondary text-white',
        dang_kiem_tra: 'bg-primary text-white',
        cho_linh_kien: 'bg-warning text-dark',
        da_sua_xong: 'bg-info text-dark',
        da_tra_hang: 'bg-success text-white'
      };
      return classes[status] || 'bg-secondary text-white';
    },
    formatDate(dateStr) {
      if (!dateStr) return 'Chưa xuất kho';
      return new Date(dateStr).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    }
  }
};
</script>

<style scoped>
.warranty-page-wrapper {
  background-color: #f4f7fb;
  min-height: 100vh;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.warranty-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 14px 0;
  margin-bottom: 24px;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.header-left {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
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
  font-size: 20px;
  color: #0f172a;
}

.logo-icon {
  background: linear-gradient(135deg, #0284c7, #2563eb);
  color: #fff;
  border-radius: 8px;
  width: 34px;
  height: 34px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.badge-sub {
  font-size: 11px;
  background: #e0f2fe;
  color: #0369a1;
  padding: 3px 9px;
  border-radius: 6px;
  font-weight: 700;
}

.breadcrumb-trail {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  padding-left: 12px;
  border-left: 1px solid #e2e8f0;
}

.breadcrumb-item-link {
  color: #64748b;
  text-decoration: none;
}

.breadcrumb-current {
  color: #0f172a;
  font-weight: 600;
}

.btn-top-action {
  display: inline-flex;
  align-items: center;
  font-size: 13.5px;
  color: #334155;
  text-decoration: none;
  font-weight: 600;
  padding: 7px 14px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}

.user-info-pill {
  display: inline-flex;
  align-items: center;
  background: #f1f5f9;
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 13.5px;
  font-weight: 600;
  color: #0f172a;
  border: 1px solid #e2e8f0;
}

.btn-login-link {
  display: inline-flex;
  align-items: center;
  font-size: 13.5px;
  font-weight: 600;
  color: #ffffff;
  background: #0284c7;
  padding: 7px 16px;
  border-radius: 8px;
  text-decoration: none;
}

.warranty-nav-tabs {
  border-radius: 14px;
}

.tab-pill-btn {
  font-weight: 700;
  font-size: 14px;
  padding: 10px 16px;
  border-radius: 10px;
}

.warranty-hero-card {
  border-radius: 18px;
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
}

.hero-shield-badge {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, #e0f2fe, #dbeafe);
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
}

.cursor-pointer {
  cursor: pointer;
}

.request-type-card {
  transition: all 0.2s ease;
}

.request-type-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

/* 5-STEP PROGRESS BAR */
.stepper-wrapper {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.stepper-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.stepper-item::before {
  position: absolute;
  content: '';
  border-bottom: 3px solid #e2e8f0;
  width: 100%;
  top: 16px;
  left: -50%;
  z-index: 2;
}

.stepper-item:first-child::before {
  content: none;
}

.stepper-item .step-counter {
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #64748b;
  font-weight: 700;
  font-size: 13px;
  margin-bottom: 8px;
}

.stepper-item.active .step-counter {
  background: #2563eb;
  color: #ffffff;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
}

.stepper-item.completed .step-counter {
  background: #10b981;
  color: #ffffff;
}

.stepper-item.completed::before,
.stepper-item.active::before {
  border-bottom-color: #10b981;
}

.stepper-item .step-name {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-align: center;
}

.stepper-item.active .step-name {
  color: #2563eb;
  font-weight: 700;
}

.stepper-item.completed .step-name {
  color: #059669;
}

.spin-icon {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
