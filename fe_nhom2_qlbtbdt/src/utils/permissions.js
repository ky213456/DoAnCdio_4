export const ALL_PERMISSIONS = [
  'UC09', 'UC10', 'UC11', 'UC12', 'UC13', 'UC14', 'UC15', 'UC16', 'UC19', 'UC22', 'UC23'
];

export const ADMIN_NAV_ROUTES = [
  { path: '/admin', perm: 'UC09' },
  { path: '/admin/products', perm: 'UC12' },
  { path: '/admin/categories', perm: 'UC22' },
  { path: '/admin/orders', perm: 'UC14' },
  { path: '/admin/serial', perm: 'UC23' },
  { path: '/admin/vouchers', perm: 'UC13' },
  { path: '/admin/reviews', perm: 'UC16' },
  { path: '/admin/customers', perm: 'UC15' },
  { path: '/admin/warranty', perm: 'UC19' },
  { path: '/admin/staff', perm: 'UC10' },
  { path: '/admin/permissions', perm: 'UC11' }
];

export function getStoredAdminUser() {
  const stored = localStorage.getItem('admin_user') || localStorage.getItem('customer_user');
  if (!stored) return null;
  try {
    return JSON.parse(stored);
  } catch (e) {
    return null;
  }
}

export function getUserPermissions(user) {
  if (!user || user.vai_tro === 'customer') return [];
  if (user.vai_tro === 'admin') return ALL_PERMISSIONS;
  if (Array.isArray(user.quyen_han) && user.quyen_han.length > 0) {
    return user.quyen_han;
  }
  return ['UC12', 'UC13', 'UC14', 'UC15', 'UC16'];
}

export function hasPermission(user, permCode) {
  if (!permCode) return true;
  if (!user || user.vai_tro === 'customer') return false;
  if (user.vai_tro === 'admin') return true;
  return getUserPermissions(user).includes(permCode);
}

export function getFirstAllowedAdminRoute(user) {
  const match = ADMIN_NAV_ROUTES.find(r => hasPermission(user, r.perm));
  return match ? match.path : '/';
}
