<template>
  <div :class="embedded ? 'serial-embedded' : 'serial-layout'">
    <!-- ========================================== -->
    <!-- 1. THANH ĐIỀU HƯỚNG TRÁI (ADMIN SIDEBAR)    -->
    <!-- ========================================== -->
    <aside v-if="!embedded" class="admin-sidebar shadow-sm">
      <div class="sidebar-brand">
        <router-link to="/admin" class="d-flex align-items-center text-decoration-none">
          <div class="bg-primary text-white p-2 rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm">
            <i class="bi bi-cpu-fill fs-5"></i>
          </div>
          <div>
            <div class="brand-title">TECHMART ADMIN</div>
            <div class="brand-sub">Hệ Thống Quản Trị BI & Kho</div>
          </div>
        </router-link>
      </div>

      <div class="sidebar-menu">
        <div class="menu-label">TỔNG QUAN & BÁO CÁO</div>
        <router-link to="/admin" class="menu-item">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard Quản Trị
        </router-link>
        <router-link to="/admin" class="menu-item">
          <i class="bi bi-robot me-2"></i> Giám Sát AI RAG
        </router-link>

        <div class="menu-label">VẬN HÀNH & KHO HÀNG</div>
        <router-link to="/admin" class="menu-item">
          <i class="bi bi-bag-check me-2"></i> Quản Lý Đơn Hàng
        </router-link>
        <router-link to="/admin/serial" class="menu-item active">
          <i class="bi bi-upc-scan me-2"></i> Kho Serial & IMEI
          <span v-if="stats.trong_kho > 0" class="badge bg-success ms-auto font-monospace">{{ stats.trong_kho }}</span>
        </router-link>
        <router-link to="/admin" class="menu-item">
          <i class="bi bi-cash-stack me-2"></i> Đối Soát PayOS
        </router-link>

        <div class="menu-label">HỆ THỐNG</div>
        <router-link to="/" class="menu-item text-info">
          <i class="bi bi-shop me-2"></i> Cửa Hàng TechMart
        </router-link>
      </div>

      <div class="sidebar-footer p-3 border-top border-secondary border-opacity-25 mt-auto">
        <div class="d-flex align-items-center">
          <div class="avatar-circle bg-primary text-white fw-bold me-2">
            {{ adminInitial }}
          </div>
          <div class="overflow-hidden">
            <div class="text-white small fw-bold text-truncate">{{ adminName }}</div>
            <div class="text-secondary text-truncate" style="font-size: 11px;">Quản Trị Viên Kho Hàng</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- ========================================== -->
    <!-- 2. KHU VỰC NỘI DUNG CHÍNH (MAIN CONTENT)    -->
    <!-- ========================================== -->
    <main :class="embedded ? 'serial-main-embedded' : 'serial-main'">
      <!-- TOPBAR -->
      <header v-if="!embedded" class="serial-topbar shadow-sm">
        <div class="d-flex align-items-center">
          <h1 class="h5 fw-bold mb-0 text-dark">
            <i class="bi bi-upc-scan text-primary me-2"></i>
            Quản Lý Kho Serial & IMEI Độc Bản (UC08 - FIFO)
          </h1>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-3 small">
            Định danh từng thiết bị vật lý
          </span>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm" @click="loadData(true)" :disabled="isLoading">
            <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin-anim': isLoading }"></i>
            Đồng bộ kho
          </button>
          <button class="btn btn-primary btn-sm px-3 shadow-sm" @click="openBatchImportModal">
            <i class="bi bi-plus-circle me-1"></i> + Nhập Lô Serial Mới
          </button>
        </div>
      </header>

      <!-- CONTENT BODY -->
      <div :class="embedded ? 'serial-content-embedded' : 'serial-content'">
        <div v-if="embedded" class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <div class="text-primary fw-bold small mb-1">QUẢN LÝ KHO ĐỊNH DANH TỪNG THIẾT BỊ VẬT LÝ</div>
            <h2 class="fw-bold text-dark mb-1">Kho Serial & IMEI FIFO (UC23)</h2>
            <p class="text-muted small mb-0">Nhập lô Serial/IMEI vào kho, kiểm tra trạng thái và gán FIFO cho đơn hàng.</p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm bg-white" @click="loadData(true)" :disabled="isLoading">
              <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin-anim': isLoading }"></i> Đồng bộ kho
            </button>
            <button class="btn btn-primary btn-sm px-3 fw-bold" @click="openBatchImportModal">
              <i class="bi bi-plus-lg me-1"></i> Nhập lô Serial/IMEI mới
            </button>
          </div>
        </div>
        <!-- Toast Thông báo -->
        <div
          v-if="alertMessage"
          :class="['alert', 'alert-' + alertType, 'alert-dismissible', 'fade', 'show', 'shadow-sm', 'd-flex', 'align-items-center', 'mb-4']"
          role="alert"
        >
          <i class="bi bi-info-circle-fill me-2 fs-5"></i>
          <div>{{ alertMessage }}</div>
          <button type="button" class="btn-close ms-auto" @click="alertMessage = ''"></button>
        </div>

        <!-- 4 Thẻ KPI Kho Serial -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 kpi-card bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-muted small fw-semibold text-uppercase">TỔNG MÃ SERIAL</div>
                  <div class="fs-3 fw-bold text-dark mt-1">{{ stats.total }}</div>
                  <div class="text-muted small">Đang quản lý hệ thống</div>
                </div>
                <div class="kpi-icon bg-secondary-subtle text-secondary rounded-3 p-3">
                  <i class="bi bi-boxes fs-4"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 kpi-card bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-success small fw-semibold text-uppercase">SẴN SÀNG XUẤT BÁN</div>
                  <div class="fs-3 fw-bold text-success mt-1">{{ stats.trong_kho }}</div>
                  <div class="text-success small">Có thể gán đơn hàng (FIFO)</div>
                </div>
                <div class="kpi-icon bg-success-subtle text-success rounded-3 p-3">
                  <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 kpi-card bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-primary small fw-semibold text-uppercase">ĐÃ BÁN GÁN ĐƠN</div>
                  <div class="fs-3 fw-bold text-primary mt-1">{{ stats.da_ban }}</div>
                  <div class="text-primary small">Đã kích hoạt bảo hành</div>
                </div>
                <div class="kpi-icon bg-primary-subtle text-primary rounded-3 p-3">
                  <i class="bi bi-truck fs-4"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 kpi-card bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-danger small fw-semibold text-uppercase">MÁY LỖI / RMA</div>
                  <div class="fs-3 fw-bold text-danger mt-1">{{ stats.loi }}</div>
                  <div class="text-danger small">Chờ bảo hành / đổi mới</div>
                </div>
                <div class="kpi-icon bg-danger-subtle text-danger rounded-3 p-3">
                  <i class="bi bi-shield-x fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- BẢNG DỮ LIỆU KHO SERIAL & IMEI -->
        <div class="card border-0 shadow-sm bg-white p-4">
          <!-- Hàng tiêu đề & Bộ lọc -->
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
              <h2 class="h6 fw-bold text-dark mb-1">
                <i class="bi bi-list-task text-primary me-2"></i>
                Danh Sách Từng Chiếc Máy Trong Kho
              </h2>
              <p class="text-muted small mb-0">
                Tìm kiếm, lọc trạng thái, theo dõi hạn bảo hành điện tử và đổi trạng thái từng mã serial.
              </p>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-outline-success btn-sm" @click="exportCSV" :disabled="serialsList.length === 0">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Xuất File CSV
              </button>
            </div>
          </div>

          <!-- Bộ lọc trạng thái, lọc biến thể và tìm kiếm -->
          <div class="row g-2 mb-3">
            <div class="col-md-3">
              <select v-model="filterStatus" @change="applyFilters" class="form-select form-select-sm">
                <option value="ALL">-- Tất cả trạng thái --</option>
                <option value="trong_kho">Sẵn sàng xuất bán (Trong kho)</option>
                <option value="da_ban">Đã xuất bán theo đơn</option>
                <option value="loi">Máy lỗi / RMA</option>
                <option value="giu_cho">Đang giữ chỗ</option>
              </select>
            </div>

            <div class="col-md-3">
              <select v-model="filterVariant" @change="applyFilters" class="form-select form-select-sm">
                <option value="ALL">-- Tất cả dòng sản phẩm --</option>
                <option v-for="v in variants" :key="v.id" :value="v.id">
                  {{ v.ten_san_pham }} - {{ v.ten_bien_the }}
                </option>
              </select>
            </div>

            <div class="col-md-6">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input
                  type="text"
                  v-model="searchQuery"
                  @input="applyFilters"
                  placeholder="Tìm theo Mã Serial (S/N), IMEI, Tên máy, Phân loại, Mã đơn hàng..."
                  class="form-control"
                />
                <button v-if="searchQuery" class="btn btn-outline-secondary" @click="searchQuery = ''; applyFilters()">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Bảng danh sách serial -->
          <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th style="width: 50px;">STT</th>
                  <th>Mã Serial (S/N)</th>
                  <th>Mã IMEI</th>
                  <th>Thiết Bị & Cấu Hình</th>
                  <th class="text-center">Trạng Thái</th>
                  <th>Ngày Nhập Kho</th>
                  <th>Đơn Hàng Gán</th>
                  <th>Hạn Bảo Hành Điện Tử</th>
                  <th class="text-center" style="width: 140px;">Thao Tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoading">
                  <td colspan="9" class="text-center py-4 text-muted">
                    <span class="spinner-border spinner-border-sm me-2 text-primary"></span>
                    Đang tải dữ liệu kho Serial & IMEI...
                  </td>
                </tr>

                <tr v-else-if="filteredSerials.length === 0">
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                    Không tìm thấy mã Serial nào phù hợp với bộ lọc hiện tại.
                  </td>
                </tr>

                <tr v-for="(ser, idx) in filteredSerials" :key="ser.id" :class="{ 'table-danger': ser.trang_thai === 'loi' }">
                  <td class="text-muted small">{{ idx + 1 }}</td>
                  <td>
                    <span class="font-monospace fw-bold text-primary">{{ ser.so_serial }}</span>
                  </td>
                  <td>
                    <span class="font-monospace small text-muted">{{ ser.imei || '—' }}</span>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark small">{{ ser.ten_san_pham }}</div>
                    <span class="text-muted" style="font-size: 11px;">{{ ser.ten_bien_the || ser.sku }}</span>
                  </td>
                  <td class="text-center">
                    <span :class="['badge', getStatusBadge(ser.trang_thai)]">
                      {{ getStatusLabel(ser.trang_thai) }}
                    </span>
                  </td>
                  <td class="small text-muted">{{ formatDate(ser.ngay_nhap_kho) }}</td>
                  <td>
                    <span v-if="ser.ma_don_hang" class="badge bg-secondary font-monospace">
                      #{{ ser.ma_don_hang }}
                    </span>
                    <span v-else class="text-muted small">Chưa xuất</span>
                  </td>
                  <td>
                    <div v-if="ser.han_bao_hanh" class="small">
                      <span class="text-success fw-bold">{{ formatDate(ser.han_bao_hanh) }}</span>
                    </div>
                    <span v-else class="text-muted small">—</span>
                  </td>
                  <td class="text-center">
                    <div class="btn-group btn-group-sm">
                      <button
                        v-if="ser.trang_thai === 'trong_kho'"
                        class="btn btn-outline-danger btn-xs py-0 px-2"
                        style="font-size: 11px;"
                        @click="quickChangeStatus(ser, 'loi')"
                        title="Đánh dấu máy lỗi / RMA"
                      >
                        Báo lỗi
                      </button>
                      <button
                        v-if="ser.trang_thai === 'loi'"
                        class="btn btn-outline-success btn-xs py-0 px-2"
                        style="font-size: 11px;"
                        @click="quickChangeStatus(ser, 'trong_kho')"
                        title="Đã sửa chữa, đưa máy lại kho"
                      >
                        Hoàn kho
                      </button>
                      <button
                        class="btn btn-outline-secondary btn-xs py-0 px-2"
                        style="font-size: 11px;"
                        @click="copySerial(ser.so_serial)"
                        title="Sao chép mã Serial"
                      >
                        <i class="bi bi-copy"></i>
                      </button>
                      <button
                        class="btn btn-outline-primary btn-xs py-0 px-2"
                        style="font-size: 11px;"
                        @click="openDetailModal(ser)"
                        title="Xem chi tiết thiết bị"
                      >
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Phân trang / Thống kê số lượng bản ghi hiển thị -->
          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top small text-muted">
            <div>
              Hiển thị <strong>{{ filteredSerials.length }}</strong> / <strong>{{ serialsList.length }}</strong> mã máy trong kho
            </div>
            <div class="font-monospace">
              TECHMART INVENTORY SERIAL CONTROL
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ========================================== -->
    <!-- 3. MODAL NHẬP LÔ SERIAL MỚI (UC08)         -->
    <!-- ========================================== -->
    <div
      v-if="showBatchModal"
      class="modal fade show d-block"
      style="background: rgba(0,0,0,0.65);"
      tabindex="-1"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-primary text-white py-3">
            <h5 class="modal-title h6 fw-bold mb-0">
              <i class="bi bi-plus-circle me-2"></i> NHẬP LÔ MÃ SERIAL & IMEI MỚI VÀO KHO (FIFO)
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showBatchModal = false"></button>
          </div>

          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold small">Chọn Biến Thể Sản Phẩm Nhập Kho (*):</label>
              <select v-model="batchForm.bien_the_id" class="form-select form-select-sm" @change="onBatchVariantChange">
                <option value="">-- Chọn máy cần nhập lô Serial --</option>
                <option v-for="v in variants" :key="v.id" :value="v.id">
                  {{ v.ten_san_pham }} - {{ v.ten_bien_the }} (Tồn kho hiện tại: {{ v.so_luong_ton }})
                </option>
              </select>
            </div>

            <!-- Tabs chọn phương thức nhập -->
            <ul class="nav nav-pills nav-fill mb-3">
              <li class="nav-item">
                <button
                  class="nav-link btn-sm"
                  :class="{ active: batchForm.mode === 'auto' }"
                  @click="batchForm.mode = 'auto'"
                >
                  <i class="bi bi-magic me-1"></i> 1. Tự động sinh mã hàng loạt
                </button>
              </li>
              <li class="nav-item">
                <button
                  class="nav-link btn-sm"
                  :class="{ active: batchForm.mode === 'manual' }"
                  @click="batchForm.mode = 'manual'"
                >
                  <i class="bi bi-textarea-t me-1"></i> 2. Dán danh sách mã thủ công / Quét mã vạch
                </button>
              </li>
            </ul>

            <!-- Khung sinh mã tự động -->
            <div v-if="batchForm.mode === 'auto'" class="p-3 bg-light rounded border mb-3">
              <div class="row g-2">
                <div class="col-md-5">
                  <label class="small text-muted fw-semibold">Tiền tố mã (Prefix):</label>
                  <input type="text" v-model="batchForm.prefix" class="form-control form-control-sm font-monospace" placeholder="VD: TM-ROG26-" />
                </div>
                <div class="col-md-4">
                  <label class="small text-muted fw-semibold">Số bắt đầu:</label>
                  <input type="number" v-model.number="batchForm.startNum" class="form-control form-control-sm" min="1" />
                </div>
                <div class="col-md-3">
                  <label class="small text-muted fw-semibold">Số lượng máy:</label>
                  <input type="number" v-model.number="batchForm.count" class="form-control form-control-sm" min="1" max="100" />
                </div>
              </div>
              <div class="mt-2 small text-muted">
                Dải mã sẽ sinh: <span class="badge bg-secondary font-monospace">{{ batchForm.prefix }}{{ String(batchForm.startNum).padStart(4, '0') }}</span> đến <span class="badge bg-secondary font-monospace">{{ batchForm.prefix }}{{ String(batchForm.startNum + batchForm.count - 1).padStart(4, '0') }}</span>
              </div>
            </div>

            <!-- Khung dán danh sách thủ công -->
            <div v-else class="mb-3">
              <label class="small text-muted fw-semibold">Dán danh sách mã Serial (Mỗi dòng một mã):</label>
              <textarea
                v-model="batchForm.manualSerials"
                rows="5"
                placeholder="TM-ROG26-0101&#10;TM-ROG26-0102&#10;TM-ROG26-0103"
                class="form-control form-control-sm font-monospace"
              ></textarea>
            </div>

            <!-- Ghi chú nhập lô -->
            <div class="mb-3">
              <label class="form-label small fw-semibold">Ghi chú đợt nhập:</label>
              <input type="text" v-model="batchForm.ghi_chu" placeholder="Ví dụ: Nhập kho chính hãng đợt 2 ASUS Việt Nam" class="form-control form-control-sm" />
            </div>

            <div v-if="batchError" class="alert alert-danger py-2 small">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ batchError }}
            </div>
          </div>

          <div class="modal-footer bg-light py-2">
            <button type="button" class="btn btn-secondary btn-sm" @click="showBatchModal = false" :disabled="isSubmittingBatch">Hủy</button>
            <button type="button" class="btn btn-primary btn-sm" @click="submitBatchImport" :disabled="isSubmittingBatch">
              <span v-if="isSubmittingBatch" class="spinner-border spinner-border-sm me-1"></span>
              <i v-else class="bi bi-check-circle me-1"></i>
              Xác nhận nhập kho
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. MODAL CHI TIẾT SERIAL & LỊCH SỬ THIẾT BỊ -->
    <!-- ========================================== -->
    <div
      v-if="selectedDetail"
      class="modal fade show d-block"
      style="background: rgba(0,0,0,0.65);"
      tabindex="-1"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-dark text-white py-3">
            <h5 class="modal-title h6 fw-bold mb-0">
              <i class="bi bi-qr-code-scan me-2 text-warning"></i>
              Chi Tiết Thiết Bị: {{ selectedDetail.so_serial }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="selectedDetail = null"></button>
          </div>
          <div class="modal-body p-4">
            <div class="text-center p-3 bg-light rounded border mb-3">
              <div class="font-monospace fs-4 fw-bold text-primary">{{ selectedDetail.so_serial }}</div>
              <div class="text-muted small">IMEI: {{ selectedDetail.imei || 'Không áp dụng' }}</div>
              <span :class="['badge mt-2', getStatusBadge(selectedDetail.trang_thai)]">
                {{ getStatusLabel(selectedDetail.trang_thai) }}
              </span>
            </div>

            <div class="small">
              <p class="mb-2"><strong>Sản phẩm:</strong> {{ selectedDetail.ten_san_pham }}</p>
              <p class="mb-2"><strong>Biến thể / SKU:</strong> {{ selectedDetail.ten_bien_the || selectedDetail.sku }}</p>
              <p class="mb-2"><strong>Ngày nhập kho:</strong> {{ formatDate(selectedDetail.ngay_nhap_kho) }}</p>
              <p class="mb-2"><strong>Mã đơn hàng gán:</strong> {{ selectedDetail.ma_don_hang ? '#' + selectedDetail.ma_don_hang : 'Chưa gán đơn (Còn trong kho)' }}</p>
              <p class="mb-2"><strong>Hạn bảo hành điện tử:</strong> {{ selectedDetail.han_bao_hanh ? formatDate(selectedDetail.han_bao_hanh) : 'Chưa kích hoạt' }}</p>
              <p class="mb-0"><strong>Ghi chú nhập:</strong> {{ selectedDetail.ghi_chu || 'Không có ghi chú' }}</p>
            </div>
          </div>
          <div class="modal-footer bg-light py-2">
            <button type="button" class="btn btn-secondary btn-sm" @click="selectedDetail = null">Đóng</button>
            <button type="button" class="btn btn-outline-primary btn-sm" @click="copySerial(selectedDetail.so_serial)">
              <i class="bi bi-copy me-1"></i> Sao chép mã
            </button>
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
  name: 'SerialManagementPage',
  props: {
    embedded: { type: Boolean, default: false }
  },
  data() {
    return {
      isLoading: false,
      alertMessage: '',
      alertType: 'success',

      // Dữ liệu kho
      serialsList: [],
      filteredSerials: [],
      variants: [],
      stats: {
        total: 0,
        trong_kho: 0,
        da_ban: 0,
        giu_cho: 0,
        loi: 0
      },

      // Bộ lọc
      filterStatus: 'ALL',
      filterVariant: 'ALL',
      searchQuery: '',

      // Modal Nhập Lô
      showBatchModal: false,
      isSubmittingBatch: false,
      batchError: '',
      batchForm: {
        bien_the_id: '',
        mode: 'auto',
        prefix: 'TM-ROG26-',
        startNum: 101,
        count: 5,
        manualSerials: '',
        ghi_chu: ''
      },

      // Modal chi tiết
      selectedDetail: null
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
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    // 1. Tải toàn bộ dữ liệu kho Serial từ API
    async loadData(isManual = false) {
      this.isLoading = true;
      try {
        const res = await axios.get(apiUrl('admin/serial/get-data'));
        if (res.data && res.data.status) {
          this.serialsList = res.data.data || [];
          this.stats = res.data.stats || { total: 0, trong_kho: 0, da_ban: 0, giu_cho: 0, loi: 0 };
          this.variants = res.data.variants || [];
          this.applyFilters();

          if (isManual) {
            this.showAlert('Đã đồng bộ dữ liệu kho Serial & IMEI thời gian thực thành công!', 'success');
          }
        } else {
          this.showAlert(res.data.message || 'Không thể tải dữ liệu kho', 'danger');
        }
      } catch (err) {
        console.error('Lỗi khi tải kho serial:', err);
        if (isManual) {
          this.showAlert('Không thể kết nối đến máy chủ Backend Laravel!', 'danger');
        }
      } finally {
        this.isLoading = false;
      }
    },

    // 2. Lọc danh sách Serial
    applyFilters() {
      let list = this.serialsList;

      // Lọc trạng thái
      if (this.filterStatus !== 'ALL') {
        list = list.filter(s => s.trang_thai === this.filterStatus);
      }

      // Lọc biến thể
      if (this.filterVariant !== 'ALL') {
        list = list.filter(s => s.bien_the_id === Number(this.filterVariant));
      }

      // Lọc từ khóa tìm kiếm
      if (this.searchQuery.trim() !== '') {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(s => {
          const serial = String(s.so_serial || '').toLowerCase();
          const imei = String(s.imei || '').toLowerCase();
          const sp = String(s.ten_san_pham || '').toLowerCase();
          const bt = String(s.ten_bien_the || '').toLowerCase();
          const don = String(s.ma_don_hang || '').toLowerCase();
          return serial.includes(q) || imei.includes(q) || sp.includes(q) || bt.includes(q) || don.includes(q);
        });
      }

      this.filteredSerials = list;
    },

    // 3. Mở Modal Nhập Lô
    openBatchImportModal() {
      this.batchForm = {
        bien_the_id: this.variants.length > 0 ? this.variants[0].id : '',
        mode: 'auto',
        prefix: 'TM-ROG26-',
        startNum: 101,
        count: 5,
        manualSerials: '',
        ghi_chu: ''
      };
      if (this.batchForm.bien_the_id) {
        this.onBatchVariantChange();
      }
      this.batchError = '';
      this.showBatchModal = true;
    },

    onBatchVariantChange() {
      const v = this.variants.find(x => x.id === this.batchForm.bien_the_id);
      if (v) {
        const prefixClean = (v.sku || 'TECH')
          .replace(/[^A-Za-z0-9]/g, '')
          .toUpperCase()
          .slice(0, 8);
        this.batchForm.prefix = `TM-${prefixClean}-`;
      }
    },

    // 4. Gửi yêu cầu nhập lô
    async submitBatchImport() {
      this.batchError = '';
      if (!this.batchForm.bien_the_id) {
        this.batchError = 'Vui lòng chọn biến thể sản phẩm!';
        return;
      }

      let serialsList = [];
      if (this.batchForm.mode === 'auto') {
        const count = parseInt(this.batchForm.count) || 0;
        const start = parseInt(this.batchForm.startNum) || 1;
        const prefix = (this.batchForm.prefix || 'TM-').trim();
        if (count <= 0 || count > 100) {
          this.batchError = 'Số lượng máy nhập phải từ 1 đến 100!';
          return;
        }
        for (let i = 0; i < count; i++) {
          const numStr = String(start + i).padStart(4, '0');
          serialsList.push(`${prefix}${numStr}`);
        }
      } else {
        serialsList = (this.batchForm.manualSerials || '')
          .split('\n')
          .map(s => s.trim())
          .filter(s => s.length > 0);
        if (serialsList.length === 0) {
          this.batchError = 'Vui lòng nhập hoặc quét ít nhất 1 mã serial!';
          return;
        }
      }

      this.isSubmittingBatch = true;
      try {
        const res = await axios.post(apiUrl('admin/serial/nhap-lo'), {
          bien_the_id: this.batchForm.bien_the_id,
          serials: serialsList,
          ghi_chu: this.batchForm.ghi_chu
        });
        if (res.data && res.data.status) {
          this.showAlert(res.data.message || `Đã nhập thành công ${res.data.count} mã Serial vào kho!`, 'success');
          this.showBatchModal = false;
          await this.loadData();
        } else {
          this.batchError = res.data.message || 'Lỗi khi nhập lô serial!';
        }
      } catch (err) {
        this.batchError = err?.response?.data?.message || 'Lỗi kết nối máy chủ khi nhập lô serial!';
      } finally {
        this.isSubmittingBatch = false;
      }
    },

    // 5. Đổi trạng thái nhanh
    async quickChangeStatus(ser, newStatus) {
      try {
        const res = await axios.post(apiUrl('admin/serial/change-status'), {
          serial_id: ser.id,
          trang_thai: newStatus
        });
        if (res.data && res.data.status) {
          this.showAlert(`Đã chuyển trạng thái mã ${ser.so_serial} sang "${newStatus}"!`, 'success');
          await this.loadData();
        } else {
          this.showAlert(res.data.message || 'Không thể đổi trạng thái', 'danger');
        }
      } catch (err) {
        this.showAlert(err?.response?.data?.message || 'Lỗi khi đổi trạng thái serial', 'danger');
      }
    },

    // 6. Sao chép Serial
    copySerial(code) {
      if (!code) return;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code);
        this.showAlert(`Đã copy mã Serial: ${code}`, 'info');
      } else {
        this.showAlert(`Mã Serial: ${code}`, 'info');
      }
    },

    openDetailModal(ser) {
      this.selectedDetail = ser;
    },

    // 7. Xuất file CSV
    exportCSV() {
      if (this.filteredSerials.length === 0) {
        this.showAlert('Không có dữ liệu serial để xuất file!', 'warning');
        return;
      }
      let csv = 'STT,Ma_Serial,IMEI,San_Pham,Bien_The,Trang_Thai,Ngay_Nhap,Ma_Don_Hang,Han_Bao_Hanh\n';
      this.filteredSerials.forEach((s, i) => {
        csv += `"${i + 1}","${s.so_serial}","${s.imei || ''}","${s.ten_san_pham}","${s.ten_bien_the || s.sku || ''}","${s.trang_thai}","${s.ngay_nhap_kho || ''}","${s.ma_don_hang || ''}","${s.han_bao_hanh || ''}"\n`;
      });
      const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.setAttribute('href', url);
      link.setAttribute('download', `Kho_Serial_${new Date().toISOString().slice(0, 10)}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      this.showAlert('Đã xuất báo cáo kho Serial thành công (file CSV)!', 'success');
    },

    // Helpers
    showAlert(msg, type = 'success') {
      this.alertMessage = msg;
      this.alertType = type;
      setTimeout(() => {
        if (this.alertMessage === msg) {
          this.alertMessage = '';
        }
      }, 5000);
    },

    getStatusBadge(st) {
      const map = {
        trong_kho: 'bg-success',
        da_ban: 'bg-primary',
        giu_cho: 'bg-warning text-dark',
        loi: 'bg-danger'
      };
      return map[st] || 'bg-secondary';
    },

    getStatusLabel(st) {
      const map = {
        trong_kho: 'Trong kho (Sẵn sàng)',
        da_ban: 'Đã xuất bán',
        giu_cho: 'Đang giữ chỗ',
        loi: 'Máy lỗi / RMA'
      };
      return map[st] || st;
    },

    formatDate(d) {
      if (!d) return '--/--/----';
      try {
        const date = new Date(d);
        return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
      } catch (e) {
        return d;
      }
    }
  }
}
</script>

<style scoped>
.serial-layout {
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

.avatar-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

/* Main Content Area */
.serial-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  min-width: 0;
}

.serial-topbar {
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

.serial-content {
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

.kpi-icon {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
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
