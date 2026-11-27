<template>
  <div>
    <!-- Header + Sub-tabs (UC12 Sản phẩm vs UC22 Danh mục & Thương hiệu) -->
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
      <div>
        <div class="text-primary fw-bold small mb-1">KHO LOGISTICS TRUNG ƯƠNG • CẬP NHẬT TỨC THÌ</div>
        <h2 class="fw-bold text-dark mb-1">Sản phẩm, Danh mục & Tồn kho</h2>
        <p class="text-muted small mb-0">Quản lý định danh SKU, phân loại danh mục/thương hiệu, cấu hình biến thể và tồn kho.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <div class="btn-group bg-white p-1 rounded-3 border shadow-sm">
          <button class="btn btn-sm fw-semibold" :class="subTab === 'products' ? 'btn-primary' : 'btn-light'" @click="subTab = 'products'">
            <i class="bi bi-laptop me-1"></i> Sản phẩm ({{ products.length }})
          </button>
          <button class="btn btn-sm fw-semibold" :class="subTab === 'categories' ? 'btn-primary' : 'btn-light'" @click="subTab = 'categories'">
            <i class="bi bi-folder2-open me-1"></i> Danh mục & Hãng ({{ categories.length + brands.length }})
          </button>
        </div>
        <button class="btn btn-primary btn-sm px-3 fw-bold" @click="openProductModal(null)" v-if="subTab === 'products'">
          <i class="bi bi-plus-lg me-1"></i> Thêm thiết bị mới
        </button>
      </div>
    </div>

    <!-- ===================================== -->
    <!-- SUB-TAB 1: QUẢN LÝ SẢN PHẨM (UC12) -->
    <!-- ===================================== -->
    <div v-if="subTab === 'products'">
      <!-- 5 KPI Cards (4 white + 1 royal blue gradient) -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg">
          <div class="ea-card p-3 h-100">
            <div class="ea-kpi-label mb-1">TỔNG SẢN PHẨM</div>
            <div class="fs-4 fw-bold text-dark">{{ products.length }}</div>
            <div class="small text-primary">Đang quản lý</div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="ea-card p-3 h-100">
            <div class="ea-kpi-label mb-1">SẴN SÀNG XUẤT</div>
            <div class="fs-4 fw-bold text-dark">{{ totalStock }}</div>
            <div class="small text-success">Tồn kho thực tế</div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="ea-card p-3 h-100">
            <div class="ea-kpi-label mb-1">SẮP HẾT HÀNG</div>
            <div class="fs-4 fw-bold text-warning">{{ lowStockCount }}</div>
            <div class="small text-muted">Dưới 10 chiếc</div>
          </div>
        </div>
        <div class="col-6 col-lg">
          <div class="ea-card p-3 h-100">
            <div class="ea-kpi-label mb-1">NGỪNG KINH DOANH</div>
            <div class="fs-4 fw-bold text-danger">{{ inactiveCount }}</div>
            <div class="small text-muted">Đang tạm ẩn</div>
          </div>
        </div>
        <div class="col-12 col-lg">
          <div class="ea-card-blue p-3 h-100 text-white">
            <div class="small opacity-75 fw-bold mb-1">TỔNG ĐỊNH GIÁ TỒN KHO</div>
            <div class="fs-5 fw-bold">{{ formatCurrency(totalInventoryValue) }}</div>
            <div class="small opacity-75">100% Khả dụng</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="ea-card p-3 mb-4">
        <div class="row g-2 align-items-center">
          <div class="col-md-5">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
              <input type="text" v-model="searchText" class="form-control shadow-none" placeholder="Tìm theo tên thiết bị, mã SKU..." />
            </div>
          </div>
          <div class="col-md-3">
            <select v-model="filterCat" class="form-select form-select-sm">
              <option value="">Tất cả Danh mục</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.ten_danh_muc }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <select v-model="filterBrand" class="form-select form-select-sm">
              <option value="">Tất cả Thương hiệu</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.ten_thuong_hieu }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <select v-model="filterStatus" class="form-select form-select-sm">
              <option value="">Tất cả Trạng thái</option>
              <option value="1">Đang bán</option>
              <option value="0">Tạm ẩn</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Product Table -->
      <div class="ea-card overflow-hidden mb-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-muted">
              <tr>
                <th class="ps-3">ẢNH & TÊN THIẾT BỊ / SKU</th>
                <th>DANH MỤC & HÃNG</th>
                <th>GIÁ BÁN / NIÊM YẾT</th>
                <th>TỒN THỰC</th>
                <th>TRẠNG THÁI</th>
                <th class="text-end pe-3">THAO TÁC</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in filteredProducts" :key="p.id" @click="selectedInspectProduct = p" class="cursor-pointer">
                <td class="ps-3">
                  <div class="d-flex align-items-center gap-3">
                    <img :src="p.anh_chinh || 'https://via.placeholder.com/48'" class="rounded border p-1 bg-white" style="width: 48px; height: 48px; object-fit: contain;" />
                    <div>
                      <div class="fw-bold text-dark">{{ p.ten_san_pham }}</div>
                      <div class="text-muted" style="font-size: 11px;">SKU: {{ p.ma_san_pham || ('SP-' + p.id) }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ p.category ? p.category.ten_danh_muc : '-' }}</div>
                  <div class="text-muted" style="font-size: 11px;">{{ p.brand ? p.brand.ten_thuong_hieu : '-' }}</div>
                </td>
                <td>
                  <div class="fw-bold text-primary">{{ formatCurrency(p.gia_khuyen_mai || p.gia_goc) }}</div>
                  <div v-if="p.gia_khuyen_mai" class="text-muted text-decoration-line-through" style="font-size: 11px;">{{ formatCurrency(p.gia_goc) }}</div>
                </td>
                <td>
                  <div class="fw-bold text-dark">{{ getStock(p) }} chiếc</div>
                  <div class="progress mt-1" style="height: 4px; width: 80px;">
                    <div class="progress-bar" :class="getStock(p) < 10 ? 'bg-danger' : 'bg-success'" :style="{ width: Math.min(100, getStock(p) * 3) + '%' }"></div>
                  </div>
                </td>
                <td>
                  <button
                    class="btn btn-sm rounded-pill px-3 fw-semibold"
                    :class="p.trang_thai ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'"
                    @click.stop="toggleProductStatus(p)"
                  >
                    {{ p.trang_thai ? 'Đang bán' : 'Tạm ẩn' }}
                  </button>
                </td>
                <td class="text-end pe-3" @click.stop>
                  <button class="btn btn-sm btn-outline-primary me-1" @click="openProductModal(p)" title="Sửa">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteProduct(p)" title="Xóa">
                    <i class="bi bi-trash3"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Variant Quick Inspector -->
      <div class="ea-card p-4" v-if="selectedInspectProduct">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-layers text-primary me-2"></i>Kiểm tra Biến thể Cấu hình (Variant Quick Inspector)</h6>
            <small class="text-muted">Đang xem SKU gốc: <strong>{{ selectedInspectProduct.ten_san_pham }}</strong></small>
          </div>
          <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ selectedInspectProduct.ma_san_pham }}</span>
        </div>
        <div class="row g-3" v-if="selectedInspectProduct.variants && selectedInspectProduct.variants.length > 0">
          <div class="col-md-4" v-for="v in selectedInspectProduct.variants" :key="v.id">
            <div class="p-3 border rounded-3 bg-light h-100">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary">{{ v.ma_sku }}</span>
                <span class="small fw-semibold text-success">Còn {{ v.so_luong_ton }} chiếc</span>
              </div>
              <div class="fw-bold text-dark mb-2">{{ v.ten_bien_the }}</div>
              <div class="fs-5 fw-bold text-danger">{{ formatCurrency(v.gia_ban) }}</div>
            </div>
          </div>
        </div>
        <div v-else class="text-muted small">Sản phẩm chưa có biến thể phụ.</div>
      </div>
    </div>

    <!-- ===================================== -->
    <!-- SUB-TAB 2: DANH MỤC & THƯƠNG HIỆU (UC22) -->
    <!-- ===================================== -->
    <div v-else class="row g-4">
      <!-- Danh mục -->
      <div class="col-lg-6">
        <div class="ea-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-folder-fill text-primary me-2"></i>Quản lý Danh mục</h5>
            <button class="btn btn-primary btn-sm" @click="openCatModal(null)"><i class="bi bi-plus"></i> Thêm Danh mục</button>
          </div>
          <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
              <tr><th>TÊN DANH MỤC</th><th>SỐ SP</th><th>TRẠNG THÁI</th><th class="text-end">THAO TÁC</th></tr>
            </thead>
            <tbody>
              <tr v-for="c in categories" :key="c.id">
                <td class="fw-bold text-dark">{{ c.ten_danh_muc }}</td>
                <td><span class="badge bg-light text-dark border">{{ c.products_count ?? 0 }}</span></td>
                <td>
                  <button class="btn btn-sm rounded-pill px-2" :class="c.trang_thai ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'" @click="toggleCatStatus(c)">
                    {{ c.trang_thai ? 'Hiện' : 'Ẩn' }}
                  </button>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" @click="openCatModal(c)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteCat(c)"><i class="bi bi-trash3"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Thương hiệu -->
      <div class="col-lg-6">
        <div class="ea-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-award-fill text-warning me-2"></i>Quản lý Thương hiệu</h5>
            <button class="btn btn-primary btn-sm" @click="openBrandModal(null)"><i class="bi bi-plus"></i> Thêm Hãng</button>
          </div>
          <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
              <tr><th>THƯƠNG HIỆU</th><th>SỐ SP</th><th>TRẠNG THÁI</th><th class="text-end">THAO TÁC</th></tr>
            </thead>
            <tbody>
              <tr v-for="b in brands" :key="b.id">
                <td class="fw-bold text-dark">{{ b.ten_thuong_hieu }}</td>
                <td><span class="badge bg-light text-dark border">{{ b.products_count ?? 0 }}</span></td>
                <td>
                  <button class="btn btn-sm rounded-pill px-2" :class="b.trang_thai ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'" @click="toggleBrandStatus(b)">
                    {{ b.trang_thai ? 'Hiện' : 'Ẩn' }}
                  </button>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" @click="openBrandModal(b)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteBrand(b)"><i class="bi bi-trash3"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL THÊM / SỬA SẢN PHẨM -->
    <div v-if="showProdModal" class="ea-modal-backdrop" @click.self="showProdModal = false">
      <div class="ea-modal-card p-4" style="max-width: 680px;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
          <h5 class="fw-bold mb-0">{{ prodForm.id ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm mới' }}</h5>
          <button class="btn-close" @click="showProdModal = false"></button>
        </div>
        <form @submit.prevent="saveProduct">
          <div class="row g-2 mb-2">
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Tên sản phẩm *</label>
              <input v-model="prodForm.ten_san_pham" required class="form-control form-control-sm" />
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Mã SKU</label>
              <input v-model="prodForm.ma_san_pham" class="form-control form-control-sm" placeholder="SP-..." />
            </div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Danh mục *</label>
              <select v-model="prodForm.danh_muc_id" required class="form-select form-select-sm">
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.ten_danh_muc }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Thương hiệu *</label>
              <select v-model="prodForm.thuong_hieu_id" required class="form-select form-select-sm">
                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.ten_thuong_hieu }}</option>
              </select>
            </div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Giá gốc (VNĐ) *</label>
              <input type="number" v-model.number="prodForm.gia_goc" required class="form-control form-control-sm" />
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Giá khuyến mãi</label>
              <input type="number" v-model.number="prodForm.gia_khuyen_mai" class="form-control form-control-sm" />
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Tồn kho ban đầu</label>
              <input type="number" v-model.number="prodForm.so_luong_ton" class="form-control form-control-sm" />
            </div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">CPU</label>
              <input v-model="prodForm.cpu" class="form-control form-control-sm" placeholder="VD: Intel Core i7-13650HX" />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">RAM & Ổ cứng</label>
              <input v-model="prodForm.ram" class="form-control form-control-sm" placeholder="VD: 16GB DDR5" />
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label small fw-semibold">Link ảnh chính</label>
            <input v-model="prodForm.anh_chinh" class="form-control form-control-sm" placeholder="https://..." />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Mô tả ngắn</label>
            <textarea v-model="prodForm.mo_ta_ngan" rows="2" class="form-control form-control-sm"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showProdModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Lưu sản phẩm</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL DANH MỤC -->
    <div v-if="showCatModal" class="ea-modal-backdrop" @click.self="showCatModal = false">
      <div class="ea-modal-card p-4" style="max-width: 440px;">
        <h5 class="fw-bold mb-3">{{ catForm.id ? 'Sửa Danh mục' : 'Thêm Danh mục' }}</h5>
        <form @submit.prevent="saveCat">
          <div class="mb-2">
            <label class="form-label small fw-semibold">Tên danh mục *</label>
            <input v-model="catForm.ten_danh_muc" required class="form-control form-control-sm" />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Mô tả</label>
            <textarea v-model="catForm.mo_ta" rows="2" class="form-control form-control-sm"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showCatModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL THƯƠNG HIỆU -->
    <div v-if="showBrandModal" class="ea-modal-backdrop" @click.self="showBrandModal = false">
      <div class="ea-modal-card p-4" style="max-width: 440px;">
        <h5 class="fw-bold mb-3">{{ brandForm.id ? 'Sửa Thương hiệu' : 'Thêm Thương hiệu' }}</h5>
        <form @submit.prevent="saveBrand">
          <div class="mb-2">
            <label class="form-label small fw-semibold">Tên thương hiệu *</label>
            <input v-model="brandForm.ten_thuong_hieu" required class="form-control form-control-sm" />
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Mô tả</label>
            <textarea v-model="brandForm.mo_ta" rows="2" class="form-control form-control-sm"></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light btn-sm" @click="showBrandModal = false">Hủy</button>
            <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
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
  name: 'AdminProductsTab',
  props: {
    products: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    initialSubTab: { type: String, default: 'products' }
  },
  data() {
    return {
      subTab: this.initialSubTab || 'products',
      searchText: '',
      filterCat: '',
      filterBrand: '',
      filterStatus: '',
      selectedInspectProduct: null,
      showProdModal: false,
      prodForm: {},
      showCatModal: false,
      catForm: {},
      showBrandModal: false,
      brandForm: {}
    };
  },
  watch: {
    initialSubTab(val) {
      if (val) this.subTab = val;
    },
    products: {
      immediate: true,
      handler(val) {
        if (!this.selectedInspectProduct && val.length > 0) {
          this.selectedInspectProduct = val[0];
        }
      }
    }
  },
  computed: {
    filteredProducts() {
      return this.products.filter(p => {
        if (this.searchText) {
          const q = this.searchText.toLowerCase();
          if (!(p.ten_san_pham || '').toLowerCase().includes(q) && !(p.ma_san_pham || '').toLowerCase().includes(q)) return false;
        }
        if (this.filterCat && p.danh_muc_id != this.filterCat) return false;
        if (this.filterBrand && p.thuong_hieu_id != this.filterBrand) return false;
        if (this.filterStatus !== '' && Number(p.trang_thai) !== Number(this.filterStatus)) return false;
        return true;
      });
    },
    totalStock() {
      return this.products.reduce((s, p) => s + this.getStock(p), 0);
    },
    lowStockCount() {
      return this.products.filter(p => this.getStock(p) < 10).length;
    },
    inactiveCount() {
      return this.products.filter(p => !p.trang_thai).length;
    },
    totalInventoryValue() {
      return this.products.reduce((s, p) => s + (this.getStock(p) * Number(p.gia_khuyen_mai || p.gia_goc || 0)), 0);
    }
  },
  methods: {
    getStock(p) {
      if (Array.isArray(p.variants) && p.variants.length > 0) {
        return p.variants.reduce((s, v) => s + Number(v.so_luong_ton || 0), 0);
      }
      return Number(p.so_luong_ton || 0);
    },
    openProductModal(p) {
      if (p) {
        this.prodForm = {
          id: p.id,
          ten_san_pham: p.ten_san_pham,
          ma_san_pham: p.ma_san_pham,
          danh_muc_id: p.danh_muc_id,
          thuong_hieu_id: p.thuong_hieu_id,
          gia_goc: p.gia_goc,
          gia_khuyen_mai: p.gia_khuyen_mai,
          so_luong_ton: this.getStock(p),
          anh_chinh: p.anh_chinh,
          mo_ta_ngan: p.mo_ta_ngan,
          cpu: p.specification?.cpu || '',
          ram: p.specification?.ram || ''
        };
      } else {
        this.prodForm = {
          danh_muc_id: this.categories[0]?.id || 1,
          thuong_hieu_id: this.brands[0]?.id || 1,
          gia_goc: 15000000,
          so_luong_ton: 10
        };
      }
      this.showProdModal = true;
    },
    async saveProduct() {
      const endpoint = this.prodForm.id ? '/admin/san-pham/update' : '/admin/san-pham/add-data';
      const res = await axios.post(apiUrl(endpoint), this.prodForm);
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.showProdModal = false;
        this.$emit('refresh');
      }
    },
    async toggleProductStatus(p) {
      const res = await axios.post(apiUrl('/admin/san-pham/change-status'), { id: p.id });
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.$emit('refresh');
      }
    },
    async deleteProduct(p) {
      if (!confirm(`Xóa sản phẩm "${p.ten_san_pham}"?`)) return;
      const res = await axios.post(apiUrl('/admin/san-pham/delete'), { id: p.id });
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.$emit('refresh');
      }
    },
    openCatModal(c) {
      this.catForm = c ? { ...c } : { ten_danh_muc: '', mo_ta: '' };
      this.showCatModal = true;
    },
    async saveCat() {
      const endpoint = this.catForm.id ? '/admin/danh-muc/update' : '/admin/danh-muc/add-data';
      const res = await axios.post(apiUrl(endpoint), this.catForm);
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.showCatModal = false;
        this.$emit('refresh');
      }
    },
    async toggleCatStatus(c) {
      await axios.post(apiUrl('/admin/danh-muc/change-status'), { id: c.id });
      this.$emit('refresh');
    },
    async deleteCat(c) {
      if (!confirm(`Xóa danh mục "${c.ten_danh_muc}"?`)) return;
      await axios.post(apiUrl('/admin/danh-muc/delete'), { id: c.id });
      this.$emit('refresh');
    },
    openBrandModal(b) {
      this.brandForm = b ? { ...b } : { ten_thuong_hieu: '', mo_ta: '' };
      this.showBrandModal = true;
    },
    async saveBrand() {
      const endpoint = this.brandForm.id ? '/admin/thuong-hieu/update' : '/admin/thuong-hieu/add-data';
      const res = await axios.post(apiUrl(endpoint), this.brandForm);
      if (res.data?.status) {
        this.$emit('notify', res.data.message);
        this.showBrandModal = false;
        this.$emit('refresh');
      }
    },
    async toggleBrandStatus(b) {
      await axios.post(apiUrl('/admin/thuong-hieu/change-status'), { id: b.id });
      this.$emit('refresh');
    },
    async deleteBrand(b) {
      if (!confirm(`Xóa thương hiệu "${b.ten_thuong_hieu}"?`)) return;
      await axios.post(apiUrl('/admin/thuong-hieu/delete'), { id: b.id });
      this.$emit('refresh');
    },
    formatCurrency(val) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
    }
  }
};
</script>

<style scoped>
.ea-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
}
.ea-card-blue {
  background: linear-gradient(135deg, #0b5ed7, #1d4ed8);
  border-radius: 16px;
}
.ea-kpi-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
}
.ea-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.ea-modal-card {
  background: #ffffff;
  border-radius: 16px;
  width: 100%;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
}
.cursor-pointer { cursor: pointer; }
</style>
