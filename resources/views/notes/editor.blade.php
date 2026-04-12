<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">

        <!-- CARD WRAPPER -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-200 relative">

            <!-- AUTOSAVE STATUS -->
            <div id="saveStatus" class="absolute top-3 right-4 text-sm text-gray-500">
                Saved
            </div>

            <!-- TITLE INPUT -->
            <input
                type="text"
                id="noteTitle"
                value="{{ $note->title ?? '' }}"
                placeholder="Title..."
                class="w-full text-2xl font-semibold text-gray-800 outline-none border-none mb-4"
            />

            <!-- CONTENT INPUT -->
            <textarea
                id="noteContent"
                placeholder="Write something..."
                class="w-full text-gray-700 outline-none border-none resize-none min-h-[200px]"
            >{{ $note->content ?? '' }}</textarea>

            <!-- IMAGE UPLOAD -->
            <div class="mt-4">
                <label class="block font-semibold text-gray-700 mb-2">Images</label>

                <input type="file" id="imageInput" multiple accept="image/*">

                <!-- NÚT UPLOAD ẢNH -->
                <button id="uploadBtn"
                        class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 hidden">
                    Upload Images
                </button>
            </div>

            <!-- IMAGE PREVIEW -->
            <div id="imagePreview" class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-16">
                @if(isset($note))
                    @foreach($note->images as $img)
                        <div id="img-{{ $img->id }}" class="relative group">
                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                class="w-full h-32 object-cover rounded-lg shadow">

                            <!-- DELETE BUTTON -->
                            <button onclick="event.stopPropagation(); deleteImage('{{ $img->id }}')"
                                    class="absolute top-1 right-1 bg-black/60 text-white text-sm px-2 py-1 rounded hidden group-hover:block">
                                ✕
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- LABEL -->
            <div class="mt-6 border-t pt-4">
                <div class="flex flex-wrap gap-2 mb-3" id="selectedLabelsContainer">
                    @if(isset($note))
                        @foreach($note->labels as $label)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-sm font-medium text-gray-700 border border-gray-300 group">
                                {{ $label->name }}
                                <button onclick="detachLabel('{{ $label->id }}')" class="ml-2 text-gray-400 hover:text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </span>
                        @endforeach
                    @endif
                </div>

                <div class="relative group">
                    <div class="flex items-center text-gray-500 focus-within:text-gray-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <input type="text" id="newLabel" 
                            placeholder="Add label..." 
                            class="w-full text-sm outline-none border-none focus:ring-0 bg-transparent"
                            onkeypress="if(event.key === 'Enter') createLabel()">
                    </div>
                    
                    <div id="labelSuggestions" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto">
                        </div>
                </div>
            </div>

            <!-- HIDDEN NOTE ID -->
            <input type="hidden" id="noteId" value="{{ $note->id ?? '' }}">

            <!-- ACTION BUTTONS -->
            <div class="flex justify-end mt-6">
                <a href="{{ route('notes.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Back
                </a>
            </div>

        </div>
    </div>

    <!-- AUTOSAVE SCRIPT -->
    <script>
        let timer = null;

        const titleInput = document.getElementById('noteTitle');
        const contentInput = document.getElementById('noteContent');
        const noteIdInput = document.getElementById('noteId');
        const saveStatus = document.getElementById('saveStatus');

        function autosave() {
            saveStatus.textContent = "Saving...";

            fetch("{{ route('notes.autosave') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id: noteIdInput.value,
                    title: titleInput.value,
                    content: contentInput.value
                })
            })
            .then(res => res.json())
            .then(data => {
                saveStatus.textContent = "Saved";

                // Nếu là note mới → set ID để đổi sang chế độ update
                if (data.note_id && !noteIdInput.value) {
                    noteIdInput.value = data.note_id;
                    window.history.replaceState({}, "", "/notes/editor/" + data.note_id);
                }
            });
        }

        // Debounce 1 giây
        function scheduleSave() {
            if (timer) clearTimeout(timer);
            timer = setTimeout(autosave, 1000);
        }

        titleInput.addEventListener("input", scheduleSave);
        contentInput.addEventListener("input", scheduleSave);
    </script>

    <!-- IMAGE UPLOAD + DELETE -->
    <script>
        // UPLOAD IMAGES
        // SHOW UPLOAD BUTTON WHEN FILES SELECTED
        const imageInput = document.getElementById('imageInput');
        const uploadBtn  = document.getElementById('uploadBtn');

        imageInput.addEventListener('change', function () {

            // preview trước khi upload
            const preview = document.getElementById('imagePreview');

            for (let file of this.files) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.classList.add('relative', 'group');

                    div.innerHTML = `
                        <img src="${e.target.result}"
                            class="w-full h-32 object-cover rounded-lg shadow opacity-70">
                    `;

                    preview.prepend(div);
                }

                reader.readAsDataURL(file);
            }

            uploadBtn.classList.remove('hidden');
        });

        // UPLOAD IMAGES WHEN CLICK
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

                // load lại để hiển thị ảnh
                location.reload();
            });
        });

        

        // DELETE IMAGE
        function deleteImage(id) {
            fetch(`/notes/images/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(() => {
                // ❗ XÓA LUÔN TRÊN UI (KHÔNG CẦN RELOAD)
                document.getElementById('img-' + id).remove();
            })
            .catch(err => console.log(err));
        }

        // LABEL
        // let selectedLabels = [];
        

        // // load labels
        // function loadLabels() {
        //     fetch('/labels')
        //     .then(res => res.json())
        //     .then(data => {
        //         const container = document.getElementById('labelList');
        //         container.innerHTML = "";

        //         data.forEach(label => {
        //             container.innerHTML += `
        //                 <span onclick="toggleLabel(${label.id})"
        //                     class="px-3 py-1 rounded-full border cursor-pointer"
        //                     id="label-${label.id}">
        //                     ${label.name}
        //                 </span>
        //             `;
        //         });
        //     });
        // }

        // // toggle chọn label
        // function toggleLabel(id) {
        //     const el = document.getElementById(`label-${id}`);

        //     if (selectedLabels.includes(id)) {
        //         selectedLabels = selectedLabels.filter(l => l !== id);
        //         el.classList.remove('bg-blue-500','text-white');
        //     } else {
        //         selectedLabels.push(id);
        //         el.classList.add('bg-blue-500','text-white');
        //     }

        //     saveLabels();
        // }

        // // save labels to note
        // function saveLabels() {
        //     if (!noteIdInput.value) return;

        //     fetch(`/notes/${noteIdInput.value}/labels`, {
        //         method: "POST",
        //         headers: {
        //             "Content-Type": "application/json",
        //             "X-CSRF-TOKEN": "{{ csrf_token() }}"
        //         },
        //         body: JSON.stringify({ label_ids: selectedLabels })
        //     });
        // }

        // // create new label
        // function createLabel() {

        //     console.log("CLICK ADD"); // debug

        //     const input = document.getElementById('newLabel');
        //     const name = input.value.trim();

        //     if (!name) {
        //         alert("Please enter label name");
        //         return;
        //     }

        //     fetch('/labels', {
        //         method: "POST",
        //         headers: {
        //             "Content-Type": "application/json",
        //             "X-CSRF-TOKEN": "{{ csrf_token() }}"
        //         },
        //         body: JSON.stringify({ name: name })
        //     })
        //     .then(res => res.json())
        //     .then(data => {
        //         console.log("CREATED:", data);

        //         input.value = "";
        //         loadLabels(); // reload list
        //     });
        // }

        // // load khi mở trang
        // loadLabels();

        // --- LABEL LOGIC (GOOGLE KEEP STYLE) ---
        const labelInput = document.getElementById('newLabel');
        const suggestions = document.getElementById('labelSuggestions');

        // Hiện danh sách khi focus
        labelInput.addEventListener('focus', () => {
            loadLabels();
            suggestions.classList.remove('hidden');
        });

        // Tạo nhãn mới khi nhấn Enter
        labelInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                createLabel();
            }
        });

        // Đóng gợi ý khi click ngoài
        document.addEventListener('click', (e) => {
            if (!labelInput.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.classList.add('hidden');
            }
        });

        function loadLabels() {
            fetch('/labels')
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = "";
                data.forEach(label => {
                    const div = document.createElement('div');
                    div.className = "px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm flex justify-between items-center border-b last:border-0";
                    div.innerHTML = `<span>${label.name}</span> <span class="text-gray-300 text-xs">Apply</span>`;
                    div.onclick = () => {
                        attachLabelToNote(label.id, label.name);
                        suggestions.classList.add('hidden');
                        labelInput.value = "";
                    };
                    suggestions.appendChild(div);
                });
            });
        }

        function createLabel() {
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
            if (!noteIdInput.value) return;
            fetch(`/notes/${noteIdInput.value}/labels`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ label_ids: [id] })
            })
            .then(() => renderLabelBadge(id, name));
        }

        function renderLabelBadge(id, name) {
            if (document.getElementById(`badge-${id}`)) return;
            const container = document.getElementById('selectedLabelsContainer');
            const span = document.createElement('span');
            span.id = `badge-${id}`;
            span.className = "inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-xs font-medium text-gray-700 border border-gray-200";
            span.innerHTML = `${name} 
                <button onclick="detachLabel('${id}')" class="ml-2 text-gray-400 hover:text-red-500">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>`;
            container.appendChild(span);
        }

        // Logic gỡ nhãn (Bạn cần viết API route xóa pivot note_label này)
        function detachLabel(labelId) {
             // Gọi API xóa label khỏi note ở đây (tùy thuộc route của bạn)
             // Tạm thời xóa trên UI:
             document.getElementById(`badge-${labelId}`).remove();
        }

    </script>
</x-app-layout>