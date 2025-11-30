(function () {

    const SEARCH_URL = "search_user.php";
    const DEFAULT_AVATAR = "assets/img/hinhpopup.jpg";
    let debounceTimer = null;

    function qs(id) { return document.getElementById(id); }

    function renderList(resultEl, list) {
        if (!list || list.length === 0) {
            resultEl.innerHTML = `<div class="item">Không tìm thấy</div>`;
            resultEl.style.display = "block";
            return;
        }

        resultEl.innerHTML = list.map(u => `
            <div class="item" data-id="${u.id}" data-name="${u.ho_ten}">
                <div class="user-item">
                    <div class="user-name">${u.ho_ten}</div>
                    <div class="user-email">${u.email || ""}</div>
                </div>
            </div>
        `).join("");

        resultEl.style.display = "block";

        // thêm event cho item
        resultEl.querySelectorAll(".item").forEach(item => {
            item.addEventListener("click", () => {
                qs("selectedUser").value = item.dataset.id;
                qs("previewName").textContent = item.dataset.name;
                qs("previewAvatar").src = DEFAULT_AVATAR;
                resultEl.style.display = "none";
            });
        });
    }

    function searchUser(keyword, resultEl) {
        resultEl.style.display = "block";
        resultEl.innerHTML = `<div class="item">Đang tải...</div>`;

        fetch(`${SEARCH_URL}?keyword=${encodeURIComponent(keyword)}`)
            .then(r => r.json())
            .then(list => renderList(resultEl, list))
            .catch(() => resultEl.innerHTML = `<div class="item">Lỗi kết nối</div>`);
    }

    function initSearchBox() {

        const input = qs("searchMember");
        const result = qs("searchResultMember");
        const spinner = document.querySelector(".spinner-icon");

        if (!input || !result) return;

        // khi focus → load tất cả
        input.addEventListener("focus", () => searchUser("", result));

        // khi click icon → load lại
        spinner.addEventListener("click", () => searchUser(input.value.trim(), result));

        input.addEventListener("input", () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchUser(input.value.trim(), result);
            }, 200);
        });

        // click ngoài → đóng dropdown
        document.addEventListener("click", (e) => {
            if (!e.target.closest(".search-box")) {
                result.style.display = "none";
            }
        });
    }

    window.initAddMemberSearch = initSearchBox;

})();


// mở popup
function openAddMemberPopup(pb_id) {
    document.getElementById("popup_add_member").classList.add("show");
    document.getElementById("pb_id_input").value = pb_id;
    initAddMemberSearch();
}

// thêm thành viên
function addMember() {
    const user_id = document.getElementById("selectedUser").value;
    const pb_id = document.getElementById("pb_id_input").value;

    if (!user_id) return alert("Vui lòng chọn thành viên!");

    fetch("add_member_department.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "user_id=" + user_id + "&pb_id=" + pb_id
    })
        .then(r => r.text())
        .then(msg => {
            alert(msg);
            closeAddMemberPopup();
            location.reload();
        });
}