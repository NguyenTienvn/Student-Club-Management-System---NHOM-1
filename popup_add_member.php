<div id="popup_add_member" class="popup-overlay">
    <div class="popup-box">

        <h2>Thêm thành viên vào ban <?= htmlspecialchars($phongban['ten_phong_ban']) ?></h2>
        <p>Phân bổ vào phòng ban để quản lý thông tin dễ dàng hơn</p>

        <div class="search-box">
            <input type="text" id="searchMember" placeholder="Tìm kiếm thành viên">

            <span class="spinner-icon">
                <svg width="20" height="20" viewBox="0 0 20 20">
                    <path d="M6 8l4-4 4 4H6zM6 12h8l-4 4-4-4z" fill="#777"/>
                </svg>
            </span>

            <div id="searchResultMember" class="search-result"></div>
        </div>

        <!-- Preview khi chọn -->
        <div id="memberPreview" class="member-preview">
            <img id="previewAvatar" src="assets/img/hinhpopup.jpg" alt="Avatar">
            <p id="previewName"></p>
        </div>

        <input type="hidden" id="selectedUser">
        <input type="hidden" id="pb_id_input">

        <div class="popup-actions">
            <button class="cancel" onclick="closeAddMemberPopup()">Hủy bỏ</button>
            <button class="confirm" onclick="addMember()">Thêm</button>
        </div>

    </div>
</div>
