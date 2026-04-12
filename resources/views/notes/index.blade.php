<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold text-gray-800">Your Notes</h1>

            <div class="flex items-center gap-3">

                <!-- 🔥 TOGGLE VIEW -->
                <button onclick="toggleView()"
                        class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    🔳
                </button>

                <a href="{{ route('notes.editor') }}"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                    + Create Note
                </a>

            </div>
        </div>

        <!-- THÊM SEARCH Ở ĐÂY -->
        <input
            type="text"
            id="searchInput"
            placeholder="Search notes..."
            class="w-full mb-6 px-4 py-2 border rounded-lg"
        />

        @if ($notes->count() == 0)
            <p class="text-gray-500 text-lg">You have no notes yet.</p>
        @else

            <!-- GRID LAYOUT -->
            <div id="notesContainer" 
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($notes as $note)
                    <div click="window.location='{{ route('notes.editor.edit', $note->id) }}'"

                        class="cursor-pointer relative group p-4 rounded-xl shadow-md border bg-yellow-50 hover:shadow-lg hover:-translate-y-1 transition transform duration-150">
                        
                        <!-- TITLE -->
                        <h2 class="text-xl font-semibold mb-2 text-gray-800">
                            {{ $note->title }}
                        </h2>

                        <!-- CONTENT -->
                        <p class="text-gray-700 text-sm line-clamp-3">
                            {{ $note->content }}
                        </p>

                        <!-- LABEL -->
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($note->labels as $label)
                                <span class="text-xs bg-gray-200 px-2 py-1 rounded">
                                    {{ $label->name }}
                                </span>
                            @endforeach
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="absolute top-3 right-3 hidden group-hover:flex space-x-3">

                            <!-- Pin -->
                            <button onclick="event.stopPropagation(); togglePin('{{ $note->id }}')"
                                    class="text-gray-600 hover:text-yellow-500">
                                {{ $note->is_pinned ? '📌' : '📍' }}
                            </button>

                            <!-- EDIT -->
                            <a href="{{ route('notes.editor.edit', $note->id) }}"
                            onclick="event.stopPropagation()"
                            class="text-blue-600 hover:text-blue-800">
                                ✏️
                            </a>

                            <!-- DELETE -->
                            <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                onclick="event.stopPropagation()"
                                onsubmit="return confirm('Delete this note?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800">
                                    🗑️
                                </button>
                            </form>

                        </div>
                    </div>
                @endforeach
                
            </div>

        @endif

    </div>

    <script>
        // PIN
        function togglePin(id) {
            console.log("CLICK PIN:", id);

            fetch(`/notes/${id}/pin`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
            })
            .then(res => res.json())
            .then(() => location.reload());
        }

        // SEARCH
        let timer = null;

        const searchInput = document.getElementById('searchInput');
        const notesContainer = document.querySelector('.grid');

        searchInput.addEventListener('input', function () {
            if (timer) clearTimeout(timer);

            // 🔥 SHOW LOADING Ở ĐÂY
            notesContainer.innerHTML = "<p class='text-gray-400'>Searching...</p>";

            timer = setTimeout(() => {
                fetch(`/notes/search?q=${this.value}`)
                    .then(res => res.json())
                    .then(data => renderNotes(data));
            }, 300);
        });

        function renderNotes(notes) {
            const q = searchInput.value.toLowerCase();

            if (notes.length === 0) {
                notesContainer.innerHTML = "<p class='text-gray-500'>No notes found</p>";
                return;
            }
            

            let html = "";

            notes.forEach(note => {

                const q = searchInput.value;

                // ✅ CẮT TRƯỚC
                let rawContent = note.content.substring(0, 100);

                // ✅ highlight title
                let title = note.title.replace(
                    new RegExp(q, "gi"),
                    match => `<mark class="bg-yellow-200">${match}</mark>`
                );

                // ✅ highlight content (sau khi cắt)
                let content = rawContent.replace(
                    new RegExp(q, "gi"),
                    match => `<mark class="bg-yellow-200">${match}</mark>`
                );

                html += `
                <div onclick="window.location='/notes/editor/${note.id}'"
                    class="cursor-pointer p-4 rounded-xl shadow-md border bg-yellow-50 hover:shadow-lg">

                    <h2 class="text-xl font-semibold mb-2">${title}</h2>
                    <p class="text-sm text-gray-700">${content}</p>

                </div>
                `;
            });

            notesContainer.innerHTML = html;

            applyView(view);
        }

        // Grid
        const container = document.getElementById('notesContainer');

        // load trạng thái đã lưu
        let view = localStorage.getItem('view') || 'grid';
        applyView(view);

        function toggleView() {
            view = (view === 'grid') ? 'list' : 'grid';
            localStorage.setItem('view', view);
            applyView(view);
        }

        function applyView(mode) {

            if (mode === 'list') {
                container.className = "flex flex-col gap-3";

                // chỉnh từng note thành dạng list
                document.querySelectorAll('#notesContainer > div').forEach(note => {
                    note.classList.remove('p-4');
                    note.classList.add('p-3', 'flex', 'justify-between', 'items-center');
                });

            } else {
                container.className = "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4";

                document.querySelectorAll('#notesContainer > div').forEach(note => {
                    note.classList.remove('flex', 'justify-between', 'items-center');
                    note.classList.add('p-4');
                });
            }

            document.querySelector("button").textContent = (view === 'grid') ? '📋' : '🔳';
        }

        // SIBAR LABEL
        let selectedFilterLabels = [];

        // load labels sidebar
        function loadSidebarLabels() {
            fetch('/labels')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('sidebarLabels');
                container.innerHTML = "";

                data.forEach(label => {
                    container.innerHTML += `
                        <div onclick="toggleFilter(${label.id})"
                            id="filter-${label.id}"
                            class="cursor-pointer px-3 py-1 rounded hover:bg-gray-200">
                            ${label.name}
                        </div>
                    `;
                });
            });
        }

        function toggleFilter(id) {
            const el = document.getElementById(`filter-${id}`);

            if (selectedFilterLabels.includes(id)) {
                selectedFilterLabels = selectedFilterLabels.filter(l => l !== id);
                el.classList.remove('bg-blue-500','text-white');
            } else {
                selectedFilterLabels.push(id);
                el.classList.add('bg-blue-500','text-white');
            }

            fetchFilteredNotes();
        }

        function fetchFilteredNotes() {

            let url = "/notes/filter?labels=" + selectedFilterLabels.join(',');

            fetch(url)
            .then(res => res.json())
            .then(notes => {

                const container = document.getElementById('notesContainer');
                container.innerHTML = "";

                notes.forEach(note => {
                    container.innerHTML += `
                        <div class="p-4 bg-yellow-50 rounded shadow cursor-pointer"
                            onclick="window.location='/notes/editor/${note.id}'">

                            <h2 class="font-bold">${note.title}</h2>
                            <p>${note.content}</p>

                        </div>
                    `;
                });
            });
        }
        loadSidebarLabels();

    </script>
</x-app-layout>



