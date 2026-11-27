<template>
  <div>
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
      <div>
        <div class="text-primary fw-bold small mb-1">TRUNG TÂM ĐIỀU HÀNH CSKH & DỊCH VỤ HẬU MÃI</div>
        <h2 class="fw-bold text-dark mb-1">Khách hàng & Dịch vụ Bảo hành</h2>
        <p class="text-muted small mb-0">Kiểm soát vòng đời thiết bị, tiếp nhận phiếu bảo hành / đổi trả và quản lý hồ sơ khách hàng.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-outline-primary btn-sm bg-white fw-semibold" @click="openCustomerModal(null)">
          <i class="bi bi-person-plus me-1"></i> Thêm khách hàng
        </button>
        <button class="btn btn-primary btn-sm px-3 fw-bold" @click="openTicketModal(null)">
          <i class="bi bi-plus-lg me-1"></i> Tạo phiếu bảo hành / đổi trả
        </button>
      </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">TỔNG KHÁCH HÀNG</div>
          <div class="fs-4 fw-bold text-dark">{{ customers.length }}</div>
          <div class="small text-success">Tài khoản thành viên</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">PHIẾU ĐANG XỬ LÝ</div>
          <div class="fs-4 fw-bold text-primary">{{ activeTicketsCount }} phiếu</div>
          <div class="small text-muted">Bảo hành & Đổi trả</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">ĐÃ SỬA XONG / TRẢ MÁY</div>
          <div class="fs-4 fw-bold text-success">{{ doneTicketsCount }} phiếu</div>
          <div class="small text-muted">Hoàn tất dịch vụ</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">XỬ LÝ ĐÚNG HẠN SLA</div>
          <div class="fs-4 fw-bold text-dark">96.8%</div>
          <div class="small text-primary">Mục tiêu SLA: &gt;95%</div>
        </div>
      </div>
    </div>

    <!-- Instant Serial/IMEI Lookup Card -->
    <div class="ea-card p-4 mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-upc-scan text-primary me-2"></i>Khung Công cụ Tra cứu Bảo hành Siêu Tốc (Instant Serial/IMEI Lookup)</h6>
        <span class="small text-success fw-semibold"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>CSDL Serial TechMart Realtime</span>
      </div>
      <div class="input-group mb-3">
        <input v-model="lookupSerial" @keyup.enter="runInstantLookup" class="form-control" placeholder="Nhập số Serial hoặc IMEI (VD: ROG-G16-4060-2026-SN04, SAMS-990P-1001, LG-27GR75Q-003)..." />
        <button class="btn btn-primary px-4 fw-bold" @click="runInstantLookup" :disabled="isLookingUp">
          <i class="bi bi-search me-1"></i> Kiểm tra ngay
        </button>
      </div>

      <div v-if="lookupError" class="alert alert-warning small py-2 mb-0">{{ lookupError }}</div>
      <div v-if="lookupResult" class="p-3 bg-light rounded-3 border d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <div class="fw-bold text-dark">{{ lookupResult.ten_san_pham }} <span class="badge bg-success ms-2">Chính hãng TechMart</span></div>
          <div class="small text-primary fw-semibold mt-1">Serial: {{ lookupResult.so_serial }} <span v-if="lookupResult.ma_don_hang">• Đơn hàng #{{ lookupResult.ma_don_hang }}</span></div>
          <div class="small text-muted mt-1">
            Hạn BH đến: <strong>{{ formatDate(lookupResult.han_bao_hanh) }}</strong>
            <span v-if="lookupResult.ho_ten_nguoi_nhan"> • Chủ sở hữu: {{ lookupResult.ho_ten_nguoi_nhan }} ({{ lookupResult.so_dien_thoai_nhan }})</span>
          </div>
        </div>
        <button class="btn btn-primary btn-sm fw-bold" @click="prefillTicketFromLookup">
          <i class="bi bi-plus-circle me-1"></i> Tạo phiếu tiếp nhận
        </button>
      </div>
    </div>

    <!-- Sub-tabs: Phiếu Bảo hành vs Danh sách Khách hàng -->
    <div class="ea-card p-2 mb-3 d-inline-flex gap-2">
      <button class="btn btn-sm fw-semibold" :class="subTab === 'tickets' ? 'btn-primary' : 'btn-light'" @click="subTab = 'tickets'">
        <i class="bi bi-tools me-1"></i> Phiếu Bảo hành & Đổi trả ({{ tickets.length }})
      </button>
      <button class="btn btn-sm fw-semibold" :class="subTab === 'customers' ? 'btn-primary' : 'btn-light'" @click="subTab = 'customers'">
        <i class="bi bi-people me-1"></i> Danh sách Khách hàng ({{ customers.length }})
      </button>
    </div>

    <!-- TAB 1: PHIẾU BẢO HÀNH & ĐỔI TRẢ (UC19) -->
    <div v-if="subTab === 'tickets'" class="ea-card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
          <thead class="table-light text-muted">
            <tr>
              <th class="ps-3">MÃ PHIẾU</th>
              <th>KHÁCH HÀNG</th>
              <th>THIẾT BỊ & SERIAL</th>
              <th>LOẠI & MÔ TẢ LỖI</th>
              <th>HẠN TRẢ & KTV</th>
              <th>TRẠNG THÁI XỬ LÝ</th>
              <th class="text-end pe-3">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in tickets" :key="t.id">
              <td class="ps-3 fw-bold text-primary">#{{ t.ma_phieu }}</td>
              <td>
                <div class="fw-bold text-dark">{{ t.ten_khach_hang }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ t.so_dien_thoai }}</div>
              </td>
              <td>
                <div class="fw-semibold text-dark">{{ t.ten_thiet_bi }}</div>
                <div class="text-muted" style="font-size: 11px;" v-if="t.so_serial">SN: {{ t.so_serial }}</div>
              </td>
              <td style="max-width: 240px;">
                <span class="badge mb-1" :class="t.loai_yeu_cau === 'doi_tra' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis'">
                  {{ t.loai_yeu_cau === 'doi_tra' ? 'Đổi trả' : 'Bảo hành' }}
                </span>
                <div class="text-dark">{{ t.mo_ta_loi }}</div>
              </td>
              <td>
                <div class="text-dark">{{ formatDate(t.han_tra) }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ t.ky_thuat_vien }}</div>
              </td>
              <td>
                <select
                  :value="t.trang_thai"
                  @change="updateTicketStatus(t, $event.target.value)"
                  class="form-select form-select-sm fw-semibold"
                  style="width: 155px;"
                >
                  <option value="tiep_nhan">Đã tiếp nhận</option>
                  <option value="dang_kiem_tra">Đang kiểm tra KT</option>
                  <option value="cho_linh_kien">Chờ linh kiện</option>
                  <option value="da_sua_xong">Đã sửa xong</option>
                  <option value="da_tra_hang">Đã trả hàng/đổi</option>
                </select>
              </td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-danger" @click="deleteTicket(t)"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
            <tr v-if="tickets.length === 0">
              <td colspan="7" class="text-center text-muted py-4">Chưa có phiếu tiếp nhận bảo hành / đổi trả nào.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: DANH SÁCH KHÁCH HÀNG (UC15) -->
    <div v-else class="ea-card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
          <thead class="table-light text-muted">
            <tr>
              <th class="ps-3">KHÁCH HÀNG</th>
              <th>EMAIL & SĐT</th>
              <th>ĐỊA CHỈ</th>
              <th>SỐ ĐƠN</th>
              <th>TRẠNG THÁI</th>
              <th class="text-end pe-3">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in customers" :key="c.id">
              <td class="ps-3 fw-bold text-dark">{{ c.ho_ten }}</td>
              <td>
                <div class="text-dark">{{ c.email }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ c.so_dien_thoai || '-' }}</div>
              </td>
              <td class="text-muted">{{ c.dia_chi || '-' }}</td>
              <td><span class="badge bg-light text-dark border">{{ c.orders_count ?? 0 }} đơn</span></td>
              <td>
                <button class="btn btn-sm rounded-pill px-3" :class="c.trang_thai ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'" @click="toggleCustomerStatus(c)">
                  {{ c.trang_thai ? 'Hoạt động' : 'Đã khóa' }}
                </button>
              </td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-primary me-1" @click="openCustomerModal(c)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteCustomer(c)"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL TẠO PHIẾU BẢO HÀNH (UC19) -->
    <div v-if="showTicketModal" class="ea-modal-backdrop" @click.self="showTicketModal = false">
      <div class="ea-modal-card p-4" style="max-width: 540px;">
        <h5 class="fw-bold mb-3">Tạo phiếu tiếp nhận Bảo hành / Đổi trả</h5>
        <form @submit.prevent="saveTicket">
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small fw-semibold">Tên khách hàng *</label>
              <input v-model="ticketForm.ten_khach_hang" required class="form-control form-control-sm" />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Số điện thoại</label>
              <input v-model="ticketForm.so_dien_thoai" class="form-control form-control-sm" />
            </div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-8">
              <label class="form-label small fw-semibold">Tên thiết bị *</label>
              <input v-model="ticketForm.ten_thiet_bi" required class="form-control form-control-sm" />
            </div>
            <div class="col-4">
              <label class="form-label small fw-semibold">Loại yêu cầu</label>
              <select v-model="ticketForm.loai_yeu_cau" class="form-select form-select-sm">
                <option value="bao_hanh">Bảo hành</option>
                <option value="doi_tra">Đổi trả</option>
              </select>
            </div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small fw-semibold">Số Serial / IMEI</label>
              <input v-model="ticketForm.so_serial" class="form-control form-control-sm" />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Kỹ thuật viên phụ trách</label>
              <input v-model="ticketForm.ky_thuat_vien" class="form-control form-control-sm" placeholder="Trần Linh (Level 2)" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Mô tả lỗi kỹ thuật / Lý do đổi trả *</label>
            <textarea v-model="ticketForm.mo_ta_loi" required rows="3" class="form-control form-control-sm"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showTicketModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Tạo phiếu</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL THÊM / SỬA KHÁCH HÀNG (UC15) -->
    <div v-if="showCustModal" class="ea-modal-backdrop" @click.self="showCustModal = false">
      <div class="ea-modal-card p-4" style="max-width: 480px;">
        <h5 class="fw-bold mb-3">{{ custForm.id ? 'Cập nhật Khách hàng' : 'Thêm Khách hàng mới' }}</h5>
        <form @submit.prevent="saveCustomer">
          <div class="mb-2">
            <label class="form-label small fw-semibold">Họ và tên *</label>
            <input v-model="custForm.ho_ten" required class="form-control form-control-sm" />
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small fw-semibold">Email *</label>
              <input type="email" v-model="custForm.email" required class="form-control form-control-sm" />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Số điện thoại</label>
              <input v-model="custForm.so_dien_thoai" class="form-control form-control-sm" />
            </div>
          </div>
          <div class="mb-2" v-if="!custForm.id">
            <label class="form-label small fw-semibold">Mật khẩu mặc định</label>
            <input type="password" v-model="custForm.password" class="form-control form-control-sm" placeholder="Mặc định: 123456" />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Địa chỉ</label>
            <input v-model="custForm.dia_chi" class="form-control form-control-sm" />
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showCustModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Lưu</button>
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
  name: 'AdminWarrantyCustomersTab',
  props: {
    customers: { type: Array, default: () => [] },
    tickets: { type: Array, default: () => [] },
    initialSubTab: { type: String, default: 'tickets' }
  },
  data() {
    return {
      subTab: this.initialSubTab || 'tickets',
      lookupSerial: '',
      lookupResult: null,
      lookupError: '',
      isLookingUp: false,
      showTicketModal: false,
      ticketForm: { loai_yeu_cau: 'bao_hanh' },
      showCustModal: false,
      custForm: {}
    };
  },
  watch: {
    initialSubTab(val) {
      if (val) this.subTab = val;
    }
  },
  computed: {
    activeTicketsCount() {
      return this.tickets.filter(t => !['da_sua_xong', 'da_tra_hang'].includes(t.trang_thai)).length;
    },
    doneTicketsCount() {
      return this.tickets.filter(t => ['da_sua_xong', 'da_tra_hang'].includes(t.trang_thai)).length;
    }
  },
  methods: {
    async runInstantLookup() {
      if (!this.lookupSerial.trim()) return;
      this.isLookingUp = true;
      this.lookupError = '';
      this.lookupResult = null;
      try {
        const res = await axios.get(apiUrl('/client/tra-cuu-bao-hanh'), { params: { serial: this.lookupSerial.trim() } });
        if (res.data?.status) this.lookupResult = res.data.data;
        else this.lookupError = res.data?.message || 'Không tìm thấy Serial/IMEI';
      } catch (e) {
        this.lookupError = e.response?.data?.message || 'Không tìm thấy Serial/IMEI này trong kho.';
      } finally {
        this.isLookingUp = false;
      }
    },
    prefillTicketFromLookup() {
      if (!this.lookupResult) return;
      this.ticketForm = {
        ten_khach_hang: this.lookupResult.ho_ten_nguoi_nhan || '',
        so_dien_thoai: this.lookupResult.so_dien_thoai_nhan || '',
        ten_thiet_bi: this.lookupResult.ten_san_pham,
        so_serial: this.lookupResult.so_serial,
        loai_yeu_cau: 'bao_hanh',
        mo_ta_loi: ''
      };
      this.showTicketModal = true;
    },
    openTicketModal(t) {
      this.ticketForm = t ? { ...t } : { loai_yeu_cau: 'bao_hanh', ky_thuat_vien: 'Trần Linh (Level 2)' };
      this.showTicketModal = true;
    },
    async saveTicket() {
      const res = await axios.post(apiUrl('/admin/bao-hanh/create'), this.ticketForm);
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.showTicketModal = false;
        this.$emit('refresh');
      }
    },
    async updateTicketStatus(t, status) {
      const res = await axios.post(apiUrl('/admin/bao-hanh/update-status'), { id: t.id, trang_thai: status });
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.$emit('refresh');
      }
    },
    async deleteTicket(t) {
      if (!confirm(`Xóa phiếu #${t.ma_phieu}?`)) return;
      await axios.post(apiUrl('/admin/bao-hanh/delete'), { id: t.id });
      this.$emit('refresh');
    },
    openCustomerModal(c) {
      this.custForm = c ? { ...c } : { ho_ten: '', email: '', so_dien_thoai: '', dia_chi: '' };
      this.showCustModal = true;
    },
    async saveCustomer() {
      const endpoint = this.custForm.id ? '/admin/khach-hang/update' : '/admin/khach-hang/add-data';
      try {
        const res = await axios.post(apiUrl(endpoint), this.custForm);
        if (res.data?.status) {
          this.$emit('notify', res.data.message);
          this.showCustModal = false;
          this.$emit('refresh');
        }
      } catch (err) {
        this.$emit('notify', err.response?.data?.message || 'Lỗi lưu khách hàng');
      }
    },
    async toggleCustomerStatus(c) {
      await axios.post(apiUrl('/admin/khach-hang/change-status'), { id: c.id });
      this.$emit('refresh');
    },
    async deleteCustomer(c) {
      if (!confirm(`Xóa khách hàng "${c.ho_ten}"?`)) return;
      await axios.post(apiUrl('/admin/khach-hang/delete'), { id: c.id });
      this.$emit('refresh');
    },
    formatDate(d) {
      return d ? new Date(d).toLocaleDateString('vi-VN') : '-';
    }
  }
};
</script>

<style scoped>
.ea-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
.ea-kpi-label { font-size: 11px; font-weight: 700; color: #64748b; }
.ea-modal-backdrop { position: fixed; inset: 0; background: rgba(15,23,42,.55); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 16px; }
.ea-modal-card { background: #fff; border-radius: 16px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,.2); }
</style>
