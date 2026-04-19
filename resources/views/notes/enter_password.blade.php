<div class="container mt-5">
    <h3>🔒 Note này đã bị khóa</h3>

    <input type="password" id="password" class="form-control mt-3" placeholder="Nhập mật khẩu">

    <button class="btn btn-primary mt-3" onclick="unlockNote()">Mở khóa</button>
</div>

<script>
function unlockNote() {
    fetch(`/notes/{{ $note->id }}/verify-password`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            password: document.getElementById('password').value
        })
    })
    .then(res => {
        if (!res.ok) throw new Error();
        return res.json();
    })
    .then(() => {
        window.location.href = `/notes/{{ $note->id }}/editor`;
    })
    .catch(() => {
        alert("Sai mật khẩu!");
    });
}
</script>