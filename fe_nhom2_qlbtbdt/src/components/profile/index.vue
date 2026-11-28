<template>
  <div class="profile-page-wrapper">
    <!-- ========================================== -->
    <!-- HEADER TOPBAR & BREADCRUMB -->
    <!-- ========================================== -->
    <header class="profile-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">HỒ SƠ CÁ NHÂN</span>
          </router-link>
          <div class="breadcrumb-trail ms-4 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">Quản lý tài khoản</span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/wishlist" class="btn-top-action me-2">
            <i class="bi bi-heart me-1"></i> Yêu thích
          </router-link>
          <router-link to="/my-orders" class="btn-top-action me-2">
            <i class="bi bi-bag-check me-1"></i> Đơn hàng
          </router-link>
          <router-link to="/cart" class="btn-top-action me-3">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <button class="btn btn-outline-danger btn-sm" @click="handleLogout" v-if="currentUser">
            <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
          </button>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- NỘI DUNG CHÍNH -->
    <!-- ========================================== -->
    <main class="main-width profile-main-content">
      <!-- NẾU CHƯA ĐĂNG NHẬP -->
      <div v-if="!currentUser && !isLoading" class="card border-0 shadow-sm p-5 text-center my-4">
        <div class="mb-3 text-warning">
          <i class="bi bi-shield-lock-fill fs-1"></i>
        </div>
        <h4 class="fw-bold">Vui lòng đăng nhập để xem thông tin hồ sơ</h4>
        <p class="text-muted mb-4">Bạn cần đăng nhập tài khoản khách hàng TechMart để quản lý thông tin, đổi mật khẩu và cập nhật sổ địa chỉ.</p>
        <div class="d-flex justify-content-center gap-2">
          <router-link to="/login?redirect=/profile" class="btn btn-primary px-4">
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập ngay
          </router-link>
          <router-link to="/register" class="btn btn-outline-secondary px-4">
            <i class="bi bi-person-plus me-1"></i> Đăng ký tài khoản
          </router-link>
        </div>
      </div>

      <!-- KHI ĐÃ ĐĂNG NHẬP -->
      <div v-else>
        <!-- KPI METRICS SUMMARY -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-3">
            <div class="card metric-card border-0 shadow-sm p-3 h-100">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-primary-subtle text-primary rounded p-3 me-3">
                  <i class="bi bi-bag-check-fill fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small">Tổng đơn hàng</div>
                  <div class="fs-4 fw-bold text-dark">{{ stats.so_don_hang || 0 }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card metric-card border-0 shadow-sm p-3 h-100">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-success-subtle text-success me-3">
                  <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div class="min-w-0">
                  <div class="text-muted small">Tổng chi tiêu</div>
                  <div class="fs-5 fw-bold text-success text-truncate">{{ formatCurrency(stats.tong_chi_tieu || 0) }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card metric-card border-0 shadow-sm p-3 h-100">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-danger-subtle text-danger rounded p-3 me-3">
                  <i class="bi bi-heart-fill fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small">Sản phẩm yêu thích</div>
                  <div class="fs-4 fw-bold text-danger">{{ stats.so_yeu_thich || 0 }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="card metric-card border-0 shadow-sm p-3 h-100">
              <div class="d-flex align-items-center">
                <div class="metric-icon bg-warning-subtle text-warning rounded p-3 me-3">
                  <i class="bi bi-star-fill fs-4"></i>
                </div>
                <div>
                  <div class="text-muted small">Đánh giá đã gửi</div>
                  <div class="fs-4 fw-bold text-warning">{{ stats.so_danh_gia || 0 }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4 mb-5">
          <!-- CỘT TRÁI: AVATAR & MENU TAB -->
          <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm profile-sidebar-card text-center overflow-hidden mb-3">
              <!-- Cover Banner Gradient -->
              <div class="profile-cover-banner"></div>

              <div class="px-4 pb-4">
                <div class="avatar-wrap mx-auto mb-3 position-relative" style="margin-top: -54px; width: 108px;">
                  <img
                    :src="profileForm.anh_dai_dien || 'https://api.dicebear.com/7.x/bottts/svg?seed=' + (profileForm.email || 'techmart')"
                    class="rounded-circle profile-avatar shadow"
                    style="width: 108px; height: 108px; object-fit: cover; border: 4px solid #ffffff; background: #fff;"
                  />
                  <span class="avatar-online-dot" title="Đang hoạt động"></span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ profileForm.ho_ten || 'Khách hàng' }}</h5>
                <div class="text-muted small mb-2">{{ profileForm.email }}</div>
                <div class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill mx-auto mb-2">
                  <i class="bi bi-shield-check me-1"></i> Thành viên TechMart AI
                </div>
                <hr class="my-3 text-muted opacity-25" />

                <!-- DANH SÁCH TAB -->
                <div class="nav flex-column nav-pills text-start profile-nav gap-2">
                <button
                  class="nav-link text-start d-flex align-items-center"
                  :class="{ active: currentTab === 'info' }"
                  @click="currentTab = 'info'"
                >
                  <i class="bi bi-person-lines-fill me-2 fs-5"></i>
                  <span>Thông tin tài khoản</span>
                </button>
                <button
                  class="nav-link text-start d-flex align-items-center"
                  :class="{ active: currentTab === 'address' }"
                  @click="currentTab = 'address'"
                >
                  <i class="bi bi-geo-alt-fill me-2 fs-5"></i>
                  <span>Sổ địa chỉ nhận hàng</span>
                  <span class="badge bg-secondary ms-auto">{{ addresses.length }}</span>
                </button>
                <button
                  class="nav-link text-start d-flex align-items-center"
                  :class="{ active: currentTab === 'password' }"
                  @click="currentTab = 'password'"
                >
                  <i class="bi bi-key-fill me-2 fs-5"></i>
                  <span>Đổi mật khẩu</span>
                </button>
                <router-link to="/my-orders" class="nav-link text-start d-flex align-items-center text-dark">
                  <i class="bi bi-bag-check me-2 fs-5 text-primary"></i>
                  <span>Lịch sử đơn hàng</span>
                </router-link>
                <router-link to="/wishlist" class="nav-link text-start d-flex align-items-center text-dark">
                  <i class="bi bi-heart me-2 fs-5 text-danger"></i>
                  <span>Sản phẩm yêu thích</span>
                </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- CỘT PHẢI: NỘI DUNG TỪNG TAB -->
          <div class="col-lg-8 col-md-7">
            <!-- TAB 1: THÔNG TIN TÀI KHOẢN -->
            <div v-if="currentTab === 'info'" class="card border-0 shadow-sm p-4">
              <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                <i class="bi bi-person-badge-fill text-primary me-2"></i> THÔNG TIN CÁ NHÂN
              </h5>
              <form @submit.prevent="handleUpdateProfile">
                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input
                      type="text"
                      v-model="profileForm.ho_ten"
                      required
                      class="form-control"
                      placeholder="Nhập họ và tên đầy đủ"
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold">Địa chỉ Email</label>
                    <input
                      type="email"
                      :value="profileForm.email"
                      disabled
                      class="form-control bg-light"
                      title="Không thể đổi email đăng nhập"
                    />
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold">Số điện thoại liên lạc</label>
                    <input
                      type="tel"
                      v-model="profileForm.so_dien_thoai"
                      class="form-control"
                      placeholder="VD: 0987654321"
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold">Link ảnh đại diện (Avatar URL)</label>
                    <input
                      type="text"
                      v-model="profileForm.anh_dai_dien"
                      class="form-control"
                      placeholder="https://..."
                    />
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label small fw-semibold">Địa chỉ thường trú</label>
                  <textarea
                    rows="2"
                    v-model="profileForm.dia_chi"
                    class="form-control"
                    placeholder="Số nhà, đường phố, Quận/Huyện, Tỉnh/Thành..."
                  ></textarea>
                </div>

                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary px-4" :disabled="isSubmitting">
                    <span class="spinner-border spinner-border-sm me-1" v-if="isSubmitting"></span>
                    <i class="bi bi-save me-1" v-else></i> Lưu thay đổi thông tin
                  </button>
                </div>
              </form>
            </div>

            <!-- TAB 2: SỔ ĐỊA CHỈ NHẬN HÀNG -->
            <div v-if="currentTab === 'address'" class="card border-0 shadow-sm p-4">
              <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                  <i class="bi bi-geo-alt-fill text-danger me-2"></i> SỔ ĐỊA CHỈ NHẬN HÀNG
                </h5>
                <button class="btn btn-outline-primary btn-sm" @click="showAddAddressModal = true">
                  <i class="bi bi-plus-circle me-1"></i> Thêm địa chỉ mới
                </button>
              </div>

              <!-- DANH SÁCH ĐỊA CHỈ -->
              <div v-if="addresses.length === 0" class="text-center py-4 text-muted">
                <i class="bi bi-pin-map fs-1 text-muted d-block mb-2"></i>
                <div>Bạn chưa có địa chỉ giao hàng nào trong sổ địa chỉ.</div>
                <button class="btn btn-primary btn-sm mt-3" @click="showAddAddressModal = true">
                  + Thêm địa chỉ đầu tiên
                </button>
              </div>

              <div v-else class="d-flex flex-column gap-3">
                <div
                  v-for="addr in addresses"
                  :key="addr.id"
                  class="card p-3 border rounded-3 position-relative"
                  :class="{ 'border-primary bg-primary-subtle bg-opacity-10': addr.mac_dinh }"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="d-flex align-items-center gap-2 mb-1">
                        <strong class="text-dark">{{ addr.ho_ten_nguoi_nhan }}</strong>
                        <span class="text-muted">|</span>
                        <span class="text-muted fw-semibold">{{ addr.so_dien_thoai }}</span>
                        <span v-if="addr.mac_dinh" class="badge bg-primary ms-2">Mặc định</span>
                      </div>
                      <div class="text-dark small mb-1">{{ addr.dia_chi_chi_tiet }}</div>
                      <div class="text-muted small">
                        {{ [addr.phuong_xa, addr.quan_huyen, addr.tinh_thanh].filter(Boolean).join(', ') }}
                      </div>
                    </div>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="handleDeleteAddress(addr.id)"
                      title="Xóa địa chỉ này"
                    >
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 3: ĐỔI MẬT KHẨU -->
            <div v-if="currentTab === 'password'" class="card border-0 shadow-sm p-4">
              <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                <i class="bi bi-key-fill text-warning me-2"></i> ĐỔI MẬT KHẨU TÀI KHOẢN
              </h5>
              <form @submit.prevent="handleChangePassword">
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                  <input
                    type="password"
                    v-model="passwordForm.mat_khau_cu"
                    required
                    class="form-control"
                    placeholder="Nhập mật khẩu đang dùng"
                  />
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                  <input
                    type="password"
                    v-model="passwordForm.mat_khau_moi"
                    required
                    minlength="6"
                    class="form-control"
                    placeholder="Tối thiểu 6 ký tự"
                  />
                </div>
                <div class="mb-4">
                  <label class="form-label small fw-semibold">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                  <input
                    type="password"
                    v-model="passwordForm.xac_nhan_mat_khau"
                    required
                    class="form-control"
                    placeholder="Nhập lại mật khẩu mới"
                  />
                </div>
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-warning px-4 text-dark fw-bold" :disabled="isSubmitting">
                    <span class="spinner-border spinner-border-sm me-1" v-if="isSubmitting"></span>
                    <i class="bi bi-check-circle me-1" v-else></i> Xác nhận đổi mật khẩu
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- MODAL THÊM ĐỊA CHỈ NHẬN HÀNG -->
    <div
      v-if="showAddAddressModal"
      class="modal-backdrop-custom d-flex align-items-center justify-content-center position-fixed top-0 start-0 w-100 h-100"
      style="background: rgba(15, 23, 42, 0.6); z-index: 9999;"
      @click.self="showAddAddressModal = false"
    >
      <div class="card p-4 shadow-lg border-0" style="max-width: 520px; width: 92%;">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-plus-circle text-primary me-2"></i> Thêm địa chỉ nhận hàng
          </h5>
          <button class="btn-close" @click="showAddAddressModal = false"></button>
        </div>

        <form @submit.prevent="handleAddAddress">
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small fw-semibold">Họ tên người nhận *</label>
              <input type="text" v-model="newAddress.ho_ten_nguoi_nhan" required class="form-control form-control-sm" />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Số điện thoại *</label>
              <input type="tel" v-model="newAddress.so_dien_thoai" required class="form-control form-control-sm" />
            </div>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-4">
              <label class="form-label small fw-semibold">Tỉnh/TP *</label>
              <input type="text" v-model="newAddress.tinh_thanh" placeholder="Đà Nẵng" required class="form-control form-control-sm" />
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Quận/Huyện *</label>
              <input type="text" v-model="newAddress.quan_huyen" placeholder="Hải Châu" required class="form-control form-control-sm" />
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Phường/Xã *</label>
              <input type="text" v-model="newAddress.phuong_xa" placeholder="Hòa Thuận Đông" required class="form-control form-control-sm" />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Địa chỉ chi tiết (Số nhà, Tên đường) *</label>
            <input type="text" v-model="newAddress.dia_chi_chi_tiet" placeholder="VD: 123 Trưng Nữ Vương" required class="form-control form-control-sm" />
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="chkDefault" v-model="newAddress.mac_dinh" />
            <label class="form-check-label small" for="chkDefault">
              Đặt làm địa chỉ nhận hàng mặc định
            </label>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary btn-sm" @click="showAddAddressModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm" :disabled="isSubmitting">
              <span class="spinner-border spinner-border-sm me-1" v-if="isSubmitting"></span>
              Thêm địa chỉ
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- TOAST THÔNG BÁO -->
    <div
      v-if="toastMessage"
      class="toast-alert position-fixed bottom-0 end-0 m-3 p-3 bg-dark text-white rounded shadow-lg"
      style="z-index: 9999;"
    >
      <i class="bi bi-info-circle-fill text-warning me-2"></i>
      <span>{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';
import { confirmDialog } from '@/utils/notify';

export default {
  name: 'CustomerProfileView',
  data() {
    return {
      currentUser: null,
      isLoading: false,
      isSubmitting: false,
      currentTab: 'info',
      stats: {
        so_don_hang: 0,
        tong_chi_tieu: 0,
        so_yeu_thich: 0,
        so_danh_gia: 0
      },
      profileForm: {
        ho_ten: '',
        email: '',
        so_dien_thoai: '',
        anh_dai_dien: '',
        dia_chi: ''
      },
      passwordForm: {
        mat_khau_cu: '',
        mat_khau_moi: '',
        xac_nhan_mat_khau: ''
      },
      addresses: [],
      showAddAddressModal: false,
      newAddress: {
        ho_ten_nguoi_nhan: '',
        so_dien_thoai: '',
        tinh_thanh: 'Đà Nẵng',
        quan_huyen: 'Hải Châu',
        phuong_xa: '',
        dia_chi_chi_tiet: '',
        mac_dinh: false
      },
      toastMessage: '',
      toastTimer: null
    };
  },
  created() {
    this.checkCurrentUser();
    this.loadProfile();
  },
  methods: {
    checkCurrentUser() {
      try {
        const u = localStorage.getItem('customer_user') || localStorage.getItem('techmart_user') || localStorage.getItem('khach_hang_info');
        if (u) this.currentUser = JSON.parse(u);
      } catch (e) {
        console.error(e);
      }
    },
    getAuthHeaders() {
      const token = localStorage.getItem('customer_token') || localStorage.getItem('techmart_token') || localStorage.getItem('token_khach_hang');
      return token ? { Authorization: `Bearer ${token}` } : {};
    },
    async loadProfile() {
      this.isLoading = true;
      try {
        const res = await axios.get(apiUrl('/client/profile'), {
          headers: this.getAuthHeaders()
        });
        if (res.data && res.data.status) {
          const u = res.data.user;
          this.currentUser = u;
          this.profileForm = {
            ho_ten: u.ho_ten || '',
            email: u.email || '',
            so_dien_thoai: u.so_dien_thoai || '',
            anh_dai_dien: u.anh_dai_dien || '',
            dia_chi: u.dia_chi || ''
          };
          this.stats = res.data.stats || this.stats;
          this.addresses = res.data.addresses || [];
          localStorage.setItem('techmart_user', JSON.stringify(u));
          localStorage.setItem('customer_user', JSON.stringify(u));
        }
      } catch (err) {
        console.log('Chưa đăng nhập hoặc token hết hạn');
      } finally {
        this.isLoading = false;
      }
    },
    async handleUpdateProfile() {
      this.isSubmitting = true;
      try {
        const res = await axios.post(apiUrl('/client/profile/update'), this.profileForm, {
          headers: this.getAuthHeaders()
        });
        if (res.data && res.data.status) {
          this.showToast('Cập nhật thông tin cá nhân thành công!');
          this.currentUser = res.data.user;
          localStorage.setItem('techmart_user', JSON.stringify(res.data.user));
        } else {
          this.showToast(res.data ? res.data.message : 'Cập nhật thất bại');
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Có lỗi xảy ra khi lưu thông tin';
        this.showToast(msg);
      } finally {
        this.isSubmitting = false;
      }
    },
    async handleChangePassword() {
      if (this.passwordForm.mat_khau_moi !== this.passwordForm.xac_nhan_mat_khau) {
        this.showToast('Mật khẩu mới xác nhận không khớp!');
        return;
      }
      this.isSubmitting = true;
      try {
        const res = await axios.post(apiUrl('/client/profile/change-password'), this.passwordForm, {
          headers: this.getAuthHeaders()
        });
        if (res.data && res.data.status) {
          this.showToast('Đổi mật khẩu thành công!');
          this.passwordForm = { mat_khau_cu: '', mat_khau_moi: '', xac_nhan_mat_khau: '' };
        } else {
          this.showToast(res.data ? res.data.message : 'Đổi mật khẩu thất bại');
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Đổi mật khẩu thất bại. Vui lòng kiểm tra lại mật khẩu cũ!';
        this.showToast(msg);
      } finally {
        this.isSubmitting = false;
      }
    },
    async handleAddAddress() {
      this.isSubmitting = true;
      try {
        const res = await axios.post(apiUrl('/client/profile/add-address'), this.newAddress, {
          headers: this.getAuthHeaders()
        });
        if (res.data && res.data.status) {
          this.showToast('Thêm địa chỉ giao hàng thành công!');
          this.showAddAddressModal = false;
          this.loadProfile();
          this.newAddress = {
            ho_ten_nguoi_nhan: '',
            so_dien_thoai: '',
            tinh_thanh: 'Đà Nẵng',
            quan_huyen: 'Hải Châu',
            phuong_xa: '',
            dia_chi_chi_tiet: '',
            mac_dinh: false
          };
        } else {
          this.showToast(res.data ? res.data.message : 'Thêm địa chỉ thất bại');
        }
      } catch (err) {
        this.showToast('Có lỗi khi thêm địa chỉ');
      } finally {
        this.isSubmitting = false;
      }
    },
    async handleDeleteAddress(id) {
      const confirmed = await confirmDialog('Bạn có chắc chắn muốn xóa địa chỉ này?', 'Xóa địa chỉ', 'Xóa');
      if (!confirmed) return;
      try {
        const res = await axios.post(apiUrl('/client/profile/delete-address'), { id }, {
          headers: this.getAuthHeaders()
        });
        if (res.data && res.data.status) {
          this.showToast('Đã xóa địa chỉ thành công');
          this.addresses = this.addresses.filter(a => a.id !== id);
        } else {
          this.showToast(res.data ? res.data.message : 'Xóa địa chỉ thất bại');
        }
      } catch (err) {
        this.showToast('Có lỗi khi xóa địa chỉ');
      }
    },
    handleLogout() {
      localStorage.removeItem('customer_token');
      localStorage.removeItem('customer_user');
      localStorage.removeItem('techmart_token');
      localStorage.removeItem('token_khach_hang');
      localStorage.removeItem('techmart_user');
      localStorage.removeItem('khach_hang_info');
      this.currentUser = null;
      this.showToast('Đã đăng xuất tài khoản');
      this.$router.push('/');
    },
    formatCurrency(val) {
      if (!val) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    },
    showToast(msg) {
      this.toastMessage = msg;
      clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => {
        this.toastMessage = '';
      }, 3500);
    }
  }
};
</script>

<style scoped>
.profile-page-wrapper {
  background-color: #f8fafc;
  min-height: 100vh;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 16px;
}

.profile-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 0;
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
  display: flex;
  align-items: center;
  text-decoration: none;
  font-weight: 800;
  font-size: 20px;
  color: #0f172a;
}

.logo-icon {
  background: #0284c7;
  color: #fff;
  border-radius: 8px;
  padding: 4px 8px;
  margin-right: 8px;
}

.badge-sub {
  font-size: 11px;
  background: #e0f2fe;
  color: #0369a1;
  padding: 3px 8px;
  border-radius: 6px;
  font-weight: 700;
}

.breadcrumb-trail {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
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
  font-size: 14px;
  color: #334155;
  text-decoration: none;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.btn-top-action:hover {
  background: #f1f5f9;
}

.profile-nav .nav-link {
  color: #475569;
  padding: 10px 14px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s ease;
}

.profile-nav .nav-link:hover {
  background-color: #f1f5f9;
  color: #0284c7;
}

.profile-nav .nav-link.active {
  background: linear-gradient(135deg, #0284c7, #2563eb);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28);
}

.metric-card {
  border-radius: 16px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
}

.metric-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.profile-sidebar-card,
.card.border-0.shadow-sm {
  border-radius: 16px;
}

.profile-cover-banner {
  height: 88px;
  background: linear-gradient(135deg, #0f172a 0%, #0284c7 65%, #38bdf8 100%);
}

.avatar-online-dot {
  position: absolute;
  bottom: 6px;
  right: 6px;
  width: 16px;
  height: 16px;
  background-color: #22c55e;
  border: 3px solid #ffffff;
  border-radius: 50%;
}

.form-control {
  border-radius: 10px;
  padding: 9px 14px;
  border: 1.5px solid #e2e8f0;
  font-size: 14px;
}

.form-control:focus {
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.14);
}
</style>
