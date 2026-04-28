<div id="editorContent" class="w-full h-[620px] bg-white">
    <div class="w-full h-full">
        <div class="bg-white h-full p-6 border border-gray-200 relative flex flex-col">

            @if(!$canEdit)
                <div class="mb-4 px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
                    👁️ You are in READ ONLY mode
                </div>
            @endif

            <div id="saveStatus" class="absolute top-4 right-5 text-sm text-gray-500">
                Saved
            </div>

            <input
                type="text"
                id="noteTitle"
                name="note_title_{{ $note->id ?? 'new' }}"
                value="{{ isset($note) && $note?->title ? $note->title : '' }}"
                placeholder="Title..."
                autocomplete="new-password"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
                {{ !$canEdit ? 'disabled' : '' }}
                class="mt-8 w-full text-3xl font-bold text-gray-900 outline-none border-none mb-4
                {{ !$canEdit ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}"
            />

            <textarea
                id="noteContent"
                name="note_content"
                placeholder="Write something..."
                autocomplete="off"
                {{ !$canEdit ? 'disabled' : '' }}
                class="w-full outline-none border-none resize-none flex-1 min-h-0 overflow-y-auto pr-2
                {{ !$canEdit ? 'bg-gray-100 cursor-not-allowed text-gray-500' : 'text-gray-700' }}"
            >{{ $note->content ?? '' }}</textarea>

            <div class="mt-auto border-t border-gray-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleEditorPanel('imagePanel')"
                        class="w-11 h-11 rounded-2xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center justify-center"
                        title="Upload image">
                        <i class="fa-solid fa-image"></i>
                    </button>

                    <button type="button" onclick="toggleEditorPanel('labelPanel')"
                        class="w-11 h-11 rounded-2xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center justify-center"
                        title="Add label">
                        <i class="fa-solid fa-tag"></i>
                    </button>

                    @if($canEdit)
                        <button type="button" id="lockToggleBtn" onclick="toggleLockPanel()"
                            class="w-11 h-11 rounded-2xl {{ isset($note) && $note->note_password ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition flex items-center justify-center"
                            title="Lock / Unlock note">
                            @if(isset($note) && $note->note_password)
                                <i id="lockToggleIcon" class="fa-solid fa-lock"></i>
                            @else
                                <i id="lockToggleIcon" class="fa-solid fa-lock-open"></i>
                            @endif
                        </button>
                    @endif
                </div>

                <button type="button" onclick="saveNoteAndBack()"
                    class="px-5 py-3 rounded-2xl bg-black text-white font-semibold hover:bg-gray-800 active:scale-95 transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </button>
            </div>

            <div id="imagePanel" class="hidden mt-4 rounded-3xl border border-gray-200 bg-gray-50 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Images</h3>
                        <p class="text-sm text-gray-500">Upload hình ảnh cho ghi chú.</p>
                    </div>

                    <label class="w-11 h-11 rounded-2xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition flex items-center justify-center cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <input type="file" id="imageInput" multiple accept="image/*"
                            {{ !$canEdit ? 'disabled' : '' }}
                            class="hidden">
                    </label>
                </div>

                <button type="button" id="uploadBtn"
                    {{ !$canEdit ? 'disabled' : '' }}
                    class="hidden mb-4 px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:bg-gray-800 active:scale-95 transition">
                    Upload Images
                </button>

                <div id="imagePreview" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @if(isset($note))
                        @foreach($note->images as $img)
                            <div id="img-{{ $img->id }}" class="relative group overflow-hidden rounded-2xl border border-gray-200 bg-white">
                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                    class="w-full h-32 object-cover transition group-hover:scale-105">

                                @if($canEdit)
                                    <button type="button" onclick="event.stopPropagation(); deleteImage('{{ $img->id }}')"
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/70 text-white hidden group-hover:flex items-center justify-center hover:bg-black transition">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div id="labelPanel" class="hidden mt-4 rounded-3xl border border-gray-200 bg-gray-50 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Labels</h3>
                        <p class="text-sm text-gray-500">Gắn nhãn để phân loại ghi chú.</p>
                    </div>

                    <div class="w-11 h-11 rounded-2xl bg-white border border-gray-200 text-gray-700 flex items-center justify-center">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-4" id="selectedLabelsContainer">
                    @if(isset($note))
                        @foreach($note->labels as $label)
                            <span id="badge-{{ $label->id }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-black text-white text-sm font-medium shadow-sm">
                                {{ $label->name }}

                                @if($canEdit)
                                    <button type="button" onclick="detachLabel('{{ $label->id }}')"
                                        class="w-5 h-5 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                @endif
                            </span>
                        @endforeach
                    @endif
                </div>

                <div class="relative flex items-center gap-3">
                    <div class="flex flex-1 items-center px-4 py-3 rounded-2xl border border-gray-200 bg-white focus-within:ring-2 focus-within:ring-black/5 transition">
                        <i class="fa-solid fa-plus text-gray-400 mr-3"></i>

                        <input type="text" id="newLabel"
                            {{ !$canEdit ? 'disabled' : '' }}
                            placeholder="Add label..."
                            autocomplete="off"
                            class="w-full text-sm outline-none border-none bg-transparent focus:ring-0 text-gray-800 placeholder-gray-400"
                            oninput="showSuggestions(this.value)">
                    </div>

                    @if($canEdit)
                        <button type="button" onclick="createLabel()"
                            class="px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:bg-gray-800 active:scale-95 transition">
                            Add
                        </button>
                    @endif

                    <div id="labelSuggestions"
                        class="absolute z-20 left-0 right-0 top-full mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl hidden max-h-44 overflow-y-auto">
                    </div>
                </div>
            </div>

            <div id="lockBox" class="hidden mt-4 rounded-3xl border border-yellow-200 bg-yellow-50 p-5">
                <h4 class="font-bold text-yellow-900 mb-3">
                    <i class="fa-solid fa-lock mr-2"></i>
                    Set Password
                </h4>

                <input type="password" id="lockPassword" placeholder="Password"
                    autocomplete="new-password"
                    class="w-full mb-3 px-4 py-3 border border-yellow-200 rounded-2xl bg-white focus:ring-2 focus:ring-yellow-300 outline-none">

                <input type="password" id="lockConfirm" placeholder="Confirm Password"
                    autocomplete="new-password"
                    class="w-full mb-4 px-4 py-3 border border-yellow-200 rounded-2xl bg-white focus:ring-2 focus:ring-yellow-300 outline-none">

                <button type="button" onclick="setPassword()"
                    class="w-full px-5 py-3 rounded-2xl bg-yellow-500 text-white font-semibold hover:bg-yellow-600 transition">
                    Confirm Lock
                </button>
            </div>

            <div id="unlockBox" class="hidden mt-4 rounded-3xl border border-gray-200 bg-gray-50 p-5">
                <h4 class="font-bold text-gray-900 mb-3">
                    <i class="fa-solid fa-unlock mr-2"></i>
                    Remove Password
                </h4>

                <input type="password" id="unlockPassword" placeholder="Enter current password"
                    autocomplete="current-password"
                    class="w-full mb-4 px-4 py-3 border border-gray-200 rounded-2xl bg-white focus:ring-2 focus:ring-black/10 outline-none">

                <button type="button" onclick="removePassword()"
                    class="w-full px-5 py-3 rounded-2xl bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Confirm Unlock
                </button>
            </div>

            <input type="hidden" id="noteId" value="{{ $note->id ?? '' }}">
        </div>
    </div>

    <script>
        (() => {
            const canEditValue = @json($canEdit ?? true);

            let timer = null;
            let noteIsLocked = @json(isset($note) && $note->note_password);

            const titleInput = document.getElementById('noteTitle');
            const contentInput = document.getElementById('noteContent');
            const noteIdInput = document.getElementById('noteId');
            const saveStatus = document.getElementById('saveStatus');
            const imageInput = document.getElementById('imageInput');
            const uploadBtn = document.getElementById('uploadBtn');
            const labelInput = document.getElementById('newLabel');
            const suggestions = document.getElementById('labelSuggestions');

            window.safeCloseEditorModal = function () {
                if (typeof closeEditorModal === 'function') {
                    closeEditorModal();
                } else {
                    window.location.href = "{{ route('notes.index') }}";
                }
            }

            window.toggleEditorPanel = function (panelId) {
                ['imagePanel', 'labelPanel', 'lockBox', 'unlockBox'].forEach(id => {
                    const panel = document.getElementById(id);
                    if (!panel) return;

                    if (id === panelId) {
                        panel.classList.toggle('hidden');
                    } else {
                        panel.classList.add('hidden');
                    }
                });
            }

            window.toggleLockPanel = function () {
                if (noteIsLocked) {
                    toggleEditorPanel('unlockBox');
                } else {
                    toggleEditorPanel('lockBox');
                }
            }

            function updateLockIcon(isLocked) {
                noteIsLocked = isLocked;

                const icon = document.getElementById('lockToggleIcon');
                const btn = document.getElementById('lockToggleBtn');

                if (!icon || !btn) return;

                icon.className = isLocked ? 'fa-solid fa-lock' : 'fa-solid fa-lock-open';

                btn.className = isLocked
                    ? 'w-11 h-11 rounded-2xl bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition flex items-center justify-center'
                    : 'w-11 h-11 rounded-2xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center justify-center';
            }

            function autosave() {
                if (!canEditValue) return Promise.resolve();
                if (!titleInput || !contentInput || !noteIdInput || !saveStatus) return Promise.resolve();

                saveStatus.textContent = "Saving...";

                return fetch("{{ route('notes.autosave') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        id: noteIdInput.value || null,
                        title: titleInput.value || "",
                        content: contentInput.value || ""
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error("Save failed");
                    return res.json();
                })
                .then(data => {
                    saveStatus.textContent = "Saved";

                    if (data.note_id && !noteIdInput.value) {
                        noteIdInput.value = data.note_id;
                        window.history.replaceState({}, "", "/notes/editor/" + data.note_id);
                    }

                    return data;
                })
                .catch(err => {
                    console.log("SAVE ERROR:", err);
                    saveStatus.textContent = "❌ Save failed";
                    throw err;
                });
            }

            window.saveNoteAndBack = function () {
                if (timer) clearTimeout(timer);

                autosave()
                    .then(() => {
                        window.location.href = "{{ route('notes.index') }}";
                    })
                    .catch(() => {
                        alert("Không thể lưu ghi chú. Vui lòng thử lại.");
                    });
            }

            function scheduleSave() {
                if (timer) clearTimeout(timer);
                timer = setTimeout(autosave, 1000);
            }

            if (titleInput) titleInput.addEventListener("input", scheduleSave);
            if (contentInput) contentInput.addEventListener("input", scheduleSave);

            if (imageInput) {
                imageInput.addEventListener('change', function () {
                    const preview = document.getElementById('imagePreview');
                    if (!preview || !uploadBtn) return;

                    for (let file of this.files) {
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            const div = document.createElement('div');
                            div.className = 'relative group overflow-hidden rounded-2xl border border-gray-200 bg-white';

                            div.innerHTML = `
                                <img src="${e.target.result}"
                                    class="w-full h-32 object-cover opacity-70">
                            `;

                            preview.prepend(div);
                        }

                        reader.readAsDataURL(file);
                    }

                    uploadBtn.classList.remove('hidden');
                });
            }

            if (uploadBtn) {
                uploadBtn.addEventListener('click', function () {
                    if (!noteIdInput.value) {
                        alert("Note is saving, please wait 1 second...");
                        return;
                    }

                    const formData = new FormData();

                    for (let file of imageInput.files) {
                        formData.append('images[]', file);
                    }

                    uploadBtn.textContent = "Uploading...";
                    uploadBtn.disabled = true;

                    fetch(`/notes/${noteIdInput.value}/images`, {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(() => {
                        uploadBtn.textContent = "Upload Images";
                        uploadBtn.disabled = false;
                        imageInput.value = "";
                        uploadBtn.classList.add('hidden');
                        location.reload();
                    });
                });
            }

            window.deleteImage = function (id) {
                fetch(`/notes/images/${id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(() => {
                    const el = document.getElementById('img-' + id);
                    if (el) el.remove();
                })
                .catch(err => console.log(err));
            }

            window.showSuggestions = function () {
                loadLabels();
                if (suggestions) suggestions.classList.remove('hidden');
            }

            if (labelInput) {
                labelInput.addEventListener('focus', () => {
                    loadLabels();
                    suggestions.classList.remove('hidden');
                });

                labelInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        createLabel();
                    }
                });
            }

            document.addEventListener('click', (e) => {
                if (!labelInput || !suggestions) return;

                if (!labelInput.contains(e.target) && !suggestions.contains(e.target)) {
                    suggestions.classList.add('hidden');
                }
            });

            function loadLabels() {
                if (!suggestions) return;

                fetch('/labels')
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = "";

                    data.forEach(label => {
                        const div = document.createElement('div');

                        div.className = "px-4 py-3 hover:bg-gray-50 cursor-pointer text-sm flex justify-between items-center border-b last:border-0 transition";
                        div.innerHTML = `
                            <span class="font-medium text-gray-700">${label.name}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">Apply</span>
                        `;

                        div.onclick = () => {
                            attachLabelToNote(label.id, label.name);
                            suggestions.classList.add('hidden');
                            labelInput.value = "";
                        };

                        suggestions.appendChild(div);
                    });
                });
            }

            window.createLabel = function () {
                if (!labelInput) return;

                const name = labelInput.value.trim();
                if (!name) return;

                fetch('/labels', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ name: name })
                })
                .then(res => res.json())
                .then(data => {
                    attachLabelToNote(data.id, data.name);
                    labelInput.value = "";
                    suggestions.classList.add('hidden');
                });
            }

            function attachLabelToNote(id, name) {
                const noteId = noteIdInput.value;

                if (!noteId) {
                    alert("Vui lòng chờ Note lưu nháp xong trước khi thêm nhãn.");
                    return;
                }

                fetch(`/notes/${noteId}/labels`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ label_ids: [id] })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'attached') {
                        renderLabelBadge(id, name);
                    }
                })
                .catch(err => console.error("ATTACH ERROR:", err));
            }

            function renderLabelBadge(id, name) {
                if (document.getElementById(`badge-${id}`)) return;

                const container = document.getElementById('selectedLabelsContainer');
                if (!container) return;

                const span = document.createElement('span');

                span.id = `badge-${id}`;
                span.className = "inline-flex items-center gap-2 px-4 py-2 rounded-full bg-black text-white text-sm font-medium shadow-sm";
                span.innerHTML = `
                    ${name}
                    <button type="button" onclick="detachLabel('${id}')"
                        class="w-5 h-5 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                `;

                container.appendChild(span);
            }

            window.detachLabel = function (labelId) {
                const noteId = noteIdInput.value;

                fetch(`/notes/${noteId}/labels/${labelId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(() => {
                    const el = document.getElementById(`badge-${labelId}`);
                    if (!el) return;

                    el.classList.add('opacity-0', 'scale-75', 'transition');
                    setTimeout(() => el.remove(), 200);
                });
            }

            window.setPassword = function () {
                const password = document.getElementById('lockPassword')?.value;
                const confirm = document.getElementById('lockConfirm')?.value;

                if (!password || password.length < 4) {
                    alert("Password phải >= 4 ký tự");
                    return;
                }

                if (password !== confirm) {
                    alert("Mật khẩu không khớp");
                    return;
                }

                if (!noteIdInput.value) {
                    alert("Note chưa save, đợi 1 giây...");
                    return;
                }

                fetch(`/notes/${noteIdInput.value}/set-password`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        password: password,
                        password_confirmation: confirm
                    })
                })
                .then(res => res.json())
                .then(() => {
                    alert("Đã khóa note 🔒");
                    document.getElementById('lockBox').classList.add('hidden');
                    updateLockIcon(true);
                });
            }

            window.removePassword = function () {
                const password = document.getElementById('unlockPassword')?.value;

                if (!password) {
                    alert("Nhập password");
                    return;
                }

                fetch(`/notes/${noteIdInput.value}/remove-password`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        password: password
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error();
                    return res.json();
                })
                .then(() => {
                    alert("Đã mở khóa 🔓");
                    document.getElementById('unlockBox').classList.add('hidden');
                    updateLockIcon(false);
                })
                .catch(() => {
                    alert("Sai mật khẩu!");
                });
            }

            const currentUserId = @json(auth()->id());
            const noteId = noteIdInput ? noteIdInput.value : null;

            let isTypingRealtime = false;

            if (titleInput) {
                titleInput.addEventListener('input', () => {
                    isTypingRealtime = true;
                    setTimeout(() => isTypingRealtime = false, 1000);
                });
            }

            if (contentInput) {
                contentInput.addEventListener('input', () => {
                    isTypingRealtime = true;
                    setTimeout(() => isTypingRealtime = false, 1000);
                });
            }

            if (noteId && typeof Echo !== 'undefined') {
                Echo.channel('note.' + noteId)
                    .listen('.note.updated', (e) => {
                        if (e.updated_by === currentUserId) return;
                        if (isTypingRealtime) return;

                        titleInput.value = e.title ?? '';
                        contentInput.value = e.content ?? '';
                        saveStatus.textContent = "Updated from another user";
                    });
            }
        })();
    </script>
</div>