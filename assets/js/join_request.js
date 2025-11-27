function openJoinModal(clubId) {
    console.log('Opening modal for club:', clubId);
    
    fetch(`popup_join.php?club_id=${clubId}`)
        .then(r => {
            if (!r.ok) throw new Error('Network error');
            return r.text();
        })
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = document.getElementById('joinClubModal');
            if (modal) {
                modal.classList.add('show');
            }
        })
        .catch(err => {
            console.error('Lỗi khi mở popup:', err);
            alert('Có lỗi xảy ra khi mở form đăng ký');
        });
}

function closeJoinModal() {
    const modal = document.getElementById('joinClubModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            document.getElementById('modalContainer').innerHTML = '';
        }, 300);
    }
}

// Đóng modal khi click bên ngoài
document.addEventListener('click', function(event) {
    const modal = document.getElementById('joinClubModal');
    if (modal && event.target === modal) {
        closeJoinModal();
    }
});

// Đóng modal bằng phím ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeJoinModal();
    }
});