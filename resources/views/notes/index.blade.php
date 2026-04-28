<x-app-layout>
    <div class="min-h-screen bg-gray-50 text-gray-900">
        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed left-0 top-0 z-40 h-screen w-72 bg-white border-r border-gray-200 backdrop-blur-xl transition-all duration-300 overflow-hidden">

            <!-- USER -->
            <div class="p-4 border-b border-gray-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-gray-900 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>

                <div class="sidebar-text">
                    <div class="font-semibold text-gray-900 truncate">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-xs text-gray-400 truncate">
                        {{ auth()->user()->email }}
                    </div>
                </div>
            </div>

            <!-- MENU -->
            <nav class="p-3 space-y-2">

                <a href="{{ route('profile.edit') }}" onclick="loadPage(event, this.href)"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-user w-5"></i>
                    <span class="sidebar-text">Profile</span>
                </a>

                <a href="{{ route('notes.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl bg-gray-100 text-gray-900 font-medium">
                    <i class="fa-solid fa-note-sticky w-5"></i>
                    <span class="sidebar-text">My Notes</span>
                </a>

                <a href="{{ route('notes.shared') }}" onclick="loadPage(event, this.href)"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-user-group w-5"></i>
                    <span class="sidebar-text">Shared with me</span>
                </a>

                <button onclick="openSettingsModal()"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-gear w-5"></i>
                    <span class="sidebar-text">Settings</span>
                </button>

            </nav>

            <!-- RECENT NOTES -->
            <div class="px-4 mt-4 sidebar-text">
                <h3 class="text-xs uppercase tracking-widest text-gray-400 mb-3">
                    Gần đây
                </h3>

                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                    @foreach($notes->take(6) as $recent)
                        <a href="{{ url('/notes/editor/' . $recent->id) }}"
                            class="block px-3 py-2 rounded-xl hover:bg-gray-100 transition">
                            <div class="text-sm text-gray-800 truncate">
                                {{ $recent->title ?: 'Untitled' }}
                            </div>
                            <div class="text-xs text-gray-400 truncate">
                                {{ Str::limit($recent->content, 32) }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- BOTTOM -->
            <div class="absolute bottom-4 left-0 right-0 px-3 space-y-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                        <i class="fa-solid fa-right-from-bracket w-5"></i>
                        <span class="sidebar-text">Logout</span>
                    </button>
                </form>

                <button onclick="toggleSidebar()"
                    class="w-full flex items-center justify-center px-3 py-3 rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                    <i id="sidebarToggleIcon" class="fa-solid fa-chevron-left"></i>
                </button>
            </div>
        </aside>

        <div class="pointer-events-none fixed inset-0 bg-white"></div>

        <div id="mainContent" class="relative py-8 px-6 transition-all duration-300 lg:ml-72">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
                <div>
                    <p class="text-sm text-gray-400 mb-1">Good Morning ✨</p>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900">Your Notes!</h1>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="toggleView()" id="viewToggleBtn"
                        class="p-3 bg-white border border-gray-200 text-gray-500 rounded-xl hover:bg-gray-100 transition shadow-sm">
                        🔳
                    </button>

                    <button onclick="openEditorModal('{{ route('notes.editor') }}')"
                        class="px-5 py-3 bg-gray-900 text-white font-semibold rounded-xl shadow-sm hover:bg-gray-700 active:scale-95 transition">
                        + Create Note
                    <button>
                </div>
            </div>

            <!-- SEARCH -->
            <div class="relative mb-8">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>

                <input type="text" id="searchInput" placeholder="Search notes..."
                    class="w-full pl-12 pr-4 py-4 border border-gray-200 bg-white rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-300 focus:border-gray-300 outline-none transition shadow-sm" />
            </div>

            <!-- FILTER LABELS -->
            <div class="mb-8 overflow-x-auto">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase mr-2 tracking-widest">
                        Filters Label:
                    </span>

                    <button onclick="filterByLabel('all')"
                        data-id="all"
                        class="filter-chip active px-4 py-2 rounded-full border border-gray-900 bg-gray-900 text-white text-sm font-medium shadow-sm transition">
                        All
                    </button>

                    @foreach($allLabels as $label)
                        <button onclick="filterByLabel('{{ $label->id }}')"
                            data-id="{{ $label->id }}"
                            class="filter-chip px-4 py-2 rounded-full border border-gray-200 bg-white text-gray-600 text-sm hover:bg-gray-100 hover:border-gray-300 transition shadow-sm">
                            {{ $label->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- NOTES GRID -->
            <div id="notesContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($notes as $note)
                    @php $uniqueLabels = $note->labels->unique('name'); @endphp

                    <div data-url="{{ url('/notes/editor/' . $note->id) }}"
                        onclick="openEditorModal(this.dataset.url)"
                        data-labels="{{ $uniqueLabels->pluck('id')->join(',') }}"
                        class="note-card group relative overflow-hidden bg-white border border-gray-200 rounded-[1.75rem] p-5 shadow-sm hover:shadow-md hover:-translate-y-1 hover:border-gray-300 hover:bg-gray-50 transition-all duration-300 cursor-pointer flex flex-col min-h-[250px]">

                        <!-- PIN -->
                        <button onclick="event.stopPropagation(); togglePin('{{ $note->id }}')"
                            class="absolute top-4 right-4 z-10 p-2 rounded-xl bg-gray-100 hover:bg-gray-200 opacity-0 group-hover:opacity-100 transition">
                            <i class="fa-solid fa-thumbtack {{ $note->is_pinned ? 'text-gray-900 rotate-45' : 'text-gray-400' }}"></i>
                        </button>

                        <!-- CONTENT -->
                        <div class="relative z-10 mb-4 pr-8">
                            <div class="flex items-center gap-2 mb-3 text-sm">
                                @if($note->sharedNotes->count() > 0)
                                    <span title="This note is shared" class="text-gray-500">
                                        <i class="fa-solid fa-user-group"></i>
                                    </span>
                                @endif

                                @if($note->note_password)
                                    <span title="This note is locked" class="text-yellow-500">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                @endif

                                @if($note->is_pinned)
                                    <span title="Pinned note" class="text-gray-500">
                                        <i class="fa-solid fa-thumbtack"></i>
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-2xl font-semibold text-gray-900 mb-3 line-clamp-2 group-hover:text-black transition">
                                {{ $note->title ?: 'Untitled' }}
                            </h2>

                            <p class="text-gray-500 text-sm leading-6 line-clamp-6">
                                {{ $note->content }}
                            </p>

                            <div class="mt-4 relative group/time inline-flex items-center gap-1 text-[11px] text-gray-400 hover:text-gray-600 transition cursor-default">
                                <i class="fa-regular fa-clock"></i>
                                <span class="note-time" data-time="{{ $note->updated_at->format('c') }}"></span>

                                <div class="absolute bottom-full mb-2 hidden group-hover/time:block bg-gray-900 text-white text-xs px-2 py-1 rounded-md shadow-lg whitespace-nowrap z-50 border border-gray-700">
                                    <span class="note-full-time" data-time="{{ $note->updated_at->format('c') }}"></span>
                                </div>
                            </div>
                        </div>

                        <!-- LABELS -->
                        <div class="relative z-10 flex flex-wrap gap-2 mt-auto pb-10">
                            @foreach($uniqueLabels as $label)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-[12px] font-medium rounded-full border border-gray-200 hover:bg-gray-200 transition">
                                    {{ $label->name }}
                                </span>
                            @endforeach
                        </div>

                        <!-- ACTIONS -->
                        <div class="absolute bottom-4 right-4 z-20 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">

                            <button onclick="event.stopPropagation(); openShareModal({{ $note->id }})"
                                title="Share note"
                                class="p-2 rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-800 transition">
                                <i class="fa-solid fa-share"></i>
                            </button>

                            <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa ghi chú này không?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="event.stopPropagation()"
                                    title="Delete note"
                                    class="p-2 rounded-xl bg-gray-100 text-red-400 hover:bg-red-50 hover:text-red-500 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SHARE MODAL -->
            <div id="shareModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
                <div class="bg-white border border-gray-200 w-full max-w-md p-6 rounded-2xl shadow-2xl text-gray-900">

                    <h2 class="text-xl font-bold mb-4">Share Note</h2>

                    <input type="text" id="shareEmails"
                        placeholder="Nhập email, cách nhau dấu phẩy"
                        class="w-full border border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 px-4 py-3 rounded-xl mb-3 focus:ring-2 focus:ring-gray-300 outline-none">

                    <select id="sharePermission"
                        class="w-full border border-gray-200 bg-gray-50 text-gray-900 px-4 py-3 rounded-xl mb-4 focus:ring-2 focus:ring-gray-300 outline-none">
                        <option value="read">Read only</option>
                        <option value="edit">Can edit</option>
                    </select>

                    <button onclick="shareNote()"
                        class="w-full bg-gray-900 text-white py-3 rounded-xl font-semibold mb-4 hover:bg-gray-700 transition">
                        Share
                    </button>

                    <div id="shareList" class="space-y-2 max-h-40 overflow-y-auto"></div>

                    <button onclick="closeShareModal()" class="mt-4 text-sm text-gray-400 hover:text-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- EDITOR MODAL -->
        <div id="editorModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 px-4">
            <div id="editorModalContent"
                class="w-full max-w-3xl h-[620px] overflow-hidden rounded-3xl bg-white shadow-2xl">
            </div>
        </div>

        <!-- SETTINGS MODAL -->
        <div id="settingsModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
            <div class="bg-white border border-gray-200 p-6 rounded-2xl w-full max-w-md text-gray-900 shadow-2xl">
                <h2 class="text-xl font-bold mb-4">User Settings</h2>

                <label class="block text-sm font-medium mb-1 text-gray-600">Font size</label>
                <select id="settingFontSize" class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-3 mb-4">
                    <option value="text-sm">Small</option>
                    <option value="text-base">Normal</option>
                    <option value="text-lg">Large</option>
                </select>

                <label class="block text-sm font-medium mb-1 text-gray-600">Note color</label>
                <select id="settingNoteColor" class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-3 mb-4">
                    <option value="bg-white">White</option>
                    <option value="bg-gray-50">Light Gray</option>
                    <option value="bg-gray-100">Gray</option>
                </select>

                <label class="block text-sm font-medium mb-1 text-gray-600">Theme</label>
                <select id="settingTheme" class="w-full border border-gray-200 bg-gray-50 rounded-xl px-3 py-3 mb-4">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                </select>

                <button onclick="saveSettings()" class="w-full bg-gray-900 text-white py-3 rounded-xl font-semibold hover:bg-gray-700 transition">
                    Save Settings
                </button>

                <button onclick="closeSettingsModal()" class="mt-3 text-sm text-gray-400 hover:text-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        async function openEditorModal(url) {
            const modal = document.getElementById('editorModal');
            const content = document.getElementById('editorModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            content.innerHTML = `
                <div class="p-10 text-center text-gray-400">
                    Loading editor...
                </div>
            `;

            const res = await fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const html = await res.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const editor = doc.querySelector('#editorContent');

            content.innerHTML = editor ? editor.outerHTML : html;

            content.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
                newScript.remove();
            });
        }
                

        function reInitEditorScripts() {
            // re-bind autosave
            if (typeof scheduleSave === "function") {
                const title = document.getElementById('noteTitle');
                const content = document.getElementById('noteContent');

                if (title) title.addEventListener("input", scheduleSave);
                if (content) content.addEventListener("input", scheduleSave);
            }
        }

        function closeEditorModal() {
            const modal = document.getElementById('editorModal');
            const content = document.getElementById('editorModalContent');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            content.innerHTML = "";
        }
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

            document.querySelectorAll('.filter-chip').forEach(btn => {
                const isMatch = btn.getAttribute('data-id') === labelId;

                btn.className = isMatch
                    ? "filter-chip px-4 py-2 rounded-full border border-gray-900 bg-gray-900 text-white text-sm font-medium shadow-sm transition"
                    : "filter-chip px-4 py-2 rounded-full border border-gray-200 bg-white text-gray-600 text-sm hover:bg-gray-100 hover:border-gray-300 transition shadow-sm";
            });
        }

        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            document.querySelectorAll('.note-card').forEach(card => {
                const titleEl = card.querySelector('h2');
                const contentEl = card.querySelector('p');

                if (!titleEl.dataset.original) titleEl.dataset.original = titleEl.innerText;
                if (!contentEl.dataset.original) contentEl.dataset.original = contentEl.innerText;

                const titleText = titleEl.dataset.original;
                const contentText = contentEl.dataset.original;

                if (!query) {
                    titleEl.innerHTML = titleText;
                    contentEl.innerHTML = contentText;
                    card.style.display = "";
                    return;
                }

                const regex = new RegExp(`(${query})`, 'gi');

                titleEl.innerHTML = titleText.replace(regex, `<mark class="bg-yellow-200 text-gray-900 px-1 rounded">$1</mark>`);
                contentEl.innerHTML = contentText.replace(regex, `<mark class="bg-yellow-200 text-gray-900 px-1 rounded">$1</mark>`);

                const isMatch = titleText.toLowerCase().includes(query) || contentText.toLowerCase().includes(query);
                card.style.display = isMatch ? "" : "none";
            });
        });

        function togglePin(id) {
            fetch(`/notes/${id}/pin`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
            }).then(() => location.reload());
        }

        let currentView = localStorage.getItem('view') || 'grid';

        function applyView(mode) {
            if (mode === 'list') {
                notesContainer.className = "flex flex-col gap-4 max-w-3xl mx-auto";
            } else {
                notesContainer.className = "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5";
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

        updateTimes();
        setInterval(updateTimes, 10000);

        let currentNoteId = null;

        function openShareModal(noteId) {
            currentNoteId = noteId;

            document.getElementById('shareModal').classList.remove('hidden');
            document.getElementById('shareModal').classList.add('flex');

            loadShares();
        }

        function closeShareModal() {
            document.getElementById('shareModal').classList.add('hidden');
        }

        function shareNote() {
            const emails = document.getElementById('shareEmails').value;
            const permission = document.getElementById('sharePermission').value;

            fetch(`/notes/${currentNoteId}/share`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    emails: emails.split(',').map(e => e.trim()),
                    permission: permission
                })
            })
            .then(res => res.json())
            .then(data => {
                alert("Shared thành công");
                location.reload();
            })
            .catch(err => console.log(err));
        }

        function revoke(userId) {
            fetch(`/notes/${currentNoteId}/share/${userId}`, {
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
            })
            .then(() => location.reload());
        }

        function updatePermission(userId, permission) {
            fetch(`/notes/${currentNoteId}/share/${userId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ permission: permission })
            })
            .then(res => res.json())
            .then(() => loadShares());
        }

        function loadShares() {
            fetch(`/notes/${currentNoteId}/shares`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('shareList');
                container.innerHTML = "";

                data.forEach(share => {
                    container.innerHTML += `
                        <div class="flex justify-between items-center border border-gray-200 bg-gray-50 p-3 rounded-xl">
                            <div>
                                <div class="text-sm text-gray-900">${share.recipient.email}</div>
                                <div class="text-xs text-gray-400">
                                    ${share.permission} • ${share.created_at}
                                </div>
                            </div>

                            <div class="flex gap-2 items-center">
                                <select class="bg-white text-gray-900 border border-gray-200 rounded-lg px-2 py-1 text-xs"
                                    onchange="updatePermission(${share.recipient_id}, this.value)">
                                    <option value="read" ${share.permission === 'read' ? 'selected' : ''}>Read</option>
                                    <option value="edit" ${share.permission === 'edit' ? 'selected' : ''}>Edit</option>
                                </select>

                                <button onclick="revoke(${share.recipient_id})" class="text-red-400 hover:text-red-500">
                                    ✕
                                </button>
                            </div>
                        </div>
                    `;
                });
            });
        }

        function openSettingsModal() {
            document.getElementById('settingsModal').classList.remove('hidden');
            document.getElementById('settingsModal').classList.add('flex');
        }

        function closeSettingsModal() {
            document.getElementById('settingsModal').classList.add('hidden');
        }

        function saveSettings() {
            localStorage.setItem('noteFontSize', document.getElementById('settingFontSize').value);
            localStorage.setItem('noteColor', document.getElementById('settingNoteColor').value);
            localStorage.setItem('theme', document.getElementById('settingTheme').value);

            applySettings();
            closeSettingsModal();
        }

        function applySettings() {
            const fontSize = localStorage.getItem('noteFontSize') || 'text-sm';

            document.querySelectorAll('.note-card').forEach(card => {
                const content = card.querySelector('p');
                if (content) {
                    content.classList.remove('text-sm', 'text-base', 'text-lg');
                    content.classList.add(fontSize);
                }
            });
        }

        applySettings();

        function setSidebarState(state) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const icon = document.getElementById('sidebarToggleIcon');
            const texts = document.querySelectorAll('.sidebar-text');

            const isCollapsed = state === 'collapsed';

            sidebar.classList.toggle('w-72', !isCollapsed);
            sidebar.classList.toggle('w-20', isCollapsed);

            mainContent.classList.toggle('lg:ml-72', !isCollapsed);
            mainContent.classList.toggle('lg:ml-20', isCollapsed);

            texts.forEach(el => {
                el.classList.toggle('hidden', isCollapsed);
            });

            icon.classList.toggle('fa-chevron-left', !isCollapsed);
            icon.classList.toggle('fa-chevron-right', isCollapsed);

            localStorage.setItem('sidebar', state);
        }

        function toggleSidebar() {
            const currentState = localStorage.getItem('sidebar') || 'expanded';
            const nextState = currentState === 'expanded' ? 'collapsed' : 'expanded';

            setSidebarState(nextState);
        }

        function applySidebarState() {
            const savedState = localStorage.getItem('sidebar') || 'expanded';
            setSidebarState(savedState);
        }

        applySidebarState();

        async function loadPage(event, url) {
            event.preventDefault();

            const mainContent = document.getElementById('mainContent');

            mainContent.innerHTML = `
                <div class="flex items-center justify-center min-h-[400px] text-gray-400">
                    Loading...
                </div>
            `;

            try {
                const response = await fetch(url, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                });

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('#mainContent');

                if (newContent) {
                    mainContent.innerHTML = newContent.innerHTML;
                } else {
                    mainContent.innerHTML = doc.body.innerHTML;
                }

                window.history.pushState({}, '', url);

            } catch (error) {
                mainContent.innerHTML = `<div class="text-red-500">Không thể tải nội dung. Vui lòng thử lại.</div>`;
                console.error(error);
            }
        }

        window.addEventListener('popstate', async function () {
            const url = window.location.href;
            const mainContent = document.getElementById('mainContent');

            const response = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#mainContent');

            mainContent.innerHTML = newContent ? newContent.innerHTML : doc.body.innerHTML;
        });
    </script>
</x-app-layout>