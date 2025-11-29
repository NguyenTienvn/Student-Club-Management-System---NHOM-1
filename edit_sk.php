<?php
session_start();
require_once(__DIR__ . "/assets/database/connect.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để chỉnh sửa sự kiện");
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    die("ID sự kiện không hợp lệ");
}

// Lấy thông tin sự kiện
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    die("Không tìm thấy sự kiện");
}

// Kiểm tra quyền chỉnh sửa (người tạo hoặc admin)
$can_edit = ($event['created_by'] == $_SESSION['user_id']) || ($_SESSION['role'] ?? '') == 'admin';
if (!$can_edit) {
    die("Bạn không có quyền chỉnh sửa sự kiện này");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sự kiện</title>
    <link rel="stylesheet" href="assets/css/edit_sk.css?v=<?= time() ?>">
</head>
<body>
    <div class="edit-event-modal" id="editEventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Chỉnh sửa sự kiện</h2>
                <button class="close-btn" onclick="closeEditModal()">×</button>
            </div>

            <form action="update_sk.php" method="POST" enctype="multipart/form-data" class="edit-event-form" id="editEventForm" onsubmit="return validateForm()">
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                
                <div class="form-group">
                    <label>Tên sự kiện <span class="required">*</span></label>
                    <input type="text" name="ten_su_kien" id="ten_su_kien" 
                           value="<?= htmlspecialchars($event['ten_su_kien']) ?>">
                    <span class="error-msg" id="error_ten_su_kien"></span>
                </div>

                <div class="form-group">
                    <label>Mô tả ngắn <span class="required">*</span></label>
                    <textarea name="mo_ta" id="mo_ta" rows="3"><?= htmlspecialchars($event['mo_ta']) ?></textarea>
                    <span class="error-msg" id="error_mo_ta"></span>
                </div>

                <div class="form-group">
                    <label>Nội dung chi tiết</label>
                    <textarea name="noi_dung_chi_tiet" id="noi_dung_chi_tiet" rows="5"><?= htmlspecialchars($event['noi_dung_chi_tiet'] ?? '') ?></textarea>
                    <span class="error-msg" id="error_noi_dung_chi_tiet"></span>
                </div>

                <div class="form-group">
                    <label>Địa điểm <span class="required">*</span></label>
                    <input type="text" name="dia_diem" id="dia_diem" 
                           value="<?= htmlspecialchars($event['dia_diem']) ?>">
                    <span class="error-msg" id="error_dia_diem"></span>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label>Thời gian bắt đầu <span class="required">*</span></label>
                        <input type="datetime-local" name="tg_bat_dau" id="tg_bat_dau"
                               value="<?= date('Y-m-d\TH:i', strtotime($event['thoi_gian_bat_dau'])) ?>">
                        <span class="error-msg" id="error_tg_bat_dau"></span>
                    </div>

                    <div class="form-group">
                        <label>Thời gian kết thúc <span class="required">*</span></label>
                        <input type="datetime-local" name="tg_ket_thuc" id="tg_ket_thuc"
                               value="<?= date('Y-m-d\TH:i', strtotime($event['thoi_gian_ket_thuc'])) ?>">
                        <span class="error-msg" id="error_tg_ket_thuc"></span>
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label>Số lượng tối đa</label>
                        <input type="number" name="so_luong" id="so_luong" min="1" 
                               value="<?= $event['so_luong_toi_da'] ?>">
                        <span class="error-msg" id="error_so_luong"></span>
                    </div>

                    <div class="form-group">
                        <label>Hạn đăng ký</label>
                        <input type="datetime-local" name="han_dang_ky" id="han_dang_ky"
                               value="<?= date('Y-m-d\TH:i', strtotime($event['han_dang_ky'])) ?>">
                        <span class="error-msg" id="error_han_dang_ky"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="trang_thai" id="trang_thai">
                        <option value="sap_dien_ra" <?= $event['trang_thai'] == 'sap_dien_ra' ? 'selected' : '' ?>>Sắp diễn ra</option>
                        <option value="dang_dien_ra" <?= $event['trang_thai'] == 'dang_dien_ra' ? 'selected' : '' ?>>Đang diễn ra</option>
                        <option value="da_ket_thuc" <?= $event['trang_thai'] == 'da_ket_thuc' ? 'selected' : '' ?>>Đã kết thúc</option>
                        <option value="da_huy" <?= $event['trang_thai'] == 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                    <span class="error-msg" id="error_trang_thai"></span>
                </div>

                <div class="form-group">
                    <label>Ảnh bìa hiện tại</label>
                    <?php if (!empty($event['anh_bia'])): ?>
                        <div class="current-image">
                            <img src="<?= htmlspecialchars($event['anh_bia']) ?>" alt="Ảnh bìa hiện tại">
                            <span>Ảnh hiện tại</span>
                        </div>
                    <?php endif; ?>
                    
                    <label class="upload-label">
                        <input type="file" name="anh_bia_moi" id="anh_bia_moi" accept="image/*">
                        <span>Chọn ảnh mới (nếu muốn thay đổi)</span>
                    </label>
                    <span class="error-msg" id="error_anh_bia_moi"></span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">💾 Lưu thay đổi</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">❌ Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function closeEditModal() {
        window.parent.postMessage({ action: 'closeEditModal' }, '*');
    }

    // Validation functions
    function validateField(field, fieldName) {
        const errorElement = document.getElementById(`error_${field.id}`);
        
        if (!field.value.trim()) {
            showError(errorElement, `${fieldName} không được để trống`);
            field.classList.add('error');
            return false;
        } else {
            clearError(errorElement);
            field.classList.remove('error');
            return true;
        }
    }

    function validateDateTime() {
        const startTime = document.getElementById('tg_bat_dau');
        const endTime = document.getElementById('tg_ket_thuc');
        const startError = document.getElementById('error_tg_bat_dau');
        const endError = document.getElementById('error_tg_ket_thuc');

        let isValid = true;

        // Validate start time
        if (!startTime.value) {
            showError(startError, 'Thời gian bắt đầu không được để trống');
            startTime.classList.add('error');
            isValid = false;
        } else {
            clearError(startError);
            startTime.classList.remove('error');
        }

        // Validate end time
        if (!endTime.value) {
            showError(endError, 'Thời gian kết thúc không được để trống');
            endTime.classList.add('error');
            isValid = false;
        } else {
            clearError(endError);
            endTime.classList.remove('error');
        }

        // Validate time logic
        if (startTime.value && endTime.value) {
            const start = new Date(startTime.value);
            const end = new Date(endTime.value);
            
            if (end <= start) {
                showError(endError, 'Thời gian kết thúc phải sau thời gian bắt đầu');
                endTime.classList.add('error');
                isValid = false;
            } else {
                clearError(endError);
                endTime.classList.remove('error');
            }
        }

        return isValid;
    }

    function validateDeadline() {
        const deadline = document.getElementById('han_dang_ky');
        const startTime = document.getElementById('tg_bat_dau');
        const errorElement = document.getElementById('error_han_dang_ky');

        if (deadline.value && startTime.value) {
            const deadlineDate = new Date(deadline.value);
            const startDate = new Date(startTime.value);
            
            if (deadlineDate >= startDate) {
                showError(errorElement, 'Hạn đăng ký phải trước thời gian bắt đầu sự kiện');
                deadline.classList.add('error');
                return false;
            }
        }
        
        clearError(errorElement);
        deadline.classList.remove('error');
        return true;
    }

    function validateNumber(field) {
        const errorElement = document.getElementById(`error_${field.id}`);
        
        if (field.value && field.value < 1) {
            showError(errorElement, 'Số lượng phải lớn hơn 0');
            field.classList.add('error');
            return false;
        } else {
            clearError(errorElement);
            field.classList.remove('error');
            return true;
        }
    }

    function validateImage(field) {
        const errorElement = document.getElementById(`error_${field.id}`);
        
        if (field.files.length > 0) {
            const file = field.files[0];
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!allowedTypes.includes(file.type)) {
                showError(errorElement, 'Chỉ chấp nhận file ảnh: JPG, JPEG, PNG, GIF, WEBP');
                field.value = '';
                field.classList.add('error');
                return false;
            }
            
            if (file.size > maxSize) {
                showError(errorElement, 'Kích thước ảnh không được vượt quá 5MB');
                field.value = '';
                field.classList.add('error');
                return false;
            }
            
            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('.current-image img');
                if (preview) {
                    preview.src = e.target.result;
                }
            }
            reader.readAsDataURL(file);
        }
        
        clearError(errorElement);
        field.classList.remove('error');
        return true;
    }

    function validateForm() {
        let isValid = true;

        // Validate required fields
        const requiredFields = [
            { field: 'ten_su_kien', name: 'Tên sự kiện' },
            { field: 'mo_ta', name: 'Mô tả ngắn' },
            { field: 'dia_diem', name: 'Địa điểm' }
        ];

        requiredFields.forEach(({ field, name }) => {
            const fieldElement = document.getElementById(field);
            if (!validateField(fieldElement, name)) {
                isValid = false;
            }
        });

        // Validate date time
        if (!validateDateTime()) {
            isValid = false;
        }

        // Validate deadline
        if (!validateDeadline()) {
            isValid = false;
        }

        // Validate number
        const soLuongField = document.getElementById('so_luong');
        if (soLuongField.value && !validateNumber(soLuongField)) {
            isValid = false;
        }

        if (!isValid) {
            // Scroll to first error
            const firstError = document.querySelector('.error-msg:not(.hidden)');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Hiển thị thông báo tổng
            alert('Vui lòng kiểm tra lại các trường bắt buộc!');
        }

        return isValid;
    }

    function showError(errorElement, message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            errorElement.style.color = '#dc2626';
        }
    }

    function clearError(errorElement) {
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    // Xử lý ảnh preview
    document.getElementById('anh_bia_moi').addEventListener('change', function(e) {
        validateImage(this);
    });

    // Đóng bằng ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModal();
    });

    // Real-time validation on blur
    document.addEventListener('DOMContentLoaded', function() {
        const requiredFields = ['ten_su_kien', 'mo_ta', 'dia_diem', 'tg_bat_dau', 'tg_ket_thuc'];
        
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                element.addEventListener('blur', function() {
                    if (field.includes('tg_')) {
                        validateDateTime();
                    } else {
                        const fieldName = getFieldName(field);
                        validateField(this, fieldName);
                    }
                });
            }
        });

        // Thêm validation cho số lượng
        const soLuongField = document.getElementById('so_luong');
        if (soLuongField) {
            soLuongField.addEventListener('blur', function() {
                validateNumber(this);
            });
        }

        // Thêm validation cho hạn đăng ký
        const hanDangKyField = document.getElementById('han_dang_ky');
        if (hanDangKyField) {
            hanDangKyField.addEventListener('blur', function() {
                validateDeadline();
            });
        }
    });

    function getFieldName(fieldId) {
        const names = {
            'ten_su_kien': 'Tên sự kiện',
            'mo_ta': 'Mô tả ngắn',
            'dia_diem': 'Địa điểm',
            'tg_bat_dau': 'Thời gian bắt đầu',
            'tg_ket_thuc': 'Thời gian kết thúc'
        };
        return names[fieldId] || fieldId;
    }

    // Thêm sự kiện submit form
    document.getElementById('editEventForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>