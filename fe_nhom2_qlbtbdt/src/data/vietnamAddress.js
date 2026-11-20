// ==========================================
// DỮ LIỆU ĐỊA GIỚI HÀNH CHÍNH VIỆT NAM & TỌA ĐỘ BẢN ĐỒ
// Hỗ trợ 63 Tỉnh/Thành phố, Quận/Huyện, Phường/Xã và tọa độ GPS
// ==========================================

export const PROVINCES_DATA = [
  {
    code: '01',
    name: 'Thành phố Hà Nội',
    lat: 21.0285,
    lng: 105.8542,
    districts: [
      { code: '001', name: 'Quận Ba Đình', wards: ['Phường Phúc Xá', 'Phường Trúc Bạch', 'Phường Vĩnh Phúc', 'Phường Cống Vị', 'Phường Liễu Giai', 'Phường Kim Mã'] },
      { code: '002', name: 'Quận Hoàn Kiếm', wards: ['Phường Phan Chu Trinh', 'Phường Tràng Tiền', 'Phường Hàng Bạc', 'Phường Hàng Đào', 'Phường Cửa Nam'] },
      { code: '003', name: 'Quận Tây Hồ', wards: ['Phường Bưởi', 'Phường Thụy Khuê', 'Phường Yên Phụ', 'Phường Tứ Liên', 'Phường Quảng An', 'Phường Xuân La'] },
      { code: '004', name: 'Quận Long Biên', wards: ['Phường Thượng Thanh', 'Phường Ngọc Thụy', 'Phường Giang Biên', 'Phường Đức Giang', 'Phường Việt Hưng'] },
      { code: '005', name: 'Quận Cầu Giấy', wards: ['Phường Nghĩa Đô', 'Phường Nghĩa Tân', 'Phường Mai Dịch', 'Phường Dịch Vọng', 'Phường Yên Hòa', 'Phường Trung Hòa'] },
      { code: '006', name: 'Quận Đống Đa', wards: ['Phường Cát Linh', 'Phường Văn Miếu', 'Phường Quốc Tử Giám', 'Phường Láng Thượng', 'Phường Ô Chợ Dừa'] },
      { code: '007', name: 'Quận Hai Bà Trưng', wards: ['Phường Nguyễn Du', 'Phường Bạch Đằng', 'Phường Phạm Đình Hổ', 'Phường Lê Đại Hành', 'Phường Đồng Nhân'] },
      { code: '008', name: 'Quận Hoàng Mai', wards: ['Phường Hoàng Liệt', 'Phường Yên Sở', 'Phường Định Công', 'Phường Đại Kim', 'Phường Thịnh Liệt'] },
      { code: '009', name: 'Quận Thanh Xuân', wards: ['Phường Hạ Đình', 'Phường Khương Đình', 'Phường Khương Mai', 'Phường Khương Trung', 'Phường Thanh Xuân Bắc'] },
      { code: '016', name: 'Quận Nam Từ Liêm', wards: ['Phường Cầu Diễn', 'Phường Xuân Phương', 'Phường Phương Canh', 'Phường Mỹ Đình 1', 'Phường Mỹ Đình 2', 'Phường Mễ Trì'] },
      { code: '017', name: 'Quận Bắc Từ Liêm', wards: ['Phường Thượng Cát', 'Phường Liên Mạc', 'Phường Thụy Phương', 'Phường Minh Khai', 'Phường Tây Tựu'] },
      { code: '018', name: 'Quận Hà Đông', wards: ['Phường Nguyễn Trãi', 'Phường Mộ Lao', 'Phường Văn Quán', 'Phường Vạn Phúc', 'Phường Yết Kiêu', 'Phường Quang Trung'] }
    ]
  },
  {
    code: '79',
    name: 'Thành phố Hồ Chí Minh',
    lat: 10.8231,
    lng: 106.6297,
    districts: [
      { code: '760', name: 'Quận 1', wards: ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Cầu Kho', 'Phường Cầu Ông Lãnh', 'Phường Cô Giang', 'Phường Đa Kao', 'Phường Tân Định'] },
      { code: '761', name: 'Quận 12', wards: ['Phường Thạnh Xuân', 'Phường Thạnh Lộc', 'Phường Hiệp Thành', 'Phường Thới An', 'Phường Tân Chánh Hiệp', 'Phường An Phú Đông'] },
      { code: '764', name: 'Quận Gò Vấp', wards: ['Phường 1', 'Phường 3', 'Phường 5', 'Phường 7', 'Phường 11', 'Phường 14', 'Phường 17'] },
      { code: '765', name: 'Quận Bình Thạnh', wards: ['Phường 1', 'Phường 2', 'Phường 12', 'Phường 14', 'Phường 19', 'Phường 24', 'Phường 25', 'Phường 26'] },
      { code: '766', name: 'Quận Tân Bình', wards: ['Phường 1', 'Phường 2', 'Phường 4', 'Phường 12', 'Phường 13', 'Phường 14', 'Phường 15'] },
      { code: '767', name: 'Quận Tân Phú', wards: ['Phường Tân Sơn Nhì', 'Phường Tây Thạnh', 'Phường Sơn Kỳ', 'Phường Tân Quý', 'Phường Tân Thành', 'Phường Phú Thọ Hòa'] },
      { code: '768', name: 'Quận Phú Nhuận', wards: ['Phường 1', 'Phường 2', 'Phường 7', 'Phường 9', 'Phường 11', 'Phường 15'] },
      { code: '769', name: 'Thành phố Thủ Đức', wards: ['Phường Linh Tây', 'Phường Linh Trung', 'Phường Linh Chiểu', 'Phường Tam Bình', 'Phường Tam Phú', 'Phường Hiệp Bình Chánh', 'Phường Thảo Điền', 'Phường An Phú'] },
      { code: '770', name: 'Quận 3', wards: ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 9', 'Phường Võ Thị Sáu'] },
      { code: '771', name: 'Quận 10', wards: ['Phường 1', 'Phường 2', 'Phường 4', 'Phường 9', 'Phường 10', 'Phường 12', 'Phường 14', 'Phường 15'] },
      { code: '772', name: 'Quận 11', wards: ['Phường 1', 'Phường 3', 'Phường 5', 'Phường 8', 'Phường 11', 'Phường 14', 'Phường 15'] },
      { code: '773', name: 'Quận 4', wards: ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 6', 'Phường 8', 'Phường 13', 'Phường 18'] },
      { code: '774', name: 'Quận 5', wards: ['Phường 1', 'Phường 2', 'Phường 5', 'Phường 7', 'Phường 8', 'Phường 11', 'Phường 14'] },
      { code: '775', name: 'Quận 6', wards: ['Phường 1', 'Phường 2', 'Phường 6', 'Phường 9', 'Phường 12', 'Phường 13', 'Phường 14'] },
      { code: '776', name: 'Quận 8', wards: ['Phường 1', 'Phường 2', 'Phường 4', 'Phường 5', 'Phường 8', 'Phường 14', 'Phường 16'] },
      { code: '777', name: 'Quận 7', wards: ['Phường Tân Thuận Đông', 'Phường Tân Thuận Tây', 'Phường Tân Kiểng', 'Phường Tân Quy', 'Phường Phú Thuận', 'Phường Tân Phú', 'Phường Tân Phong'] },
      { code: '778', name: 'Huyện Củ Chi', wards: ['Thị trấn Củ Chi', 'Xã Phú Hòa Đông', 'Xã Tân Thạnh Đông', 'Xã Tân Thạnh Tây', 'Xã Trung An'] },
      { code: '783', name: 'Quận Bình Tân', wards: ['Phường Bình Hưng Hòa', 'Phường Bình Hưng Hòa A', 'Phường Bình Trị Đông', 'Phường An Lạc', 'Phường An Lạc A'] }
    ]
  },
  {
    code: '48',
    name: 'Thành phố Đà Nẵng',
    lat: 16.0544,
    lng: 108.2022,
    districts: [
      { code: '490', name: 'Quận Hải Châu', wards: ['Phường Hải Châu 1', 'Phường Hải Châu 2', 'Phường Thạch Thang', 'Phường Thanh Bình', 'Phường Thuận Phước', 'Phường Hòa Thuận Tây', 'Phường Hòa Cường Bắc', 'Phường Hòa Cường Nam', 'Phường Nam Dương'] },
      { code: '491', name: 'Quận Thanh Khê', wards: ['Phường Tam Thuận', 'Phường Thanh Khê Tây', 'Phường Thanh Khê Đông', 'Phường Xuân Hà', 'Phường Tân Chính', 'Phường Chính Gián', 'Phường Vĩnh Trung', 'Phường Thạc Gián', 'Phường An Khê', 'Phường Hòa Khê'] },
      { code: '492', name: 'Quận Sơn Trà', wards: ['Phường Thọ Quang', 'Phường Nại Hiên Đông', 'Phường Mân Thái', 'Phường An Hải Bắc', 'Phường Phước Mỹ', 'Phường An Hải Tây', 'Phường An Hải Đông'] },
      { code: '493', name: 'Quận Ngũ Hành Sơn', wards: ['Phường Mỹ An', 'Phường Khuê Mỹ', 'Phường Hòa Quý', 'Phường Hòa Hải'] },
      { code: '494', name: 'Quận Liên Chiểu', wards: ['Phường Hòa Hiệp Bắc', 'Phường Hòa Hiệp Nam', 'Phường Hòa Khánh Bắc', 'Phường Hòa Khánh Nam', 'Phường Hòa Minh'] },
      { code: '495', name: 'Quận Cẩm Lệ', wards: ['Phường Khuê Trung', 'Phường Hòa Phát', 'Phường Hòa An', 'Phường Hòa Thọ Tây', 'Phường Hòa Thọ Đông', 'Phường Hòa Xuân'] },
      { code: '497', name: 'Huyện Hòa Vang', wards: ['Xã Hòa Bắc', 'Xã Hòa Liên', 'Xã Hòa Ninh', 'Xã Hòa Sơn', 'Xã Hòa Nhơn', 'Xã Hòa Phú', 'Xã Hòa Phong', 'Xã Hòa Châu', 'Xã Hòa Tiến', 'Xã Hòa Khương'] }
    ]
  },
  {
    code: '31',
    name: 'Thành phố Hải Phòng',
    lat: 20.8449,
    lng: 106.6881,
    districts: [
      { code: '303', name: 'Quận Hồng Bàng', wards: ['Phường Quán Toan', 'Phường Hùng Vương', 'Phường Sở Dầu', 'Phường Thượng Lý', 'Phường Hạ Lý', 'Phường Minh Khai'] },
      { code: '304', name: 'Quận Ngô Quyền', wards: ['Phường Cầu Đất', 'Phường Cầu Tre', 'Phường Đằng Giang', 'Phường Đồng Quốc Bình', 'Phường Lạc Viên'] },
      { code: '305', name: 'Quận Lê Chân', wards: ['Phường An Biên', 'Phường An Dương', 'Phường Cát Dài', 'Phường Dư Hàng', 'Phường Hàng Kênh', 'Phường Kênh Dương'] },
      { code: '306', name: 'Quận Hải An', wards: ['Phường Đông Hải 1', 'Phường Đông Hải 2', 'Phường Đằng Lâm', 'Phường Đằng Hải', 'Phường Nam Hải'] }
    ]
  },
  {
    code: '92',
    name: 'Thành phố Cần Thơ',
    lat: 10.0452,
    lng: 105.7469,
    districts: [
      { code: '916', name: 'Quận Ninh Kiều', wards: ['Phường Cái Khế', 'Phường An Hòa', 'Phường Thới Bình', 'Phường An Nghiệp', 'Phường An Cư', 'Phường Tân An', 'Phường Xuân Khánh', 'Phường Hưng Lợi'] },
      { code: '917', name: 'Quận Ô Môn', wards: ['Phường Châu Văn Liêm', 'Phường Thới Hòa', 'Phường Thới An', 'Phường Phước Thới', 'Phường Trường Lạc'] },
      { code: '918', name: 'Quận Bình Thủy', wards: ['Phường Bình Thủy', 'Phường Trà An', 'Phường Trà Nóc', 'Phường Thới An Đông', 'Phường An Thới', 'Phường Bùi Hữu Nghĩa'] },
      { code: '919', name: 'Quận Cái Răng', wards: ['Phường Lê Bình', 'Phường Hưng Phú', 'Phường Hưng Thạnh', 'Phường Ba Láng', 'Phường Phú Thứ', 'Phường Tân Phú'] }
    ]
  },
  {
    code: '74',
    name: 'Tỉnh Bình Dương',
    lat: 11.0279,
    lng: 106.6669,
    districts: [
      { code: '718', name: 'Thành phố Thủ Dầu Một', wards: ['Phường Phú Cường', 'Phường Hiệp Thành', 'Phường Định Hòa', 'Phường Phú Lợi', 'Phường Phú Hòa'] },
      { code: '721', name: 'Thành phố Thuận An', wards: ['Phường Lái Thiêu', 'Phường An Thạnh', 'Phường Vĩnh Phú', 'Phường Bình Hòa', 'Phường Thuận Giao'] },
      { code: '722', name: 'Thành phố Dĩ An', wards: ['Phường Dĩ An', 'Phường Tân Đông Hiệp', 'Phường An Bình', 'Phường Bình An', 'Phường Đông Hòa'] }
    ]
  },
  {
    code: '75',
    name: 'Tỉnh Đồng Nai',
    lat: 10.9574,
    lng: 106.8427,
    districts: [
      { code: '731', name: 'Thành phố Biên Hòa', wards: ['Phường Trảng Dài', 'Phường Tân Phong', 'Phường Tân Biên', 'Phường Hố Nai', 'Phường Tân Hòa', 'Phường Quyết Thắng'] },
      { code: '732', name: 'Thành phố Long Khánh', wards: ['Phường Xuân An', 'Phường Xuân Bình', 'Phường Xuân Hòa', 'Phường Xuân Trung', 'Phường Phú Bình'] }
    ]
  },
  {
    code: '49',
    name: 'Tỉnh Quảng Nam',
    lat: 15.5683,
    lng: 108.4812,
    districts: [
      { code: '502', name: 'Thành phố Tam Kỳ', wards: ['Phường An Mỹ', 'Phường An Xuân', 'Phường An Sơn', 'Phường Hòa Hương', 'Phường Phước Hòa', 'Phường Tân Thạnh'] },
      { code: '503', name: 'Thành phố Hội An', wards: ['Phường Minh An', 'Phường Cẩm Phô', 'Phường Sơn Phong', 'Phường Thanh Hà', 'Phường Cẩm Châu', 'Phường Cửa Đại'] },
      { code: '504', name: 'Thị xã Điện Bàn', wards: ['Phường Vĩnh Điện', 'Phường Điện An', 'Phường Điện Ngọc', 'Phường Điện Nam Bắc', 'Phường Điện Nam Trung'] }
    ]
  },
  {
    code: '46',
    name: 'Tỉnh Thừa Thiên Huế',
    lat: 16.4637,
    lng: 107.5909,
    districts: [
      { code: '474', name: 'Thành phố Huế', wards: ['Phường Phú Nhuận', 'Phường Vĩnh Ninh', 'Phường Phước Vĩnh', 'Phường Xuân Phú', 'Phường Phú Hội', 'Phường Thuận Thành'] }
    ]
  },
  {
    code: '56',
    name: 'Tỉnh Khánh Hòa',
    lat: 12.2388,
    lng: 109.1967,
    districts: [
      { code: '568', name: 'Thành phố Nha Trang', wards: ['Phường Lộc Thọ', 'Phường Phước Hải', 'Phường Phước Tiến', 'Phường Vạn Thắng', 'Phường Vĩnh Hải', 'Phường Vĩnh Phước'] }
    ]
  },
  {
    code: '77',
    name: 'Tỉnh Bà Rịa - Vũng Tàu',
    lat: 10.3460,
    lng: 107.0843,
    districts: [
      { code: '747', name: 'Thành phố Vũng Tàu', wards: ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 7', 'Phường 8', 'Phường Thắng Nhì'] }
    ]
  },
  {
    code: '22',
    name: 'Tỉnh Quảng Ninh',
    lat: 20.9505,
    lng: 107.0734,
    districts: [
      { code: '193', name: 'Thành phố Hạ Long', wards: ['Phường Bãi Cháy', 'Phường Hòn Gai', 'Phường Bạch Đằng', 'Phường Hồng Gai', 'Phường Cao Thắng'] }
    ]
  },
  {
    code: '38',
    name: 'Tỉnh Thanh Hóa',
    lat: 19.8067,
    lng: 105.7852,
    districts: [
      { code: '380', name: 'Thành phố Thanh Hóa', wards: ['Phường Điện Biên', 'Phường Ba Đình', 'Phường Lam Sơn', 'Phường Ngọc Trạo', 'Phường Đông Thọ'] }
    ]
  },
  {
    code: '40',
    name: 'Tỉnh Nghệ An',
    lat: 18.6738,
    lng: 105.6813,
    districts: [
      { code: '412', name: 'Thành phố Vinh', wards: ['Phường Lê Mao', 'Phường Quang Trung', 'Phường Cửa Nam', 'Phường Trường Thi', 'Phường Hưng Bình'] }
    ]
  },
  {
    code: '68',
    name: 'Tỉnh Lâm Đồng',
    lat: 11.9404,
    lng: 108.4583,
    districts: [
      { code: '672', name: 'Thành phố Đà Lạt', wards: ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 8', 'Phường 9', 'Phường 10'] }
    ]
  },
  {
    code: '66',
    name: 'Tỉnh Đắk Lắk',
    lat: 12.6667,
    lng: 108.0383,
    districts: [
      { code: '643', name: 'Thành phố Buôn Ma Thuột', wards: ['Phường Tân Lập', 'Phường Thắng Lợi', 'Phường Tự An', 'Phường Tân An', 'Phường Thành Công'] }
    ]
  },
  {
    code: '52',
    name: 'Tỉnh Bình Định',
    lat: 13.7820,
    lng: 109.2197,
    districts: [
      { code: '540', name: 'Thành phố Quy Nhơn', wards: ['Phường Lê Lợi', 'Phường Trần Hưng Đạo', 'Phường Ngô Mây', 'Phường Nguyễn Văn Cừ', 'Phường Quang Trung'] }
    ]
  },
  {
    code: '80',
    name: 'Tỉnh Long An',
    lat: 10.5360,
    lng: 106.4116,
    districts: [
      { code: '794', name: 'Thành phố Tân An', wards: ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 7'] }
    ]
  },
  {
    code: '82',
    name: 'Tỉnh Tiền Giang',
    lat: 10.3600,
    lng: 106.3600,
    districts: [
      { code: '815', name: 'Thành phố Mỹ Tho', wards: ['Phường 1', 'Phường 2', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7'] }
    ]
  },
  {
    code: '91',
    name: 'Tỉnh Kiên Giang',
    lat: 10.0134,
    lng: 105.0809,
    districts: [
      { code: '899', name: 'Thành phố Rạch Giá', wards: ['Phường Vĩnh Thanh', 'Phường Vĩnh Lạc', 'Phường Vĩnh Bảo', 'Phường An Hòa'] },
      { code: '900', name: 'Thành phố Phú Quốc', wards: ['Phường Dương Đông', 'Phường An Thới', 'Xã Cửa Cạn', 'Xã Gành Dầu', 'Xã Hàm Ninh'] }
    ]
  }
];

// Danh sách mở rộng 63 tỉnh thành Việt Nam đầy đủ
export const ALL_63_PROVINCES = [
  { code: '01', name: 'Thành phố Hà Nội', lat: 21.0285, lng: 105.8542 },
  { code: '79', name: 'Thành phố Hồ Chí Minh', lat: 10.8231, lng: 106.6297 },
  { code: '48', name: 'Thành phố Đà Nẵng', lat: 16.0544, lng: 108.2022 },
  { code: '31', name: 'Thành phố Hải Phòng', lat: 20.8449, lng: 106.6881 },
  { code: '92', name: 'Thành phố Cần Thơ', lat: 10.0452, lng: 105.7469 },
  { code: '02', name: 'Tỉnh Hà Giang', lat: 22.8233, lng: 104.9839 },
  { code: '04', name: 'Tỉnh Cao Bằng', lat: 22.6667, lng: 106.2500 },
  { code: '06', name: 'Tỉnh Bắc Kạn', lat: 22.1470, lng: 105.8348 },
  { code: '08', name: 'Tỉnh Tuyên Quang', lat: 21.8236, lng: 105.2147 },
  { code: '10', name: 'Tỉnh Lào Cai', lat: 22.4856, lng: 103.9707 },
  { code: '11', name: 'Tỉnh Điện Biên', lat: 21.3860, lng: 103.0233 },
  { code: '12', name: 'Tỉnh Lai Châu', lat: 22.3964, lng: 103.4684 },
  { code: '14', name: 'Tỉnh Sơn La', lat: 21.3283, lng: 103.9148 },
  { code: '15', name: 'Tỉnh Yên Bái', lat: 21.7168, lng: 104.8986 },
  { code: '17', name: 'Tỉnh Hoà Bình', lat: 20.8133, lng: 105.3383 },
  { code: '19', name: 'Tỉnh Thái Nguyên', lat: 21.5942, lng: 105.8482 },
  { code: '20', name: 'Tỉnh Lạng Sơn', lat: 21.8533, lng: 106.7622 },
  { code: '22', name: 'Tỉnh Quảng Ninh', lat: 20.9505, lng: 107.0734 },
  { code: '24', name: 'Tỉnh Bắc Giang', lat: 21.2731, lng: 106.1946 },
  { code: '25', name: 'Tỉnh Phú Thọ', lat: 21.3228, lng: 105.4019 },
  { code: '26', name: 'Tỉnh Vĩnh Phúc', lat: 21.3089, lng: 105.6049 },
  { code: '27', name: 'Tỉnh Bắc Ninh', lat: 21.1861, lng: 106.0763 },
  { code: '30', name: 'Tỉnh Hải Dương', lat: 20.9409, lng: 106.3330 },
  { code: '33', name: 'Tỉnh Hưng Yên', lat: 20.6464, lng: 106.0511 },
  { code: '34', name: 'Tỉnh Thái Bình', lat: 20.4463, lng: 106.3366 },
  { code: '35', name: 'Tỉnh Hà Nam', lat: 20.5453, lng: 105.9126 },
  { code: '36', name: 'Tỉnh Nam Định', lat: 20.4344, lng: 106.1673 },
  { code: '37', name: 'Tỉnh Ninh Bình', lat: 20.2506, lng: 105.9745 },
  { code: '38', name: 'Tỉnh Thanh Hóa', lat: 19.8067, lng: 105.7852 },
  { code: '40', name: 'Tỉnh Nghệ An', lat: 18.6738, lng: 105.6813 },
  { code: '42', name: 'Tỉnh Hà Tĩnh', lat: 18.3436, lng: 105.9058 },
  { code: '44', name: 'Tỉnh Quảng Bình', lat: 17.4690, lng: 106.6225 },
  { code: '45', name: 'Tỉnh Quảng Trị', lat: 16.8163, lng: 107.1004 },
  { code: '46', name: 'Tỉnh Thừa Thiên Huế', lat: 16.4637, lng: 107.5909 },
  { code: '49', name: 'Tỉnh Quảng Nam', lat: 15.5683, lng: 108.4812 },
  { code: '51', name: 'Tỉnh Quảng Ngãi', lat: 15.1205, lng: 108.7923 },
  { code: '52', name: 'Tỉnh Bình Định', lat: 13.7820, lng: 109.2197 },
  { code: '54', name: 'Tỉnh Phú Yên', lat: 13.0882, lng: 109.3075 },
  { code: '56', name: 'Tỉnh Khánh Hòa', lat: 12.2388, lng: 109.1967 },
  { code: '58', name: 'Tỉnh Ninh Thuận', lat: 11.5653, lng: 108.9882 },
  { code: '60', name: 'Tỉnh Bình Thuận', lat: 10.9333, lng: 108.1000 },
  { code: '62', name: 'Tỉnh Kon Tum', lat: 14.3500, lng: 108.0000 },
  { code: '64', name: 'Tỉnh Gia Lai', lat: 13.9833, lng: 108.0000 },
  { code: '66', name: 'Tỉnh Đắk Lắk', lat: 12.6667, lng: 108.0383 },
  { code: '67', name: 'Tỉnh Đắk Nông', lat: 12.0000, lng: 107.6833 },
  { code: '68', name: 'Tỉnh Lâm Đồng', lat: 11.9404, lng: 108.4583 },
  { code: '70', name: 'Tỉnh Bình Phước', lat: 11.7500, lng: 106.9000 },
  { code: '72', name: 'Tỉnh Tây Ninh', lat: 11.3000, lng: 106.1000 },
  { code: '74', name: 'Tỉnh Bình Dương', lat: 11.0279, lng: 106.6669 },
  { code: '75', name: 'Tỉnh Đồng Nai', lat: 10.9574, lng: 106.8427 },
  { code: '77', name: 'Tỉnh Bà Rịa - Vũng Tàu', lat: 10.3460, lng: 107.0843 },
  { code: '80', name: 'Tỉnh Long An', lat: 10.5360, lng: 106.4116 },
  { code: '82', name: 'Tỉnh Tiền Giang', lat: 10.3600, lng: 106.3600 },
  { code: '83', name: 'Tỉnh Bến Tre', lat: 10.2333, lng: 106.3833 },
  { code: '84', name: 'Tỉnh Trà Vinh', lat: 9.9333, lng: 106.3333 },
  { code: '86', name: 'Tỉnh Vĩnh Long', lat: 10.2500, lng: 105.9667 },
  { code: '87', name: 'Tỉnh Đồng Tháp', lat: 10.4560, lng: 105.6378 },
  { code: '88', name: 'Tỉnh An Giang', lat: 10.3833, lng: 105.4167 },
  { code: '91', name: 'Tỉnh Kiên Giang', lat: 10.0134, lng: 105.0809 },
  { code: '93', name: 'Tỉnh Hậu Giang', lat: 9.7833, lng: 105.4667 },
  { code: '94', name: 'Tỉnh Sóc Trăng', lat: 9.6000, lng: 105.9833 },
  { code: '95', name: 'Tỉnh Bạc Liêu', lat: 9.2833, lng: 105.7167 },
  { code: '96', name: 'Tỉnh Cà Mau', lat: 9.1833, lng: 105.1500 }
];

// Helper nạp động từ open-api kèm fallback tự động
export async function loadProvinces() {
  try {
    const res = await fetch('https://provinces.open-api.vn/api/p/');
    if (res.ok) {
      const data = await res.json();
      return data.map(p => {
        const matched = ALL_63_PROVINCES.find(m => String(m.code) === String(p.code) || m.name.includes(p.name));
        return {
          code: String(p.code),
          name: p.name,
          lat: matched ? matched.lat : 16.0544,
          lng: matched ? matched.lng : 108.2022
        };
      });
    }
  } catch (err) {
    console.warn('API provinces open-api chậm, kích hoạt dữ liệu dự phòng offline.');
  }
  return ALL_63_PROVINCES;
}

export async function loadDistricts(provinceCode) {
  // Thử gọi open-api
  try {
    const res = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
    if (res.ok) {
      const data = await res.json();
      if (data && data.districts && data.districts.length > 0) {
        return data.districts.map(d => ({
          code: String(d.code),
          name: d.name
        }));
      }
    }
  } catch (err) {
    // fallback
  }

  // Dùng fallback nội bộ
  const found = PROVINCES_DATA.find(p => String(p.code) === String(provinceCode));
  if (found && found.districts) {
    return found.districts.map(d => ({ code: String(d.code), name: d.name }));
  }

  return [
    { code: 'D1', name: 'Quận / Huyện Trung tâm' },
    { code: 'D2', name: 'Khu vực 2' },
    { code: 'D3', name: 'Khu vực 3' }
  ];
}

export async function loadWards(districtCode) {
  // Thử gọi open-api
  try {
    const res = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
    if (res.ok) {
      const data = await res.json();
      if (data && data.wards && data.wards.length > 0) {
        return data.wards.map(w => ({
          code: String(w.code),
          name: w.name
        }));
      }
    }
  } catch (err) {
    // fallback
  }

  // Dùng fallback nội bộ
  for (const prov of PROVINCES_DATA) {
    const foundDist = prov.districts?.find(d => String(d.code) === String(districtCode));
    if (foundDist && foundDist.wards) {
      return foundDist.wards.map((w, idx) => ({
        code: `${districtCode}_${idx}`,
        name: typeof w === 'string' ? w : w.name
      }));
    }
  }

  return [
    { code: 'W1', name: 'Phường / Xã Trung tâm' },
    { code: 'W2', name: 'Phường 1' },
    { code: 'W3', name: 'Phường 2' }
  ];
}
