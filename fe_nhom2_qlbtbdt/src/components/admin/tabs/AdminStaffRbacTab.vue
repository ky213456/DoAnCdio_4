<template>
  <div>
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
      <div>
        <div class="text-primary fw-bold small mb-1">QUẢN TRỊ NHÂN SỰ & PHÂN QUYỀN RBAC</div>
        <h2 class="fw-bold text-dark mb-1">Quản lý Nhân viên & Phân quyền</h2>
        <p class="text-muted small mb-0">Thêm, sửa, xóa tài khoản quản trị/nhân viên và cấu hình quyền truy cập theo từng chức năng.</p>
      </div>
      <div class="d-flex gap-2">
        <div class="btn-group bg-white p-1 rounded-3 border shadow-sm">
          <button class="btn btn-sm fw-semibold" :class="subTab === 'staff' ? 'btn-primary' : 'btn-light'" @click="subTab = 'staff'">
            <i class="bi bi-person-badge me-1"></i> Nhân viên ({{ staffList.length }})
          </button>
          <button class="btn btn-sm fw-semibold" :class="subTab === 'rbac' ? 'btn-primary' : 'btn-light'" @click="subTab = 'rbac'">
            <i class="bi bi-shield-lock me-1"></i> Ma trận Phân quyền
          </button>
        </div>
        <button v-if="subTab === 'staff'" class="btn btn-primary btn-sm px-3 fw-bold" @click="openStaffModal(null)">
          <i class="bi bi-plus-lg me-1"></i> Thêm nhân viên
        </button>
      </div>
    </div>

    <!-- SUB-TAB 1: DANH SÁCH NHÂN VIÊN (UC10) -->
    <div v-if="subTab === 'staff'" class="ea-card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
          <thead class="table-light text-muted">
            <tr>
              <th class="ps-3">NHÂN VIÊN</th>
              <th>EMAIL & SĐT</th>
              <th>VAI TRÒ</th>
              <th>SỐ QUYỀN ĐƯỢC CẤP</th>
              <th>TRẠNG THÁI</th>
              <th class="text-end pe-3">THAO TÁC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="nv in staffList" :key="nv.id">
              <td class="ps-3 fw-bold text-dark">{{ nv.ho_ten }}</td>
              <td>
                <div>{{ nv.email }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ nv.so_dien_thoai || '-' }}</div>
              </td>
              <td>
                <span class="badge rounded-pill px-3" :class="nv.vai_tro === 'admin' ? 'bg-primary' : 'bg-info-subtle text-info-emphasis'">
                  {{ nv.vai_tro === 'admin' ? 'Admin (Toàn quyền)' : 'Nhân viên (Staff)' }}
                </span>
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  {{ (nv.quyen_han && nv.quyen_han.length) ? nv.quyen_han.length + ' quyền' : (nv.vai_tro === 'admin' ? 'Toàn quyền' : 'Mặc định (5 quyền)') }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm rounded-pill px-3" :class="nv.trang_thai ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'" @click="toggleStaffStatus(nv)">
                  {{ nv.trang_thai ? 'Đang làm' : 'Đã khóa' }}
                </button>
              </td>
              <td class="text-end pe-3">
                <button class="btn btn-sm btn-outline-primary me-1" @click="openStaffModal(nv)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteStaff(nv)"><i class="bi bi-trash3"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- SUB-TAB 2: MA TRẬN PHÂN QUYỀN RBAC (UC11) -->
    <div v-else class="ea-card p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom mb-3">
        <div class="d-flex align-items-center gap-2">
          <span class="fw-bold text-dark small">Chọn tài khoản phân quyền:</span>
          <select v-model="selectedStaffId" class="form-select form-select-sm" style="width: 260px;">
            <option v-for="nv in staffList" :key="nv.id" :value="nv.id">{{ nv.ho_ten }} ({{ nv.vai_tro }})</option>
          </select>
        </div>
        <button class="btn btn-primary btn-sm px-4 fw-bold" @click="savePermissions" :disabled="!selectedStaffId">
          <i class="bi bi-save me-1"></i> Lưu cấu hình phân quyền
        </button>
      </div>

      <div class="row g-3">
        <div v-for="uc in allUseCases" :key="uc.code" class="col-md-6 col-lg-4">
          <label class="p-3 border rounded-3 d-flex align-items-start gap-2 cursor-pointer h-100" :class="selectedPerms.includes(uc.code) ? 'bg-primary-subtle bg-opacity-25 border-primary' : 'bg-light'">
            <input type="checkbox" class="form-check-input mt-1" :value="uc.code" v-model="selectedPerms" />
            <div>
              <div class="fw-bold text-dark small">{{ uc.name }}</div>
              <div class="text-muted" style="font-size: 11px;">Nhóm: {{ uc.roleHint }}</div>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- MODAL THÊM / SỬA NHÂN VIÊN -->
    <div v-if="showStaffModal" class="ea-modal-backdrop" @click.self="showStaffModal = false">
      <div class="ea-modal-card p-4" style="max-width: 480px;">
        <h5 class="fw-bold mb-3">{{ staffForm.id ? 'Cập nhật Nhân viên' : 'Thêm Nhân viên mới' }}</h5>
        <form @submit.prevent="saveStaff">
          <div class="mb-2">
            <label class="form-label small fw-semibold">Họ và tên *</label>
            <input v-model="staffForm.ho_ten" required class="form-control form-control-sm" />
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small fw-semibold">Email *</label>
              <input type="email" v-model="staffForm.email" required class="form-control form-control-sm" />
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">Số điện thoại</label>
              <input v-model="staffForm.so_dien_thoai" class="form-control form-control-sm" />
            </div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-semibold">Vai trò *</label>
              <select v-model="staffForm.vai_tro" class="form-select form-select-sm">
                <option value="staff">Nhân viên (Staff)</option>
                <option value="admin">Quản trị viên (Admin)</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-semibold">{{ staffForm.id ? 'Mật khẩu mới (nếu đổi)' : 'Mật khẩu *' }}</label>
              <input type="password" v-model="staffForm.password" :required="!staffForm.id" class="form-control form-control-sm" />
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showStaffModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Lưu nhân viên</button>
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
  name: 'AdminStaffRbacTab',
  props: {
    staffList: { type: Array, default: () => [] },
    initialSubTab: { type: String, default: 'staff' }
  },
  data() {
    return {
      subTab: this.initialSubTab || 'staff',
      showStaffModal: false,
      staffForm: { vai_tro: 'staff' },
      selectedStaffId: null,
      selectedPerms: [],
      allUseCases: [
        { code: 'UC09', name: 'Báo cáo thống kê', roleHint: 'Quản trị hệ thống' },
        { code: 'UC10', name: 'Quản lý nhân viên', roleHint: 'Quản trị hệ thống' },
        { code: 'UC11', name: 'Phân quyền hệ thống', roleHint: 'Quản trị hệ thống' },
        { code: 'UC12', name: 'Quản lý sản phẩm', roleHint: 'Bán hàng' },
        { code: 'UC13', name: 'Quản lý Voucher', roleHint: 'Bán hàng' },
        { code: 'UC14', name: 'Quản lý đơn hàng', roleHint: 'Bán hàng' },
        { code: 'UC15', name: 'Quản lý khách hàng', roleHint: 'Bán hàng' },
        { code: 'UC16', name: 'Quản lý bình luận', roleHint: 'Bán hàng' },
        { code: 'UC22', name: 'Quản lý danh mục & Thương hiệu', roleHint: 'Kho & Bảo hành' },
        { code: 'UC23', name: 'Quản lý nhập kho & Serial/IMEI', roleHint: 'Kho & Bảo hành' },
        { code: 'UC19', name: 'Tiếp nhận & xử lý bảo hành, đổi trả', roleHint: 'Kho & Bảo hành' }
      ]
    };
  },
  created() {
    this.loadChucNangFromDb();
  },
  watch: {
    initialSubTab(val) {
      if (val) this.subTab = val;
    },
    staffList: {
      immediate: true,
      handler(val) {
        if (!this.selectedStaffId && val.length > 0) {
          this.selectedStaffId = val[0].id;
        }
      }
    },
    selectedStaffId: {
      immediate: true,
      handler(id) {
        const nv = this.staffList.find(s => s.id === id);
        if (nv) {
          this.selectedPerms = Array.isArray(nv.quyen_han)
            ? [...nv.quyen_han]
            : (nv.vai_tro === 'admin' ? this.allUseCases.map(u => u.code) : []);
        }
      }
    }
  },
  methods: {
    async loadChucNangFromDb() {
      try {
        const res = await axios.get(apiUrl('/admin/chuc-nang/get-data'));
        if (res.data?.status && Array.isArray(res.data.data) && res.data.data.length > 0) {
          this.allUseCases = res.data.data.map(cn => ({
            id: cn.id,
            code: cn.ma_chuc_nang,
            name: cn.ten_chuc_nang,
            roleHint: cn.nhom_quyen || 'Hệ thống'
          }));
        }
      } catch (e) { /* keep fallback list */ }
    },
    openStaffModal(nv) {
      this.staffForm = nv ? { ...nv, password: '' } : { vai_tro: 'staff' };
      this.showStaffModal = true;
    },
    async saveStaff() {
      const endpoint = this.staffForm.id ? '/admin/nhan-vien/update' : '/admin/nhan-vien/add-data';
      try {
        const res = await axios.post(apiUrl(endpoint), this.staffForm);
        if (res.data?.status) {
          this.$emit('notify', res.data.message);
          this.showStaffModal = false;
          this.$emit('refresh');
        }
      } catch (err) {
        this.$emit('notify', err.response?.data?.message || 'Lỗi lưu nhân viên');
      }
    },
    async toggleStaffStatus(nv) {
      await axios.post(apiUrl('/admin/nhan-vien/change-status'), { id: nv.id });
      this.$emit('refresh');
    },
    async deleteStaff(nv) {
      if (!confirm(`Xóa nhân viên "${nv.ho_ten}"?`)) return;
      try {
        const res = await axios.post(apiUrl('/admin/nhan-vien/delete'), { id: nv.id });
        if (res.data?.status) this.$emit('refresh');
      } catch (err) {
        this.$emit('notify', err.response?.data?.message || 'Không thể xóa nhân viên này');
      }
    },
    async savePermissions() {
      const res = await axios.post(apiUrl('/admin/nhan-vien/update-permissions'), {
        id: this.selectedStaffId,
        quyen_han: this.selectedPerms
      });
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.$emit('refresh');
      }
    }
  }
};
</script>

<style scoped>
.ea-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
.ea-modal-backdrop { position: fixed; inset: 0; background: rgba(15,23,42,.55); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 16px; }
.ea-modal-card { background: #fff; border-radius: 16px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,.2); }
.cursor-pointer { cursor: pointer; }
</style>
