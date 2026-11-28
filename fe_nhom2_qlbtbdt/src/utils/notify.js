import { toast } from 'vue3-toastify';
import Swal from 'sweetalert2';

export { toast };

export async function confirmDialog(text, title = 'Xác nhận thao tác', confirmButtonText = 'Đồng ý', icon = 'question') {
  const result = await Swal.fire({
    title,
    text,
    icon,
    showCancelButton: true,
    confirmButtonColor: '#2563eb',
    cancelButtonColor: '#64748b',
    confirmButtonText,
    cancelButtonText: 'Hủy bỏ',
    reverseButtons: true,
    customClass: {
      popup: 'rounded-4 shadow-lg',
      confirmButton: 'px-4 py-2 fw-bold rounded-3',
      cancelButton: 'px-4 py-2 fw-semibold rounded-3'
    }
  });
  return result.isConfirmed;
}
