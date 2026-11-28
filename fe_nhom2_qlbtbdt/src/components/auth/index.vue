<template>
  <div class="auth-page-container">
    <!-- Header đơn giản với Logo & Nút về trang chủ -->
    <header class="auth-header">
      <div class="auth-header-inner main-width">
        <router-link to="/" class="auth-logo">
          <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
          <span class="logo-text">TECHMART</span>
          <span class="logo-sub">AI PLATFORM</span>
        </router-link>
        <router-link to="/" class="btn-back-home">
          <i class="bi bi-arrow-left me-1"></i> Quay về Trang chủ
        </router-link>
      </div>
    </header>

    <!-- Main Auth Card -->
    <main class="auth-main-content">
      <div class="auth-box-wrapper">
        <!-- Khối giới thiệu đặc quyền thành viên (Bên trái trên màn lớn) -->
        <div class="auth-perks-sidebar">
          <div class="perks-badge">
            <i class="bi bi-shield-lock-fill me-1"></i> BẢO MẬT 100% THEO CHUẨN SSL
          </div>
          <h2 class="perks-title">Gia nhập cộng đồng công nghệ TechMart AI</h2>
          <p class="perks-desc">
            Trải nghiệm mua sắm thiết bị điện tử chính hãng, bảo hành điện tử theo Serial/IMEI và nhận trợ lý AI tư vấn build cấu hình máy.
          </p>

          <ul class="perks-list">
            <li>
              <div class="perk-icon"><i class="bi bi-gift-fill"></i></div>
              <div class="perk-text">
                <strong>Voucher chào mừng 500.000đ</strong>
                <span>Tặng ngay mã TECH500 vào ví khi tạo tài khoản</span>
              </div>
            </li>
            <li>
              <div class="perk-icon"><i class="bi bi-truck"></i></div>
              <div class="perk-text">
                <strong>Giao siêu tốc nội thành 2 Giờ</strong>
                <span>Miễn phí giao hàng hỏa tốc trong 2h cho thành viên</span>
              </div>
            </li>
            <li>
              <div class="perk-icon"><i class="bi bi-cpu"></i></div>
              <div class="perk-text">
                <strong>Trợ lý AI tư vấn 24/7</strong>
                <span>Gợi ý cấu hình PC/Laptop tối ưu ngân sách</span>
              </div>
            </li>
            <li>
              <div class="perk-icon"><i class="bi bi-shield-check"></i></div>
              <div class="perk-text">
                <strong>Bảo hành chính hãng Serial/IMEI</strong>
                <span>Tra cứu trực tuyến, 1 đổi 1 trong 30 ngày</span>
              </div>
            </li>
          </ul>
        </div>

        <!-- Khối Form Đăng Nhập / Đăng Ký (Bên phải) -->
        <div class="auth-form-card">
          <!-- Switch Tabs -->
          <div class="auth-tabs-nav">
            <button
              class="auth-tab-btn"
              :class="{ active: currentTab === 'login' }"
              @click="switchTab('login')"
            >
              <i class="bi bi-box-arrow-in-right me-2"></i>ĐĂNG NHẬP
            </button>
            <button
              class="auth-tab-btn"
              :class="{ active: currentTab === 'register' }"
              @click="switchTab('register')"
            >
              <i class="bi bi-person-plus-fill me-2"></i>ĐĂNG KÝ
            </button>
          </div>

          <!-- Alert thông báo lỗi / thành công -->
          <div v-if="alertMessage" class="auth-alert" :class="'alert-' + alertType">
            <i :class="alertType === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-triangle-fill'" class="me-2 fs-5"></i>
            <span>{{ alertMessage }}</span>
          </div>

          <!-- ========================================== -->
          <!-- 1. TAB ĐĂNG NHẬP (LOGIN) -->
          <!-- ========================================== -->
          <form v-if="currentTab === 'login'" @submit.prevent="handleLogin" class="auth-form">
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-envelope-fill me-1 text-primary"></i> Email hoặc Số điện thoại <span class="required">*</span>
              </label>
              <div class="input-with-icon">
                <input
                  type="text"
                  v-model.trim="loginForm.email"
                  class="form-control"
                  placeholder="Nhập email (ví dụ: user@gmail.com) hoặc số điện thoại"
                  required
                />
                <i class="bi bi-person input-icon"></i>
              </div>
            </div>

            <div class="form-group">
              <div class="label-row">
                <label class="form-label">
                  <i class="bi bi-lock-fill me-1 text-primary"></i> Mật khẩu <span class="required">*</span>
                </label>
                <a href="javascript:void(0)" class="forgot-link" @click="showForgotAlert">Quên mật khẩu?</a>
              </div>
              <div class="input-with-icon">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model="loginForm.password"
                  class="form-control"
                  placeholder="Nhập mật khẩu của bạn"
                  required
                />
                <button type="button" class="btn-toggle-eye" @click="showPassword = !showPassword" tabindex="-1">
                  <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                </button>
              </div>
            </div>

            <div class="form-options">
              <label class="custom-checkbox">
                <input type="checkbox" v-model="loginForm.remember" />
                <span class="checkbox-mark"></span>
                <span class="checkbox-label">Ghi nhớ đăng nhập trên thiết bị này</span>
              </label>
            </div>

            <button type="submit" class="btn-auth-submit" :disabled="isLoading">
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
              <span v-if="!isLoading">ĐĂNG NHẬP VÀO TECHMART</span>
              <span v-else>ĐANG XÁC THỰC...</span>
              <i class="bi bi-arrow-right ms-2" v-if="!isLoading"></i>
            </button>

            <div class="form-bottom-switch">
              <span>Chưa có tài khoản TechMart?</span>
              <a href="javascript:void(0)" @click="switchTab('register')" class="switch-link">Đăng ký thành viên mới ngay</a>
            </div>
          </form>

          <!-- ========================================== -->
          <!-- 2. TAB ĐĂNG KÝ (REGISTER) -->
          <!-- ========================================== -->
          <form v-else @submit.prevent="handleRegister" class="auth-form" novalidate>
            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-person-badge-fill me-1 text-primary"></i> Họ và tên <span class="required">*</span>
              </label>
              <div class="input-with-icon">
                <input
                  type="text"
                  v-model.trim="registerForm.ho_ten"
                  class="form-control"
                  placeholder="Nhập đầy đủ họ và tên của bạn"
                  required
                />
                <i class="bi bi-person input-icon"></i>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-envelope-fill me-1 text-primary"></i> Địa chỉ Email <span class="required">*</span>
              </label>
              <div class="input-with-icon">
                <input
                  type="email"
                  v-model.trim="registerForm.email"
                  class="form-control"
                  placeholder="name@example.com"
                  required
                />
                <i class="bi bi-envelope input-icon"></i>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-telephone-fill me-1 text-primary"></i> Số điện thoại <span class="required">*</span>
              </label>
              <div class="input-with-icon">
                <input
                  type="tel"
                  v-model.trim="registerForm.so_dien_thoai"
                  class="form-control"
                  placeholder="Ví dụ: 0905123456"
                  required
                />
                <i class="bi bi-telephone input-icon"></i>
              </div>
            </div>

            <div class="form-row-2cols">
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-lock-fill me-1 text-primary"></i> Mật khẩu <span class="required">*</span>
                </label>
                <div class="input-with-icon">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="registerForm.password"
                    class="form-control"
                    placeholder="Tối thiểu 6 ký tự"
                    required
                    minlength="6"
                  />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-shield-lock-fill me-1 text-primary"></i> Xác nhận mật khẩu <span class="required">*</span>
                </label>
                <div class="input-with-icon">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="registerForm.password_confirmation"
                    class="form-control"
                    placeholder="Nhập lại mật khẩu"
                    required
                  />
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">
                <i class="bi bi-geo-alt-fill me-1 text-primary"></i> Địa chỉ giao hàng mặc định
              </label>
              <input
                type="text"
                v-model.trim="registerForm.dia_chi"
                class="form-control"
                placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố"
              />
            </div>

            <div class="form-options">
              <label class="custom-checkbox">
                <input type="checkbox" v-model="registerForm.agreeTerms" required />
                <span class="checkbox-mark"></span>
                <span class="checkbox-label">
                  Tôi đồng ý với <a href="#" class="terms-link">Điều khoản dịch vụ</a> và <a href="#" class="terms-link">Chính sách bảo mật</a> của TechMart.
                </span>
              </label>
            </div>

            <button type="submit" class="btn-auth-submit btn-register-submit" :disabled="isLoading">
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
              <span v-if="!isLoading">TẠO TÀI KHOẢN & NHẬN ƯU ĐÃI</span>
              <span v-else>ĐANG XỬ LÝ...</span>
              <i class="bi bi-gift-fill ms-2" v-if="!isLoading"></i>
            </button>

            <div class="form-bottom-switch">
              <span>Đã có tài khoản TechMart?</span>
              <a href="javascript:void(0)" @click="switchTab('login')" class="switch-link">Đăng nhập ngay</a>
            </div>
          </form>
        </div>
      </div>
    </main>

    <!-- Footer bản quyền -->
    <footer class="auth-footer text-center">
      <p class="mb-0">© 2026 TechMart AI - Nền tảng thương mại điện tử thiết bị công nghệ hàng đầu Việt Nam.</p>
      <p class="hotline-text">Hotline hỗ trợ kỹ thuật & đặt hàng: <strong>1900.6789</strong> (8:00 - 21:30)</p>
    </footer>
  </div>
</template>

<script>
import axios from 'axios'
import { toast } from 'vue3-toastify'
import apiUrl from '../../utils/api'

export default {
  name: 'ClientAuthView',
  data() {
    return {
      currentTab: 'login', // 'login' hoặc 'register'
      isLoading: false,
      showPassword: false,
      alertMessage: '',
      alertType: 'danger', // 'danger' hoặc 'success'

      loginForm: {
        email: '',
        password: '',
        remember: true
      },

      registerForm: {
        ho_ten: '',
        email: '',
        so_dien_thoai: '',
        password: '',
        password_confirmation: '',
        dia_chi: 'Đà Nẵng, Việt Nam',
        agreeTerms: false
      }
    }
  },
  mounted() {
    // Đọc query hoặc meta route để mở đúng Tab
    if (this.$route.query.tab === 'register' || this.$route.path === '/register') {
      this.currentTab = 'register';
    } else {
      this.currentTab = 'login';
    }

    // Nếu đã đăng nhập rồi thì redirect về redirect path hoặc home
    const token = localStorage.getItem('customer_token');
    if (token) {
      const redirect = this.$route.query.redirect || '/';
      this.$router.push(redirect);
    }
  },
  methods: {
    switchTab(tab) {
      this.currentTab = tab;
      this.alertMessage = '';
      if (tab === 'register') {
        window.history.replaceState(null, '', '/register');
      } else {
        window.history.replaceState(null, '', '/login');
      }
    },
    fillDemoAccount() {
      this.currentTab = 'login';
      this.loginForm.email = 'user@gmail.com';
      this.loginForm.password = '12345678';
      this.alertType = 'success';
      this.alertMessage = 'Đã điền tài khoản mẫu! Đang tiến hành đăng nhập...';
      setTimeout(() => {
        this.handleLogin();
      }, 500);
    },
    async handleLogin() {
      this.isLoading = true;
      this.alertMessage = '';

      const sessionId = localStorage.getItem('techmart_session_id');

      try {
        const res = await axios.post(apiUrl('client/dang-nhap'), {
          email: this.loginForm.email,
          password: this.loginForm.password,
          session_id: sessionId
        });

        if (res.data && res.data.status) {
          this.alertType = 'success';
          this.alertMessage = res.data.message || 'Đăng nhập thành công!';
          toast.success(this.alertMessage);

          // Lưu token & user info
          localStorage.setItem('customer_token', res.data.token);
          localStorage.setItem('customer_user', JSON.stringify(res.data.user));
          localStorage.setItem('user_id', res.data.user.id);
          localStorage.setItem('user_name', res.data.user.ho_ten);

          // Phát event đồng bộ cho các component khác
          window.dispatchEvent(new Event('auth-change'));

          // Chuyển hướng
          const redirectPath = this.$route.query.redirect || '/';
          setTimeout(() => {
            this.$router.push(redirectPath);
          }, 800);
        } else {
          this.alertType = 'danger';
          this.alertMessage = res.data ? res.data.message : 'Đăng nhập không thành công!';
          toast.error(this.alertMessage);
        }
      } catch (err) {
        console.error('Lỗi đăng nhập:', err);
        this.alertType = 'danger';
        if (err.response && err.response.data && err.response.data.message) {
          this.alertMessage = err.response.data.message;
        } else {
          this.alertMessage = 'Lỗi kết nối máy chủ! Vui lòng thử lại sau.';
        }
        toast.error(this.alertMessage);
      } finally {
        this.isLoading = false;
      }
    },
    async handleRegister() {
      this.alertMessage = '';

      if (!this.registerForm.ho_ten) {
        this.alertType = 'danger';
        this.alertMessage = 'Vui lòng nhập họ và tên!';
        toast.warning(this.alertMessage);
        return;
      }

      if (!this.registerForm.email || !/^\S+@\S+\.\S+$/.test(this.registerForm.email)) {
        this.alertType = 'danger';
        this.alertMessage = 'Vui lòng nhập địa chỉ email hợp lệ!';
        toast.warning(this.alertMessage);
        return;
      }

      if (!this.registerForm.so_dien_thoai) {
        this.alertType = 'danger';
        this.alertMessage = 'Vui lòng nhập số điện thoại bắt buộc!';
        toast.warning(this.alertMessage);
        return;
      }

      if (!this.registerForm.password || this.registerForm.password.length < 6) {
        this.alertType = 'danger';
        this.alertMessage = 'Mật khẩu phải có ít nhất 6 ký tự!';
        toast.warning(this.alertMessage);
        return;
      }

      if (this.registerForm.password !== this.registerForm.password_confirmation) {
        this.alertType = 'danger';
        this.alertMessage = 'Mật khẩu xác nhận không khớp, vui lòng nhập lại!';
        toast.error(this.alertMessage);
        return;
      }

      if (!this.registerForm.agreeTerms) {
        this.alertType = 'danger';
        this.alertMessage = 'Bạn phải tích đồng ý với Điều khoản dịch vụ và Chính sách bảo mật của TechMart!';
        toast.warning(this.alertMessage);
        return;
      }

      this.isLoading = true;
      const sessionId = localStorage.getItem('techmart_session_id');

      try {
        const res = await axios.post(apiUrl('client/dang-ky'), {
          ho_ten: this.registerForm.ho_ten,
          email: this.registerForm.email,
          so_dien_thoai: this.registerForm.so_dien_thoai,
          password: this.registerForm.password,
          dia_chi: this.registerForm.dia_chi,
          session_id: sessionId
        });

        if (res.data && res.data.status) {
          const registeredEmail = this.registerForm.email;
          toast.success('Đăng ký tài khoản thành công! Vui lòng đăng nhập để tiếp tục.');

          // Không đăng nhập thẳng — chuyển sang Tab Đăng nhập và điền sẵn email
          this.switchTab('login');
          this.loginForm.email = registeredEmail;
          this.loginForm.password = '';
          this.alertType = 'success';
          this.alertMessage = 'Tạo tài khoản thành công! Vui lòng nhập mật khẩu để đăng nhập.';
        } else {
          this.alertType = 'danger';
          this.alertMessage = res.data ? res.data.message : 'Đăng ký thất bại!';
          toast.error(this.alertMessage);
        }
      } catch (err) {
        console.error('Lỗi đăng ký:', err);
        this.alertType = 'danger';
        if (err.response && err.response.data && err.response.data.message) {
          this.alertMessage = err.response.data.message;
        } else {
          this.alertMessage = 'Đăng ký không thành công, vui lòng kiểm tra lại thông tin!';
        }
        toast.error(this.alertMessage);
      } finally {
        this.isLoading = false;
      }
    },
    showForgotAlert() {
      toast.info('Vui lòng liên hệ Tổng đài CSKH TechMart: 1900.6789 hoặc email support@techmart.vn để được hỗ trợ đặt lại mật khẩu!');
    }
  }
}
</script>

<style scoped>
/* ==========================================
   CSS VIEW ĐĂNG NHẬP / ĐĂNG KÝ TECHMART AI
   ========================================== */
.auth-page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(135deg, #0b1120 0%, #0f172a 40%, #1e1b4b 100%);
  color: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

/* Header */
.auth-header {
  background: rgba(15, 23, 42, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding: 14px 0;
}

.auth-header-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.auth-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}

.logo-icon {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #2563eb, #3b82f6);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 18px;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}

.logo-text {
  font-size: 20px;
  font-weight: 900;
  color: #ffffff;
  letter-spacing: 0.5px;
}

.logo-sub {
  font-size: 11px;
  font-weight: 700;
  background: rgba(37, 99, 235, 0.25);
  color: #60a5fa;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid rgba(96, 165, 250, 0.3);
}

.btn-back-home {
  color: #94a3b8;
  font-size: 13.5px;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s;
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.03);
}

.btn-back-home:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
}

/* Main Content Layout */
.auth-main-content {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 16px;
}

.auth-box-wrapper {
  max-width: 1080px;
  width: 100%;
  display: grid;
  grid-template-columns: 1fr 1.1fr;
  gap: 32px;
  background: rgba(15, 23, 42, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 24px;
  box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
  overflow: hidden;
  backdrop-filter: blur(20px);
}

/* Perks Sidebar (Left) */
.auth-perks-sidebar {
  padding: 40px 36px;
  background: linear-gradient(180deg, rgba(30, 41, 59, 0.6) 0%, rgba(15, 23, 42, 0.9) 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  flex-direction: column;
}

.perks-badge {
  display: inline-flex;
  align-items: center;
  background: rgba(16, 185, 129, 0.15);
  color: #34d399;
  border: 1px solid rgba(52, 211, 153, 0.3);
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  margin-bottom: 16px;
  width: fit-content;
}

.perks-title {
  font-size: 24px;
  font-weight: 800;
  color: #ffffff;
  line-height: 1.3;
  margin-bottom: 12px;
}

.perks-desc {
  font-size: 13.5px;
  color: #94a3b8;
  line-height: 1.6;
  margin-bottom: 24px;
}

.perks-list {
  list-style: none;
  padding: 0;
  margin: 0 0 28px 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.perks-list li {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.perk-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: rgba(37, 99, 235, 0.15);
  border: 1px solid rgba(37, 99, 235, 0.3);
  color: #60a5fa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.perk-text strong {
  display: block;
  font-size: 13.5px;
  color: #f1f5f9;
  margin-bottom: 2px;
}

.perk-text span {
  font-size: 12px;
  color: #94a3b8;
  line-height: 1.4;
}

/* Demo box */
.demo-account-box {
  margin-top: auto;
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid rgba(245, 158, 11, 0.3);
  border-radius: 12px;
  padding: 14px 16px;
}

.demo-box-head {
  font-size: 12px;
  color: #fbbf24;
  margin-bottom: 4px;
}

.demo-box-sub {
  font-size: 11.5px;
  color: #94a3b8;
  margin-bottom: 8px;
}

.demo-credentials {
  display: flex;
  gap: 16px;
  font-size: 12px;
  color: #e2e8f0;
  margin-bottom: 10px;
  background: rgba(15, 23, 42, 0.6);
  padding: 6px 10px;
  border-radius: 6px;
}

.demo-credentials strong {
  color: #38bdf8;
}

.btn-quick-fill {
  width: 100%;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #ffffff;
  border: none;
  border-radius: 6px;
  padding: 7px 12px;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
}

.btn-quick-fill:hover {
  background: linear-gradient(135deg, #d97706, #b45309);
  transform: translateY(-1px);
}

/* Form Card (Right) */
.auth-form-card {
  padding: 40px 38px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* Tabs */
.auth-tabs-nav {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  background: rgba(30, 41, 59, 0.8);
  padding: 5px;
  border-radius: 12px;
  margin-bottom: 24px;
}

.auth-tab-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 13.5px;
  font-weight: 700;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-tab-btn.active {
  background: #2563eb;
  color: #ffffff;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
}

/* Alerts */
.auth-alert {
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  font-size: 13px;
  line-height: 1.4;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #fca5a5;
}

.alert-success {
  background: rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.35);
  color: #6ee7b7;
}

/* Form inputs */
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-row-2cols {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-label {
  font-size: 12.5px;
  font-weight: 600;
  color: #cbd5e1;
  display: flex;
  align-items: center;
  margin: 0;
}

.required {
  color: #ef4444;
  margin-left: 3px;
}

.label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot-link {
  font-size: 12px;
  color: #60a5fa;
  text-decoration: none;
}

.forgot-link:hover {
  text-decoration: underline;
}

.input-with-icon {
  position: relative;
  display: flex;
  align-items: center;
}

.form-control {
  width: 100%;
  background: rgba(30, 41, 59, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 10px;
  padding: 11px 14px 11px 38px;
  color: #ffffff;
  font-size: 13.5px;
  transition: all 0.2s ease;
  outline: none;
}

.form-control:focus {
  border-color: #3b82f6;
  background: rgba(30, 41, 59, 0.95);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
}

.form-control::placeholder {
  color: #64748b;
  font-size: 13px;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #64748b;
  font-size: 16px;
  pointer-events: none;
}

.btn-toggle-eye {
  position: absolute;
  right: 12px;
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0;
  font-size: 16px;
}

.btn-toggle-eye:hover {
  color: #94a3b8;
}

/* Checkbox */
.form-options {
  display: flex;
  align-items: center;
  margin: 2px 0 6px 0;
}

.custom-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  cursor: pointer;
  font-size: 12.5px;
  color: #94a3b8;
  user-select: none;
}

.custom-checkbox input {
  display: none;
}

.checkbox-mark {
  width: 17px;
  height: 17px;
  border-radius: 4px;
  border: 1.5px solid #475569;
  background: rgba(30, 41, 59, 0.6);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
  transition: all 0.2s ease;
}

.custom-checkbox input:checked + .checkbox-mark {
  background: #2563eb;
  border-color: #2563eb;
}

.custom-checkbox input:checked + .checkbox-mark::after {
  content: '✓';
  color: #ffffff;
  font-size: 11px;
  font-weight: 800;
}

.terms-link {
  color: #60a5fa;
  text-decoration: underline;
}

/* Submit Buttons */
.btn-auth-submit {
  width: 100%;
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff;
  border: none;
  border-radius: 10px;
  padding: 13px;
  font-size: 14.5px;
  font-weight: 800;
  letter-spacing: 0.5px;
  cursor: pointer;
  box-shadow: 0 8px 24px -4px rgba(37, 99, 235, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
  margin-top: 6px;
}

.btn-auth-submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  transform: translateY(-2px);
  box-shadow: 0 12px 28px -4px rgba(37, 99, 235, 0.6);
}

.btn-auth-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-register-submit {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 8px 24px -4px rgba(16, 185, 129, 0.4);
}

.btn-register-submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  box-shadow: 0 12px 28px -4px rgba(16, 185, 129, 0.5);
}

/* Bottom switch */
.form-bottom-switch {
  text-align: center;
  font-size: 13px;
  color: #94a3b8;
  margin-top: 10px;
}

.switch-link {
  color: #60a5fa;
  font-weight: 700;
  text-decoration: none;
  margin-left: 6px;
}

.switch-link:hover {
  text-decoration: underline;
}

/* Footer */
.auth-footer {
  padding: 20px 0;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  font-size: 12px;
  color: #64748b;
}

.hotline-text {
  color: #94a3b8;
  margin-top: 4px;
}

.hotline-text strong {
  color: #ef4444;
}

/* Responsive */
@media (max-width: 900px) {
  .auth-box-wrapper {
    grid-template-columns: 1fr;
  }
  .auth-perks-sidebar {
    display: none;
  }
  .auth-form-card {
    padding: 30px 20px;
  }
}

@media (max-width: 480px) {
  .form-row-2cols {
    grid-template-columns: 1fr;
  }
}
</style>
