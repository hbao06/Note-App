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
                    class="filter-chip active px-4 py-1.5 rounded-full border bg-blue-600 text-white text-sm font-medium shadow-sm transition">
                    All
                </button>

                @foreach($allLabels as $label)
                    <button onclick="filterByLabel('{{ $label->id }}')"
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
                    </div>

                    <div class="flex flex-wrap gap-2 mt-auto">
                        @foreach($uniqueLabels as $label)
                            <span class="px-3 py-1 bg-[#f1f3f4] text-[#3c4043] text-[12px] font-medium rounded-full border border-transparent hover:bg-gray-200 transition-colors">
                                {{ $label->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-4 mt-3 opacity-0 group-hover:opacity-100 transition-opacity text-gray-500">
                        <form action="{{ route('notes.destroy', $note) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="event.stopPropagation()" class="hover:text-red-600">🗑️</button>
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
                const isMatch = (labelId === 'all' && btn.innerText.includes('All')) 
                    || btn.getAttribute('data-id') === labelId;

                btn.className = isMatch 
                    ? "filter-chip px-4 py-1.5 rounded-full border bg-blue-600 text-white text-sm font-medium shadow-sm transition"
                    : "filter-chip px-4 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-sm hover:border-blue-400 transition shadow-sm";
            });
        }

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.note-card').forEach(card => {
                const content = card.innerText.toLowerCase();
                card.style.display = content.includes(query) ? "" : "none";
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
            document.getElementById('viewToggleBtn').textContent = (mode === 'grid') ? '📋' : '🔳';
        }
        function toggleView() {
            currentView = (currentView === 'grid') ? 'list' : 'grid';
            localStorage.setItem('view', currentView);
            applyView(currentView);
        }
        applyView(currentView);
    </script>
</x-app-layout>