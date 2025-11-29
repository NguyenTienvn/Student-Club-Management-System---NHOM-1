function openPending() {
    fetch('popup_danhsachcho.php')
        .then(r => r.text())
        .then(html => {
            const box = document.getElementById("pendingContainer");
            box.innerHTML = html;

            const modal = document.getElementById("pendingModal");
            if (modal) {
                modal.classList.add("show");
                loadPendingList();
            }
        });
}

function loadPendingList() {
    fetch("pending_list.php")
        .then(r => r.text())
        .then(html => {
            const list = document.getElementById("pending-list");
            if (list) list.innerHTML = html;
        });
}

function closePending() {
    const modal = document.getElementById("pendingModal");
    if (!modal) return;

    modal.classList.remove("show");

    setTimeout(() => {
        document.getElementById("pendingContainer").innerHTML = "";
    }, 200);
}
