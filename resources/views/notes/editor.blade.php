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
            <div class="mt-4">
                <label class="font-semibold">Labels</label>

                <div class="flex gap-2 mt-2">
                    <!-- input -->
                    <input type="text" id="newLabel"
                        placeholder="Add label..."
                        class="border px-3 py-1 rounded w-full">

                    <!-- 🔥 NÚT ADD -->
                    <button onclick="event.stopPropagation(); createLabel()"
                        class="px-4 bg-blue-500 text-white rounded">
                        Add
                    </button>
                </div>

                <!-- danh sách label -->
                <div id="labelList" class="flex flex-wrap gap-2 mt-3"></div>
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
        let selectedLabels = [];
        

        // load labels
        function loadLabels() {
            fetch('/labels')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('labelList');
                container.innerHTML = "";

                data.forEach(label => {
                    container.innerHTML += `
                        <span onclick="toggleLabel(${label.id})"
                            class="px-3 py-1 rounded-full border cursor-pointer"
                            id="label-${label.id}">
                            ${label.name}
                        </span>
                    `;
                });
            });
        }

        // toggle chọn label
        function toggleLabel(id) {
            const el = document.getElementById(`label-${id}`);

            if (selectedLabels.includes(id)) {
                selectedLabels = selectedLabels.filter(l => l !== id);
                el.classList.remove('bg-blue-500','text-white');
            } else {
                selectedLabels.push(id);
                el.classList.add('bg-blue-500','text-white');
            }

            saveLabels();
        }

        // save labels to note
        function saveLabels() {
            if (!noteIdInput.value) return;

            fetch(`/notes/${noteIdInput.value}/labels`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ label_ids: selectedLabels })
            });
        }

        // create new label
        function createLabel() {

            console.log("CLICK ADD"); // debug

            const input = document.getElementById('newLabel');
            const name = input.value.trim();

            if (!name) {
                alert("Please enter label name");
                return;
            }

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
                console.log("CREATED:", data);

                input.value = "";
                loadLabels(); // reload list
            });
        }

        // load khi mở trang
        loadLabels();

    </script>
</x-app-layout>