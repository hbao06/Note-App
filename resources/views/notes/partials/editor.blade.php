<div id="editorContent" class="w-full h-[620px] bg-white">
    <div class="w-full h-full">
        <div class="editor-shell bg-white h-full p-6 border border-gray-200 relative flex flex-col">

            @if(!$canEdit)
                <div class="mb-4 px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
                    👁️ You are in READ ONLY mode
                </div>
            @endif

            @if($canEdit)
                <div id="saveStatus" class="absolute top-4 right-5 text-sm text-gray-500">
                    Saved
                </div>
            @else
                <div id="saveStatus" class="hidden"></div>
            @endif

            <input
                type="text"
                id="noteTitle"
                name="note_title_{{ $note->id ?? 'new' }}"
                value="{{ isset($note) && $note?->title ? $note->title : '' }}"
                placeholder="Title..."
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
                {{ !$canEdit ? 'disabled' : '' }}
                class="mt-8 w-full text-3xl font-bold text-gray-900 mb-4
                rounded-2xl border border-gray-200 px-4 py-3
                focus:ring-2 focus:ring-gray-300 focus:border-gray-300
                transition
                {{ !$canEdit ? 'bg-gray-100 cursor-not-allowed text-gray-500 select-none pointer-events-none' : 'bg-white' }}"
            />

            <textarea
                id="noteContent"
                name="note_content"
                placeholder="Write something..."
                autocomplete="off"
                {{ !$canEdit ? 'disabled' : '' }}
                class="w-full resize-none flex-1 min-h-0 overflow-y-auto pr-2
                rounded-2xl border border-gray-200 px-4 py-3
                focus:ring-2 focus:ring-gray-300 focus:border-gray-300
                transition
                {{ !$canEdit ? 'bg-gray-100 cursor-not-allowed text-gray-500 select-none pointer-events-none' : 'bg-white text-gray-700' }}"
            >{{ $note->content ?? '' }}</textarea>

            <div class="mt-auto border-t border-gray-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($canEdit)
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

                <button type="button"
                    onclick="goBackFromEditor()"
                    class="px-5 py-3 rounded-2xl bg-black text-white font-semibold hover:bg-gray-800 active:scale-95 transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </button>
            </div>

            @if($canEdit)
                <div id="imagePanel" class="hidden mt-4 rounded-3xl border border-gray-200 bg-gray-50 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900">Images</h3>
                            <p class="text-sm text-gray-500">Upload hình ảnh cho ghi chú.</p>
                        </div>

                        <label class="w-11 h-11 rounded-2xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition flex items-center justify-center cursor-pointer">
                            <i class="fa-solid fa-plus"></i>
                            <input type="file" id="imageInput" multiple accept="image/*" class="hidden">
                        </label>
                    </div>

                    <button type="button" id="uploadBtn"
                        class="hidden mb-4 px-5 py-3 rounded-2xl bg-black text-white text-sm font-semibold hover:bg-gray-800 active:scale-95 transition">
                        Upload Images
                    </button>

                    <div id="imagePreview" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @if(isset($note))
                            @foreach($note->images as $img)
                                <div id="img-{{ $img->id }}" class="relative group overflow-hidden rounded-2xl border border-gray-200 bg-white">
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                        class="w-full h-32 object-cover transition group-hover:scale-105">

                                    <button type="button" onclick="event.stopPropagation(); deleteImage('{{ $img->id }}')"
                                        class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/70 text-white hidden group-hover:flex items-center justify-center hover:bg-black transition">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div id="labelPanel" class="hidden mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">Labels</h3>
                            <p class="text-xs text-gray-400">Gắn nhãn để phân loại ghi chú</p>
                        </div>

                        <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                            <i class="fa-solid fa-tag text-sm"></i>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-3" id="selectedLabelsContainer">
                        @if(isset($note))
                            @foreach($note->labels as $label)
                                <span id="badge-{{ $label->id }}"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-900 text-white text-xs font-semibold">
                                    {{ $label->name }}

                                    <button type="button" onclick="detachLabel('{{ $label->id }}')"
                                        class="w-4 h-4 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </button>
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="relative flex items-center gap-2">
                        <div class="flex flex-1 items-center px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-gray-200 transition">
                            <i class="fa-solid fa-plus text-gray-400 mr-2 text-sm"></i>

                            <input type="text" id="newLabel"
                                placeholder="Add label..."
                                autocomplete="off"
                                class="w-full text-sm outline-none border-none bg-transparent focus:ring-0 text-gray-800 placeholder-gray-400"
                                oninput="showSuggestions(this.value)">
                        </div>

                        <button type="button" onclick="createLabel()"
                            class="px-4 py-2.5 rounded-xl bg-black text-white text-sm font-semibold hover:bg-gray-800 active:scale-95 transition">
                            Add
                        </button>

                        <div id="labelSuggestions"
                            class="absolute z-50 left-0 right-[70px] top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl hidden max-h-40 overflow-y-auto">
                        </div>
                    </div>
                </div>

                <div id="lockBox" class="hidden mt-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Set Password</h4>
                            <p class="text-xs text-gray-400">Bảo vệ ghi chú bằng mật khẩu</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <input type="password" id="lockPassword" placeholder="Password"
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                            focus:bg-white focus:ring-2 focus:ring-gray-200 outline-none text-sm transition">

                        <input type="password" id="lockConfirm" placeholder="Confirm password"
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                            focus:bg-white focus:ring-2 focus:ring-gray-200 outline-none text-sm transition">
                    </div>

                    <button type="button" onclick="setPassword()"
                        class="w-full mt-4 px-4 py-2.5 rounded-xl bg-black text-white text-sm font-semibold
                        hover:bg-gray-800 active:scale-95 transition">
                        Confirm Lock
                    </button>
                </div>

                <div id="unlockBox" class="hidden mt-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center">
                            <i class="fa-solid fa-unlock text-sm"></i>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 text-sm">Remove Password</h4>
                            <p class="text-xs text-gray-400">Gỡ mật khẩu khỏi ghi chú này</p>
                        </div>
                    </div>

                    <input type="password" id="unlockPassword" placeholder="Enter current password"
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                        focus:bg-white focus:ring-2 focus:ring-gray-200 outline-none text-sm transition">

                    <button type="button" onclick="removePassword()"
                        class="w-full mt-4 px-4 py-2.5 rounded-xl bg-black text-white text-sm font-semibold
                        hover:bg-gray-800 active:scale-95 transition">
                        Confirm Unlock
                    </button>
                </div>
            @endif

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

            window.safeCloseEditorModal = async function () {
                if (timer) clearTimeout(timer);

                if (!canEditValue) {
                    if (typeof closeEditorModal === 'function') {
                        closeEditorModal();
                    } else {
                        window.location.href = "{{ route('notes.shared') }}";
                    }
                    return;
                }

                try {
                    await autosave();

                    if (typeof window.refreshNotesIndex === 'function') {
                        await window.refreshNotesIndex();
                    }

                    if (typeof closeEditorModal === 'function') {
                        closeEditorModal();
                    } else {
                        window.location.href = "{{ route('notes.index') }}";
                    }
                } catch (e) {
                    console.error(e);
                }
            }

            window.toggleEditorPanel = function (panelId) {
                if (!canEditValue) return;

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
                if (!canEditValue) return;

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

                    const wasNewNote = !noteIdInput.value;

                    if (data.note_id && wasNewNote) {
                        noteIdInput.value = data.note_id;

                        if (typeof refreshNotesIndex === 'function') {
                            refreshNotesIndex();
                        }
                    }

                    if (!wasNewNote) {
                        updateNoteCardOnPage(data);
                    }

                    if (typeof window.refreshSharedNotes === 'function') {
                        window.refreshSharedNotes();
                    }


                    return data;
                })
                .catch(err => {
                    console.log("SAVE ERROR:", err);
                    saveStatus.textContent = "❌ Save failed";
                    throw err;
                });
            }

            function updateNoteCardOnPage(data) {
                if (!data || !data.note_id) return;

                const card = document.querySelector(`[data-note-id="${data.note_id}"]`);

                if (!card) return;

                const titleEl = card.querySelector('.card-title');
                const contentEl = card.querySelector('.card-content');

                if (titleEl) {
                    titleEl.textContent = data.title || 'Untitled';
                }

                if (contentEl) {
                    contentEl.textContent = data.content || '';
                }
            }

            window.saveNoteAndBack = function () {
                if (timer) clearTimeout(timer);

                if (!canEditValue) {
                    if (typeof closeEditorModal === 'function') {
                        closeEditorModal();
                    } else {
                        window.location.href = "{{ route('notes.shared') }}";
                    }
                    return;
                }

                autosave()
                    .then(() => {
                        window.location.href = "{{ route('notes.index') }}";
                    })
                    .catch(() => {
                        console.error("Không thể lưu ghi chú.");
                    });
            }

            function scheduleSave() {
                if (!canEditValue) return;
                if (timer) clearTimeout(timer);
                timer = setTimeout(autosave, 1000);
            }

            if (canEditValue) {
                if (titleInput) titleInput.addEventListener("input", scheduleSave);
                if (contentInput) contentInput.addEventListener("input", scheduleSave);
            }

            if (canEditValue && imageInput) {
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

            if (canEditValue && uploadBtn) {
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

                        saveStatus.textContent = "Saved";
                    });
                });
            }

            window.deleteImage = function (id) {
                if (!canEditValue) return;

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
                if (!canEditValue) return;
                loadLabels();
                if (suggestions) suggestions.classList.remove('hidden');
            }

            if (canEditValue && labelInput) {
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
                if (!canEditValue || !suggestions) return;

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

            window.createLabel = async function () {
                if (!canEditValue || !labelInput) return;

                const name = labelInput.value.trim();
                if (!name) return;

                try {
                    // nếu note mới chưa có id thì save trước
                    if (!noteIdInput.value && typeof autosave === 'function') {
                        await autosave();
                    }

                    const noteId = noteIdInput.value;

                    if (!noteId) {
                        alert("Note chưa được lưu, vui lòng thử lại sau 1 giây.");
                        return;
                    }

                    const labelRes = await fetch('/labels', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ name })
                    });

                    const data = await labelRes.json();
                    const label = data.label || data;

                    if (!label.id) {
                        console.log("LABEL RESPONSE:", data);
                        alert("Không lấy được label id.");
                        return;
                    }

                    const attachRes = await fetch(`/notes/${noteId}/labels`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ label_ids: [label.id] })
                    });

                    if (!attachRes.ok) {
                        throw new Error("Attach label failed");
                    }

                    renderLabelBadge(label.id, label.name);

                    labelInput.value = "";
                    if (suggestions) suggestions.classList.add('hidden');

                    if (typeof window.refreshNotesIndex === 'function') {
                        window.refreshNotesIndex();
                    }

                } catch (err) {
                    console.error("CREATE LABEL ERROR:", err);
                    alert("Không thể tạo/gắn label.");
                }
            }

            function attachLabelToNote(id, name) {
                if (!canEditValue) return;

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
                .then(() => {
                    // hiện ngay trong editor
                    renderLabelBadge(id, name);

                    // refresh index/card/filter ngay
                    if (typeof window.refreshNotesIndex === 'function') {
                        window.refreshNotesIndex();
                    }
                })
                .catch(err => console.error("ATTACH ERROR:", err));
            }

            function renderLabelBadge(id, name) {
                if (!canEditValue) return;
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
                if (!canEditValue) return;

                const noteId = noteIdInput.value;

                fetch(`/notes/${noteId}/labels/${labelId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(() => {
                    const el = document.getElementById(`badge-${labelId}`);
                    if (el) {
                        el.classList.add('opacity-0', 'scale-75', 'transition');
                        setTimeout(() => el.remove(), 200);
                    }

                    if (typeof window.refreshNotesIndex === 'function') {
                        window.refreshNotesIndex();
                    }
                });
            }

            window.setPassword = function () {
                if (!canEditValue) return;

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

                    document.getElementById('lockBox')?.classList.add('hidden');
                    updateLockIcon(true);

                    // cập nhật lại index/card ngay
                    if (typeof window.refreshNotesIndex === 'function') {
                        window.refreshNotesIndex();
                    }
                });
            }

            window.removePassword = function () {
                if (!canEditValue) return;

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

                    document.getElementById('unlockBox')?.classList.add('hidden');
                    updateLockIcon(false);

                    // cập nhật lại index/card ngay
                    if (typeof window.refreshNotesIndex === 'function') {
                        window.refreshNotesIndex();
                    }
                })
                .catch(() => {
                    alert("Sai mật khẩu!");
                });
            }

            const currentUserId = @json(auth()->id());
            const noteId = noteIdInput ? noteIdInput.value : null;

            let isTypingRealtime = false;

            if (canEditValue && titleInput) {
                titleInput.addEventListener('input', () => {
                    isTypingRealtime = true;
                    setTimeout(() => isTypingRealtime = false, 1000);
                });
            }

            if (canEditValue && contentInput) {
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

                        if (titleInput) titleInput.value = e.title ?? '';
                        if (contentInput) contentInput.value = e.content ?? '';
                        if (saveStatus && canEditValue) saveStatus.textContent = "Updated from another user";
                    });
            }
        })();

        window.applyEditorStyle = function () {
            const theme = localStorage.getItem('theme') || 'light';
            const noteColor = localStorage.getItem('noteColor') || 'bg-white';

            const editor = document.getElementById('editorContent');
            const shell = editor?.querySelector('.editor-shell');

            const colorClasses = [
                'bg-white',
                'bg-gray-50',
                'bg-yellow-50',
                'bg-orange-50',
                'bg-green-50',
                'bg-blue-50',
                'bg-purple-50',
                'bg-rose-50',
                'bg-gray-800'
            ];

            [editor, shell].forEach(el => {
                if (!el) return;

                el.classList.remove(...colorClasses);
                el.classList.remove('editor-dark');
            });

            if (theme === 'dark') {
                editor?.classList.add('editor-dark');
                shell?.classList.add('editor-dark');
                return;
            }

            editor?.classList.add(noteColor);
            shell?.classList.add(noteColor);
        }

        window.applyEditorStyle();

        window.goBackFromEditor = function () {
            if (typeof closeEditorModal === 'function') {
                closeEditorModal();
                return;
            }

            if (document.referrer && document.referrer.includes('/notes/shared')) {
                window.location.href = "{{ route('notes.shared') }}";
                return;
            }

            window.location.href = "{{ route('notes.index') }}";
        }
    </script>
</div>