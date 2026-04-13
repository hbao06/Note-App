<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Your Notes</h1>
            <div class="flex items-center gap-3">
                <button onclick="toggleView()" id="viewToggleBtn" class="p-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 shadow-sm transition">🔳</button>
                <a href="{{ route('notes.editor') }}" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:bg-blue-700 transition">+ Create Note</a>
            </div>
        </div>

        <div class="relative mb-8">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" id="searchInput" placeholder="Search notes..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm" />
        </div>

        <div class="mb-8 overflow-x-auto">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="text-xs font-bold text-gray-400 uppercase mr-2 tracking-widest">Filters Label:</span>

                <button onclick="filterByLabel('all')" 
                    data-id="all"
                    class="filter-chip active px-4 py-1.5 rounded-full border bg-blue-600 text-white text-sm font-medium shadow-sm transition">
                    All
                </button>

                @foreach($allLabels as $label)
                    <button onclick="filterByLabel('{{ $label->id }}')"
                        data-id = "{{ $label->id }}"
                        class="filter-chip px-4 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-sm hover:border-blue-400 transition shadow-sm">
                        {{ $label->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <div id="notesContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($notes as $note)
                @php $uniqueLabels = $note->labels->unique('name'); @endphp

                
                <div onclick="window.location='{{ route('notes.editor.edit', $note->id) }}'"
                    data-labels="{{ $uniqueLabels->pluck('id')->join(',') }}"
                    class="note-card group relative bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-200 cursor-default flex flex-col min-h-[140px]" >
                    
                    <button onclick="event.stopPropagation(); togglePin('{{ $note->id }}')" 
                            class="absolute top-2 right-2 p-1.5 rounded-full hover:bg-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-thumbtack 
                                {{ $note->is_pinned ? 'text-gray-900 rotate-45' : 'text-gray-400' }}">
                            </i>
                    </button>

                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-1 line-clamp-2">
                            {{ $note->title ?: 'Untitled' }}
                        </h2>
                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-6">
                            {{ $note->content }}
                        </p>
                        <!-- 🆕 ADD TIME HERE -->
                        <div class="mt-2 relative group/time inline-flex items-center gap-1 text-[11px] text-gray-400 
                                    hover:text-gray-700 transition-all duration-200 cursor-default">
                            <i class="fa-regular fa-clock"></i>

                            <span class="note-time" data-time="{{ $note->updated_at->format('c') }}"></span>

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-2 hidden group-hover/time:block 
                                bg-gray-900 text-white text-xs px-2 py-1 rounded-md shadow-lg whitespace-nowrap z-50">
                                <span class="note-full-time" data-time="{{ $note->updated_at->format('c') }}"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-auto">
                        @foreach($uniqueLabels as $label)
                            <span class="px-3 py-1 bg-[#f1f3f4] text-[#3c4043] text-[12px] font-medium rounded-full border border-transparent hover:bg-gray-200 transition-colors">
                                {{ $label->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="absolute bottom-3 right-3 flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity text-gray-500">
                        <form action="{{ route('notes.destroy', $note) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="event.stopPropagation()" class="hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Logic lọc và search (Giữ nguyên các hàm bạn đã có nhưng cập nhật biến chọn card)
        const notesContainer = document.getElementById('notesContainer');
        
        function filterByLabel(labelId) {
            const cards = document.querySelectorAll('.note-card');

            cards.forEach(card => {
                const noteLabels = card.getAttribute('data-labels').split(',');

                if (labelId === 'all' || noteLabels.includes(labelId)) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            });

            // UI active button
            document.querySelectorAll('.filter-chip').forEach(btn => {
                const isMatch = btn.getAttribute('data-id') === labelId;

                btn.className = isMatch 
                    ? "filter-chip px-4 py-1.5 rounded-full border bg-blue-600 text-white text-sm font-medium shadow-sm transition"
                    : "filter-chip px-4 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-sm hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600 transition-all duration-200 shadow-sm";
            });
        }

        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            document.querySelectorAll('.note-card').forEach(card => {
                const titleEl = card.querySelector('h2');
                const contentEl = card.querySelector('p');

                // lưu text gốc (tránh bị lồng mark nhiều lần)
                if (!titleEl.dataset.original) {
                    titleEl.dataset.original = titleEl.innerText;
                }
                if (!contentEl.dataset.original) {
                    contentEl.dataset.original = contentEl.innerText;
                }

                const titleText = titleEl.dataset.original;
                const contentText = contentEl.dataset.original;

                if (!query) {
                    titleEl.innerHTML = titleText;
                    contentEl.innerHTML = contentText;
                    card.style.display = "";
                    return;
                }

                const regex = new RegExp(`(${query})`, 'gi');

                const newTitle = titleText.replace(regex, `<mark class="bg-blue-200 text-blue-900 px-1 rounded">$1</mark>`);
                const newContent = contentText.replace(regex, `<mark class="bg-blue-200 text-blue-900 px-1 rounded">$1</mark>`);

                const isMatch = titleText.toLowerCase().includes(query) || contentText.toLowerCase().includes(query);

                titleEl.innerHTML = newTitle;
                contentEl.innerHTML = newContent;

                card.style.display = isMatch ? "" : "none";
            });
        });

        function togglePin(id) {
            fetch(`/notes/${id}/pin`, { method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" } }).then(() => location.reload());
        }

        // View Mode
        let currentView = localStorage.getItem('view') || 'grid';
        function applyView(mode) {
            if (mode === 'list') {
                notesContainer.className = "flex flex-col gap-3 max-w-2xl mx-auto";
            } else {
                notesContainer.className = "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4";
            }
            document.getElementById('viewToggleBtn').innerHTML =
                (mode === 'grid')
                    ? '<i class="fa-solid fa-list"></i>'
                    : '<i class="fa-solid fa-table-cells"></i>';
        }
        function toggleView() {
            currentView = (currentView === 'grid') ? 'list' : 'grid';
            localStorage.setItem('view', currentView);
            applyView(currentView);
        }
        applyView(currentView);

        // DATE - TIME
        function timeAgo(dateString) {
            const now = new Date();
            const past = new Date(dateString);
            const diff = Math.floor((now - past) / 1000);

            if (diff < 10) return "Just now";

            const minutes = Math.floor(diff / 60);
            const hours = Math.floor(diff / 3600);
            const days = Math.floor(diff / 86400);
            const months = Math.floor(diff / 2592000);

            if (minutes < 1) return diff + " seconds ago";
            if (minutes < 60) return minutes + (minutes === 1 ? " minute ago" : " minutes ago");

            if (hours < 24) return hours + (hours === 1 ? " hour ago" : " hours ago");

            if (days === 1) return "yesterday";
            if (days < 30) return days + " days ago";

            if (months < 12) return months + (months === 1 ? " month ago" : " months ago");

            const years = Math.floor(months / 12);
            return years + (years === 1 ? " year ago" : " years ago");
        }

        function formatFull(dateString) {
            const d = new Date(dateString);

            return d.toLocaleString('vi-VN', {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function updateTimes() {
            document.querySelectorAll('.note-time').forEach(el => {
                el.textContent = timeAgo(el.dataset.time);
            });

            document.querySelectorAll('.note-full-time').forEach(el => {
                el.textContent = formatFull(el.dataset.time);
            });
        }

        // chạy lần đầu
        updateTimes();

        // cập nhật mỗi phút
        setInterval(updateTimes, 10000);
    </script>
</x-app-layout>