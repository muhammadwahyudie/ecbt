/* ======================================================================
   CBT Sekolah — Global Application JavaScript
   Helper konfirmasi SweetAlert2, toast, DataTable helper, utilities
   ====================================================================== */

/**
 * Toast Notification Helper (SweetAlert2)
 */
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  }
});

function showToast(icon, title) {
  Toast.fire({
    icon: icon,
    title: title
  });
}

/**
 * Konfirmasi Aksi dengan SweetAlert2
 */
function confirmAction(options) {
  const defaults = {
    title: 'Apakah Anda yakin?',
    text: 'Aksi ini tidak dapat dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#0E7C86',
    cancelButtonColor: '#52666F',
    confirmButtonText: 'Ya, Lanjutkan',
    cancelButtonText: 'Batal',
    reverseButtons: true
  };

  const config = Object.assign({}, defaults, options);
  return Swal.fire(config);
}

/**
 * Konfirmasi Hapus Data
 */
function confirmDelete(url, message = 'Data yang dihapus tidak dapat dikembalikan.') {
  confirmAction({
    title: 'Hapus Data?',
    text: message,
    confirmButtonColor: '#DC2626',
    confirmButtonText: 'Ya, Hapus'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = url;

      // Ambil nama field CSRF (default: csrf_test_name)
      const tokenName = document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name';

      // Ambil token hash dari cookie (paling akurat) atau meta tag
      let tokenHash = '';
      const cookieMatch = document.cookie.match(new RegExp('(^|; )csrf_cookie_name=([^;]+)'));
      if (cookieMatch) {
        tokenHash = decodeURIComponent(cookieMatch[2]);
      } else {
        tokenHash = document.querySelector('meta[name="csrf-token"]')?.content || '';
      }

      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = tokenName;
      csrfInput.value = tokenHash;

      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'DELETE';

      form.appendChild(csrfInput);
      form.appendChild(methodInput);
      document.body.appendChild(form);
      form.submit();
    }
  });
}

/**
 * Inisialisasi DataTable standar
 */
function initDataTable(selector, options = {}) {
  const defaults = {
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      infoEmpty: "Menampilkan 0 data",
      infoFiltered: "(disaring dari _MAX_ total data)",
      zeroRecords: "Tidak ada data yang ditemukan",
      paginate: {
        first: "Awal",
        last: "Akhir",
        next: "Berikutnya",
        previous: "Sebelumnya"
      }
    },
    pageLength: 10,
    responsive: true
  };

  return $(selector).DataTable(Object.assign({}, defaults, options));
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
  // Tooltip & popover bootstrap initialization if present
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }
});
