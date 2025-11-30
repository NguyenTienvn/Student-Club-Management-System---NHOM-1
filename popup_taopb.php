<?php
$club_id = isset($_GET['club_id']) ? (int)$_GET['club_id'] : 0;
?>

<div id="createDeptModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tạo phòng ban mới</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        
        <form id="createDeptForm" action="process_taopb.php" method="POST">
            <input type="hidden" name="club_id" value="<?= $club_id ?>">
            
            <div class="form-group">
                <label for="ten_phong_ban">Tên phòng ban <span class="required">*</span></label>
                <input type="text" 
                       id="ten_phong_ban" 
                       name="ten_phong_ban" 
                       placeholder="Nhập tên phòng ban" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="chuc_nang_nhiem_vu">Chức năng nhiệm vụ <span class="required">*</span></label>
                <textarea id="chuc_nang_nhiem_vu" 
                          name="chuc_nang_nhiem_vu" 
                          rows="4" 
                          placeholder="Mô tả chức năng và nhiệm vụ của phòng ban" 
                          required></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn-submit">Tạo phòng ban</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="assets/css/popup_taopb.css">
