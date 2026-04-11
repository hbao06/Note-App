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
            <div id="imagePreview" class="mt-4 grid grid-cols-2 gap-4">
                @if(isset($note))
                    @foreach($note->images as $img)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                 class="rounded-lg shadow">

                            <button x-on:onclick="deleteImage({{ $img->id }})"
                                    class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded hidden group-hover:block">
                                ✕
                            </button>
                        </div>
                    @endforeach
                @endif
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
            if (this.files.length > 0) {
                uploadBtn.classList.remove('hidden');
            }
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
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
            })
            .then(() => location.reload());
        }

       

    </script>

</x-app-layout>