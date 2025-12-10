<?php
// Load dependencies FIRST
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/constants.php';
require_once __DIR__ . '/includes/functions.php';

// NOW start session
session_start();

require_once __DIR__ . '/assets/database/connect.php';

// Kiểm tra đăng nhập
require_login();

require 'site.php';
load_top();
load_header();

// Lấy club_id
$club_id = get_club_id();

if (!$club_id) {
    die("<h3 style='color:red;text-align:center;margin-top:50px;'>Không tìm thấy CLB!</h3>");
}

// Kiểm tra quyền
if (!can_manage_club($conn, $_SESSION['user_id'], $club_id)) {
    die("<h3 style='color:red;text-align:center;margin-top:50px;'>Bạn không có quyền thêm thành viên!</h3>");
}
?>

<link rel="stylesheet" href="assets/css/add_TV_CLB.css?v=2">
<input type="hidden" id="club-id" value="<?php echo htmlspecialchars((string)$club_id, ENT_QUOTES, 'UTF-8'); ?>">

<div class="addTV-container">
    <div class="addTV-header">
        <div class="header-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
        </div>
        <div class="header-text">
            <h2>Thêm thành viên vào CLB</h2>
            <p>Tìm kiếm và thêm thành viên mới vào câu lạc bộ của bạn</p>
        </div>
    </div>

    <div class="addTV-body">
        <!-- Ô tìm kiếm -->
        <div class="search-container">
            <div class="search-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </div>
            <input type="text" id="search-input" class="input" placeholder="Tìm kiếm theo Email hoặc Họ tên..." autocomplete="off">
        </div>

        <!-- Người dùng đã chọn -->
        <div id="selected-user-box" class="selected-user-box">
            <div class="selected-user-avatar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="selected-user-info">
                <div class="selected-label">Đã chọn:</div>
                <div class="selected-name" id="selected-name"></div>
                <div class="selected-email" id="selected-email"></div>
            </div>
            <button id="btn-add-member" class="btn-add-member">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Thêm thành viên
            </button>
            <button type="button" onclick="clearSelection()" class="clear-selection" title="Hủy chọn">×</button>
        </div>

        <!-- Kết quả tìm kiếm -->
        <div id="suggestions" class="suggestions-box"></div>

    <!-- Gợi ý ngẫu nhiên -->
    <div id="default-users" class="default-users-box">
        <div class="section-header">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Gợi ý thành viên
            </div>
            <span class="section-subtitle">Những người dùng có thể quan tâm</span>
        </div>
        <div id="default-users-list" class="users-grid">
            <?php
            $sql = "SELECT id, ho_ten, email
                    FROM users 
                    WHERE id NOT IN (
                        SELECT user_id FROM club_members WHERE club_id = ?
                    ) 
                    ORDER BY RAND() 
                    LIMIT 12";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $club_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($user = $result->fetch_assoc()) {
                        $ho_ten = htmlspecialchars($user['ho_ten'] ?? 'Chưa đặt tên', ENT_QUOTES, 'UTF-8');
                        $email  = htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8');
                        $userId = $user['id'];

                        echo "
                        <div class='user-card' data-userid='$userId' data-name='$ho_ten' data-email='$email'>
                            <div class='user-avatar'>
                                <svg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                                    <path d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'></path>
                                    <circle cx='12' cy='7' r='4'></circle>
                                </svg>
                            </div>
                            <div class='user-info'>
                                <div class='user-name'>$ho_ten</div>
                                <div class='user-email'>$email</div>
                            </div>
                            <button type='button' class='add-this-user-btn'>
                                <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                                    <line x1='12' y1='5' x2='12' y2='19'></line>
                                    <line x1='5' y1='12' x2='19' y2='12'></line>
                                </svg>
                                Chọn
                            </button>
                        </div>";
                    }
                } else {
                    echo "<div class='empty-state'>
                            <svg width='64' height='64' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.5'>
                                <path d='M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2'></path>
                                <circle cx='9' cy='7' r='4'></circle>
                                <path d='M23 21v-2a4 4 0 0 0-3-3.87'></path>
                                <path d='M16 3.13a4 4 0 0 1 0 7.75'></path>
                            </svg>
                            <p>Không còn thành viên nào để thêm</p>
                          </div>";
                }
                $stmt->close();
            }
            ?>
        </div>
    </div>

    <div class="action-buttons">
        <button class="btn-back" onclick="window.location.href='Dashboard.php'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Quay lại Dashboard
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const searchInput = document.getElementById('search-input');
    const suggestionsBox = document.getElementById('suggestions');
    const defaultUsersBox = document.getElementById('default-users');
    const selectedBox = document.getElementById('selected-user-box');
    const selectedName = document.getElementById('selected-name');
    const selectedEmail = document.getElementById('selected-email');
    const btnAddMember = document.getElementById('btn-add-member');
    const clubId = document.getElementById('club-id').value;

    let selectedUser = null;

    // Chọn người dùng từ card
    document.addEventListener('click', function (e) {
        const card = e.target.closest('.user-card');
        if (!card) return;

        const userId = card.dataset.userid;
        const name = card.dataset.name || 'Không tên';
        const email = card.dataset.email;

        selectUser(userId, name, email, card);
    });

    window.clearSelection = function () {
        selectedUser = null;
        selectedBox.style.display = 'none';
        defaultUsersBox.style.display = 'block';
        suggestionsBox.innerHTML = '';
        document.querySelectorAll('.user-card').forEach(c => c.style.borderColor = '#ddd');
    };

    function selectUser(userId, name, email, element = null) {
        selectedUser = { userId, name, email };

        selectedName.textContent = name;
        selectedEmail.textContent = email;
        selectedBox.style.display = 'flex';

        suggestionsBox.innerHTML = '';
        suggestionsBox.style.display = 'none';
        defaultUsersBox.style.display = 'none';
        searchInput.value = '';

        document.querySelectorAll('.user-card').forEach(c => c.style.borderColor = '#ddd');
        if (element) element.style.borderColor = '#2196F3';
    }

    // Debounce
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Tìm kiếm
    async function performSearch(keyword) {
        if (keyword.length < 2) {
            suggestionsBox.innerHTML = '';
            suggestionsBox.style.display = 'none';
            defaultUsersBox.style.display = 'block';
            return;
        }

        defaultUsersBox.style.display = 'none';
        suggestionsBox.innerHTML = '<div style="padding:20px;text-align:center;color:#888;grid-column:1/-1;">Đang tìm...</div>';
        suggestionsBox.style.display = 'grid';

        try {
            const res = await fetch(`api/search_users.php?keyword=${encodeURIComponent(keyword)}&club_id=${clubId}`);
            const users = await res.json();

            suggestionsBox.innerHTML = '';
            if (!Array.isArray(users) || users.length === 0) {
                suggestionsBox.innerHTML = '<div style="padding:20px;text-align:center;color:#888;grid-column:1/-1;">Không tìm thấy người dùng nào</div>';
                return;
            }

            users.forEach(user => {
                const div = document.createElement('div');
                div.className = 'user-card';
                div.dataset.userid = user.id;
                div.dataset.name = user.ho_ten || 'Không tên';
                div.dataset.email = user.email;

                div.innerHTML = `
                    <div class="user-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="user-info">
                        <div class="user-name">${user.ho_ten || 'Không tên'}</div>
                        <div class="user-email">${user.email}</div>
                    </div>
                    <button type="button" class="add-this-user-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Chọn
                    </button>
                `;

                div.addEventListener('click', (e) => {
                    if (e.target.tagName === 'BUTTON') e.stopPropagation();
                    selectUser(user.id, user.ho_ten || 'Không tên', user.email, div);
                });

                suggestionsBox.appendChild(div);
            });
        } catch (err) {
            suggestionsBox.innerHTML = '<div style="padding:20px;color:red;grid-column:1/-1;">Lỗi kết nối server</div>';
            console.error(err);
        }
    }

    searchInput.addEventListener('input', debounce(function () {
        performSearch(this.value.trim());
    }, 300));

    // Thêm thành viên
    btnAddMember.addEventListener('click', function () {
        if (!selectedUser) return showNotification('Vui lòng chọn thành viên!', 'warning');

        btnAddMember.disabled = true;
        btnAddMember.innerHTML = '<span class="spinner"></span> Đang thêm...';

        fetch('api/add_member.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'club_id=' + clubId + '&user_id=' + selectedUser.userId
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('✓ Thêm thành viên thành công! Thành viên đã nhận được thông báo.', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('✗ ' + (data.message || 'Không xác định'), 'error');
                btnAddMember.disabled = false;
                btnAddMember.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    Thêm thành viên
                `;
            }
        })
        .catch(err => {
            console.error(err);
            showNotification('✗ Lỗi mạng hoặc server!', 'error');
            btnAddMember.disabled = false;
            btnAddMember.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Thêm thành viên
            `;
        });
    });

    // Hàm hiển thị thông báo
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
</script>