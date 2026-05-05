<x-app-layout>
    <div class="max-w-md mx-auto py-20 text-center">

        <div class="bg-white p-6 rounded-xl shadow border">
            <h2 class="text-xl font-bold mb-4">🔒 Note bị khóa</h2>

            <input
                type="password"
                id="noteUnlockPassword"
                name="note_unlock_password_{{ $note->id }}"
                placeholder="Nhập mật khẩu"
                autocomplete="off"
                data-lpignore="true"
                data-form-type="other"
                class="w-full px-4 py-2 border rounded mb-4">

            <button onclick="unlockNote()"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Mở khóa
            </button>
        </div>

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
            // ✅ redirect đúng route
            window.location.href = "{{ route('notes.index') }}";
        })
        .catch(() => {
            alert("Sai mật khẩu!");
        });
    }
    </script>
</x-app-layout>