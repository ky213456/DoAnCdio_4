<template>
  <div>
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
      <div>
        <div class="text-primary fw-bold small mb-1">QUẢN TRỊ KHUYẾN MÃI & TƯƠNG TÁC KHÁCH HÀNG</div>
        <h2 class="fw-bold text-dark mb-1">Quản lý Voucher & Bình luận</h2>
        <p class="text-muted small mb-0">Tạo mã giảm giá chiến dịch và kiểm duyệt bình luận đánh giá sản phẩm từ khách hàng.</p>
      </div>
      <div class="d-flex gap-2">
        <div class="btn-group bg-white p-1 rounded-3 border shadow-sm">
          <button class="btn btn-sm fw-semibold" :class="subTab === 'vouchers' ? 'btn-primary' : 'btn-light'" @click="subTab = 'vouchers'">
            <i class="bi bi-ticket-perforated me-1"></i> Voucher ({{ vouchers.length }})
          </button>
          <button class="btn btn-sm fw-semibold" :class="subTab === 'reviews' ? 'btn-primary' : 'btn-light'" @click="subTab = 'reviews'">
            <i class="bi bi-chat-square-quote me-1"></i> Bình luận ({{ reviews.length }})
          </button>
        </div>
        <button v-if="subTab === 'vouchers'" class="btn btn-primary btn-sm px-3 fw-bold" @click="openVoucherModal(null)">
          <i class="bi bi-plus-lg me-1"></i> Tạo Voucher mới
        </button>
      </div>
    </div>

    <!-- SUB-TAB 1: QUẢN LÝ VOUCHER (UC13) -->
    <div v-if="subTab === 'vouchers'" class="ea-card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
          <thead class="table-light text-muted">
            <tr>
              <th class="ps-3">MÃ CODE</th>
              <th>CHƯƠNG TRÌNH KHUYẾN MÃI</th>
              <th>MỨC GIẢM</th>
              <th>ĐƠN TỐI THIỂU</th>
              <th>LƯỢT DÙNG</th>
              <th>TRẠNG THÁI</th>
              <th class="text-end pe-3">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="v in vouchers" :key="v.id">
              <td class="ps-3"><span class="badge bg-primary-subtle text-primary fs-6 fw-bold px-3">{{ v.ma_code }}</span></td>
              <td class="fw-semibold text-dark">{{ v.tieu_de }}</td>
              <td class="fw-bold text-danger">
                {{ v.loai_giam_gia === 'percent' ? v.gia_tri_giam + '%' : formatCurrency(v.gia_tri_giam) }}
              </td>
              <td>{{ formatCurrency(v.don_hang_toi_thieu) }}</td>
              <td>{{ v.da_su_dung }} / {{ v.so_luong }}</td>
              <td>
                <button class="btn btn-sm rounded-pill px-3" :class="v.trang_thai ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'" @click="toggleVoucherStatus(v)">
                  {{ v.trang_thai ? 'Đang chạy' : 'Tạm dừng' }}
                </button>
              </td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-primary me-1" @click="openVoucherModal(v)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteVoucher(v)"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
            <tr v-if="vouchers.length === 0">
              <td colspan="7" class="text-center text-muted py-4">Chưa có mã Voucher nào.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SUB-TAB 2: QUẢN LÝ BÌNH LUẬN & ĐÁNH GIÁ (UC16) -->
    <div v-else class="ea-card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
          <thead class="table-light text-muted">
            <tr>
              <th class="ps-3">SẢN PHẨM</th>
              <th>KHÁCH HÀNG</th>
              <th>SỐ SAO</th>
              <th>NỘI DUNG BÌNH LUẬN</th>
              <th>TRẠNG THÁI DUYỆT</th>
              <th class="text-end pe-3">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in reviews" :key="r.id">
              <td class="ps-3 fw-semibold text-dark" style="max-width: 220px;">
                {{ r.product ? r.product.ten_san_pham : ('SP #' + r.san_pham_id) }}
              </td>
              <td>
                <div class="fw-bold text-dark">{{ r.user ? r.user.ho_ten : 'Khách hàng' }}</div>
                <span v-if="r.da_mua_hang" class="badge bg-success-subtle text-success" style="font-size: 10px;">Đã mua hàng</span>
              </td>
              <td class="text-warning text-nowrap">
                <i class="bi bi-star-fill" v-for="s in r.so_sao" :key="s"></i>
              </td>
              <td style="max-width: 300px;">{{ r.noi_dung }}</td>
              <td>
                <button class="btn btn-sm rounded-pill px-3" :class="r.trang_thai_duyet ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'" @click="toggleReviewStatus(r)">
                  {{ r.trang_thai_duyet ? 'Đang hiển thị' : 'Đang ẩn' }}
                </button>
              </td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-danger" @click="deleteReview(r)"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
            <tr v-if="reviews.length === 0">
              <td colspan="6" class="text-center text-muted py-4">Chưa có bình luận đánh giá nào.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL THÊM / SỬA VOUCHER -->
    <div v-if="showVoucherModal" class="ea-modal-backdrop" @click.self="showVoucherModal = false">
      <div class="ea-modal-card p-4" style="max-width: 520px;">
        <h5 class="fw-bold mb-3">{{ voucherForm.id ? 'Cập nhật Voucher' : 'Tạo Voucher mới' }}</h5>
        <form @submit.prevent="saveVoucher">
          <div class="row g-2 mb-2">
            <div class="col-5">
              <label class="form-label small fw-semibold">Mã Code *</label>
              <input v-model="voucherForm.ma_code" required class="form-control form-control-sm text-uppercase" placeholder="TECH2026" />
            </div>
            <div class="col-7">
              <label class="form-label small fw-semibold">Tiêu đề chương trình *</label>
              <input v-model="voucherForm.tieu_de" required class="form-control form-control-sm" />
            </div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-4">
              <label class="form-label small fw-semibold">Loại giảm</label>
              <select v-model="voucherForm.loai_giam_gia" class="form-select form-select-sm">
                <option value="percent">Phần trăm (%)</option>
                <option value="fixed">Số tiền (VNĐ)</option>
              </select>
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Giá trị giảm *</label>
              <input type="number" v-model.number="voucherForm.gia_tri_giam" required class="form-control form-control-sm" />
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Số lượng *</label>
              <input type="number" v-model.number="voucherForm.so_luong" required class="form-control form-control-sm" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Đơn hàng tối thiểu (VNĐ)</label>
            <input type="number" v-model.number="voucherForm.don_hang_toi_thieu" class="form-control form-control-sm" />
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showVoucherModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Lưu Voucher</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';

export default {
  name: 'AdminVouchersReviewsTab',
  props: {
    vouchers: { type: Array, default: () => [] },
    reviews: { type: Array, default: () => [] },
    initialSubTab: { type: String, default: 'vouchers' }
  },
  data() {
    return {
      subTab: this.initialSubTab,
      showVoucherModal: false,
      voucherForm: {}
    };
  },
  watch: {
    initialSubTab(val) {
      if (val) this.subTab = val;
    }
  },
  methods: {
    openVoucherModal(v) {
      this.voucherForm = v ? { ...v } : { loai_giam_gia: 'percent', gia_tri_giam: 10, so_luong: 50, don_hang_toi_thieu: 0 };
      this.showVoucherModal = true;
    },
    async saveVoucher() {
      const endpoint = this.voucherForm.id ? '/admin/voucher/update' : '/admin/voucher/add-data';
      try {
        const res = await axios.post(apiUrl(endpoint), this.voucherForm);
        if (res.data?.status) {
          this.$emit('notify', res.data.message);
          this.showVoucherModal = false;
          this.$emit('refresh');
        }
      } catch (err) {
        this.$emit('notify', err.response?.data?.message || 'Lỗi lưu Voucher');
      }
    },
    async toggleVoucherStatus(v) {
      await axios.post(apiUrl('/admin/voucher/change-status'), { id: v.id });
      this.$emit('refresh');
    },
    async deleteVoucher(v) {
      if (!confirm(`Xóa Voucher ${v.ma_code}?`)) return;
      await axios.post(apiUrl('/admin/voucher/delete'), { id: v.id });
      this.$emit('refresh');
    },
    async toggleReviewStatus(r) {
      const res = await axios.post(apiUrl('/admin/danh-gia/change-status'), { id: r.id });
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.$emit('refresh');
      }
    },
    async deleteReview(r) {
      if (!confirm('Xóa bình luận này?')) return;
      await axios.post(apiUrl('/admin/danh-gia/delete'), { id: r.id });
      this.$emit('refresh');
    },
    formatCurrency(val) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
    }
  }
};
</script>

<style scoped>
.ea-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
.ea-modal-backdrop { position: fixed; inset: 0; background: rgba(15,23,42,.55); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 16px; }
.ea-modal-card { background: #fff; border-radius: 16px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,.2); }
</style>
