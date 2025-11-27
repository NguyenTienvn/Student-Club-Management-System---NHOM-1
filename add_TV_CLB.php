<?php
session_start();
require_once(__DIR__ . "/assets/database/connect.php");
require 'site.php';
load_top();
load_header();
 // Lấy club_id
$club_id = isset($_GET['club_id']) ? (int)$_GET['club_id'] : ($_SESSION['club_id'] ?? 0);

if (!$club_id) {
    die("<h3 style='color:red;text-align:center;margin-top:50px;'>Không tìm thấy CLB!</h3>");
}
?>

<link rel="stylesheet" href="assets/css/add_TV_CLB.css?v=1">
<input type="hidden" id="club-id" value="<?php echo htmlspecialchars($club_id); ?>">

<div class="addTV-body">
    <h2>Thêm thành viên vào CLB</h2>

    <!-- Ô tìm kiếm -->
    <div class="search-container">
        <input type="text" id="search-input" class="input" placeholder="Tìm kiếm theo Email hoặc tên" autocomplete="off">
    </div>

    <!-- Người dùng đã chọn -->
    <div id="selected-user-box" class="selected-user-box">
        <div class="selected-user-info">
            Đã chọn: <strong id="selected-name"></strong> 
            (<span id="selected-email"></span>)
        </div>
        <button id="btn-add-member" class="btn-add-member">Thêm thành viên</button>
        <button type="button" onclick="clearSelection()" class="clear-selection">×</button>
    </div>

    <!-- Kết quả tìm kiếm -->
    <div id="suggestions" class="suggestions-box"></div>

    <!-- Gợi ý ngẫu nhiên -->
    <div id="default-users" class="default-users-box">
        <div class="section-title">Gợi ý thành viên ngẫu nhiên</div>
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
                        $ho_ten = htmlspecialchars($user['ho_ten'] ?: 'Chưa đặt tên');
                        $email  = htmlspecialchars($user['email']);
                        $userId = $user['id'];

                        echo "
                        <div class='user-card' data-userid='$userId' data-name='$ho_ten' data-email='$email'>
                            <div class='user-info'>
                                <div class='user-name'>$ho_ten</div>
                                <div class='user-email'>$email</div>
                            </div>
                            <button type='button' class='add-this-user-btn'>Thêm ngay</button>
                        </div>";
                    }
                } else {
                    echo "<p style='text-align:center;color:#888;padding:20px 0; grid-column: 1 / -1;'>
                            Không còn thành viên nào để thêm
                          </p>";
                }
                $stmt->close();
            }
            ?>
        </div>
    </div>
    <button class="btn-back" onclick="window.location.href='Dashboard.php'">Quay lại</button>

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
                    <div class="user-info">
                        <div class="user-name">${user.ho_ten || 'Không tên'}</div>
                        <div class="user-email">${user.email}</div>
                    </div>
                    <button type="button" class="add-this-user-btn">Chọn</button>
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
        if (!selectedUser) return alert('Vui lòng chọn thành viên!');

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
                alert('Thêm thành viên thành công!');
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không xác định'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi mạng hoặc server!');
        });
    });
});
</script>
