<x-app-layout>
    <div class="min-h-screen bg-[#071F18] text-white">
        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed left-0 top-0 z-40 h-screen w-72 bg-[#0F3D2E]/95 border-r border-emerald-300/10 backdrop-blur-xl transition-all duration-300 overflow-hidden">

            <!-- USER -->
            <div class="p-4 border-b border-emerald-300/10 flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-[#166534] to-[#22C55E] flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>

                <div class="sidebar-text">
                    <div class="font-semibold text-white truncate">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-xs text-[#A7F3D0]/60 truncate">
                        {{ auth()->user()->email }}
                    </div>
                </div>
            </div>

            <!-- MENU -->
            <nav class="p-3 space-y-2">
                <a href="{{ route('notes.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl bg-[#166534] text-white">
                    <i class="fa-solid fa-note-sticky w-5"></i>
                    <span class="sidebar-text">My Notes</span>
                </a>

                <a href="{{ route('notes.shared') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl text-[#A7F3D0] hover:bg-[#14532D] transition">
                    <i class="fa-solid fa-user-group w-5"></i>
                    <span class="sidebar-text">Shared with me</span>
                </a>

                <button onclick="openSettingsModal()"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-[#A7F3D0] hover:bg-[#14532D] transition">
                    <i class="fa-solid fa-gear w-5"></i>
                    <span class="sidebar-text">Settings</span>
                </button>

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-xl text-[#A7F3D0] hover:bg-[#14532D] transition">
                    <i class="fa-solid fa-user w-5"></i>
                    <span class="sidebar-text">Profile</span>
                </a>
            </nav>

            <!-- RECENT NOTES -->
            <div class="px-4 mt-4 sidebar-text">
                <h3 class="text-xs uppercase tracking-widest text-[#A7F3D0]/50 mb-3">
                    Gần đây
                </h3>

                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                    @foreach($notes->take(6) as $recent)
                        <a href="{{ url('/notes/editor/' . $recent->id) }}"
                            class="block px-3 py-2 rounded-xl hover:bg-[#14532D] transition">
                            <div class="text-sm text-white truncate">
                                {{ $recent->title ?: 'Untitled' }}
                            </div>
                            <div class="text-xs text-[#A7F3D0]/50 truncate">
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
                        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-red-200 hover:bg-red-400/10 transition">
                        <i class="fa-solid fa-right-from-bracket w-5"></i>
                        <span class="sidebar-text">Logout</span>
                    </button>
                </form>

                <button onclick="toggleSidebar()"
                    class="w-full flex items-center justify-center px-3 py-3 rounded-xl bg-[#14532D] text-[#A7F3D0] hover:bg-[#166534] transition">
                    <i id="sidebarToggleIcon" class="fa-solid fa-chevron-left"></i>
                </button>
            </div>
        </aside>
        <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(34,197,94,0.18),transparent_30%),radial-gradient(circle_at_90%_0%,rgba(74,222,128,0.12),transparent_28%),linear-gradient(135deg,#071F18_0%,#0F3D2E_55%,#071F18_100%)]"></div>

        <div id="mainContent" class="relative py-8 max-w-7xl mx-auto px-4 transition-all duration-300 lg:ml-72">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
                <div>
                    <p class="text-sm text-[#A7F3D0]/70 mb-1">Good Morning ✨</p>
                    <h1 class="text-4xl font-bold tracking-tight text-white">Your Notes!</h1>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="toggleView()" id="viewToggleBtn"
                        class="p-3 bg-[#0F3D2E]/80 border border-emerald-300/10 text-[#A7F3D0] rounded-xl hover:bg-[#14532D] transition shadow-lg">
                        🔳
                    </button>

                    <a href="{{ route('notes.editor') }}"
                        class="px-5 py-3 bg-gradient-to-r from-[#166534] to-[#22C55E] text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/20 hover:brightness-110 active:scale-95 transition">
                        + Create Note
                    </a>

                </div>
            </div>

            <!-- SEARCH -->
            <div class="relative mb-8">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-[#A7F3D0]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>

                <input type="text" id="searchInput" placeholder="Search notes..."
                    class="w-full pl-12 pr-4 py-4 border border-emerald-300/10 bg-[#0F3D2E]/80 rounded-2xl text-white placeholder-[#A7F3D0]/45 focus:ring-2 focus:ring-[#22C55E]/70 focus:border-[#22C55E] outline-none transition shadow-xl" />
            </div>

            <!-- FILTER LABELS -->
            <div class="mb-8 overflow-x-auto">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs font-bold text-[#A7F3D0]/60 uppercase mr-2 tracking-widest">
                        Filters Label:
                    </span>

                    <button onclick="filterByLabel('all')"
                        data-id="all"
                        class="filter-chip active px-4 py-2 rounded-full border border-[#22C55E]/40 bg-gradient-to-r from-[#166534] to-[#22C55E] text-white text-sm font-medium shadow-sm transition">
                        All
                    </button>

                    @foreach($allLabels as $label)
                        <button onclick="filterByLabel('{{ $label->id }}')"
                            data-id="{{ $label->id }}"
                            class="filter-chip px-4 py-2 rounded-full border border-emerald-300/10 bg-[#0F3D2E]/80 text-[#A7F3D0] text-sm hover:bg-[#14532D] hover:border-[#22C55E]/50 transition shadow-sm">
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
                        onclick="window.location.href=this.dataset.url"
                        data-labels="{{ $uniqueLabels->pluck('id')->join(',') }}"
                        class="note-card group relative overflow-hidden bg-gradient-to-br from-[#166534] to-[#0F3D2E] border border-emerald-300/10 rounded-[1.75rem] p-5 shadow-xl shadow-black/10 hover:shadow-emerald-500/20 hover:-translate-y-1 hover:border-[#22C55E]/40 transition-all duration-300 cursor-pointer flex flex-col min-h-[250px]">

                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(74,222,128,0.22),transparent_35%),linear-gradient(180deg,rgba(255,255,255,0.08),transparent)]"></div>

                        <!-- PIN -->
                        <button onclick="event.stopPropagation(); togglePin('{{ $note->id }}')"
                            class="absolute top-4 right-4 z-10 p-2 rounded-xl bg-white/10 hover:bg-white/20 opacity-0 group-hover:opacity-100 transition">
                            <i class="fa-solid fa-thumbtack {{ $note->is_pinned ? 'text-[#4ADE80] rotate-45' : 'text-[#A7F3D0]/70' }}"></i>
                        </button>

                        <!-- CONTENT -->
                        <div class="relative z-10 mb-4 pr-8">
                            <div class="flex items-center gap-2 mb-3 text-sm">
                                @if($note->sharedNotes->count() > 0)
                                    <span title="This note is shared" class="text-[#4ADE80]">
                                        <i class="fa-solid fa-user-group"></i>
                                    </span>
                                @endif

                                @if($note->note_password)
                                    <span title="This note is locked" class="text-yellow-300">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                @endif

                                @if($note->is_pinned)
                                    <span title="Pinned note" class="text-[#A7F3D0]">
                                        <i class="fa-solid fa-thumbtack"></i>
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-2xl font-semibold text-white mb-3 line-clamp-2 group-hover:text-[#4ADE80] transition">
                                {{ $note->title ?: 'Untitled' }}
                            </h2>

                            <p class="text-[#D1FAE5]/75 text-sm leading-6 line-clamp-6">
                                {{ $note->content }}
                            </p>

                            <div class="mt-4 relative group/time inline-flex items-center gap-1 text-[11px] text-[#A7F3D0]/60 hover:text-[#A7F3D0] transition cursor-default">
                                <i class="fa-regular fa-clock"></i>
                                <span class="note-time" data-time="{{ $note->updated_at->format('c') }}"></span>

                                <div class="absolute bottom-full mb-2 hidden group-hover/time:block bg-[#071F18] text-white text-xs px-2 py-1 rounded-md shadow-lg whitespace-nowrap z-50 border border-emerald-300/10">
                                    <span class="note-full-time" data-time="{{ $note->updated_at->format('c') }}"></span>
                                </div>
                            </div>
                        </div>

                        <!-- LABELS -->
                        <div class="relative z-10 flex flex-wrap gap-2 mt-auto pb-10">
                            @foreach($uniqueLabels as $label)
                                <span class="px-3 py-1 bg-white/10 text-[#A7F3D0] text-[12px] font-medium rounded-full border border-white/10 hover:bg-white/15 transition">
                                    {{ $label->name }}
                                </span>
                            @endforeach
                        </div>

                        <!-- ACTIONS -->
                        <div class="absolute bottom-4 right-4 z-20 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">

                            <button onclick="event.stopPropagation(); openShareModal({{ $note->id }})"
                                title="Share note"
                                class="p-2 rounded-xl bg-white/10 text-[#A7F3D0] hover:bg-[#22C55E]/20 hover:text-[#4ADE80] transition">
                                <i class="fa-solid fa-share"></i>
                            </button>

                            <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa ghi chú này không?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="event.stopPropagation()"
                                    title="Delete note"
                                    class="p-2 rounded-xl bg-white/10 text-red-200/80 hover:bg-red-400/10 hover:text-red-200 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SHARE MODAL -->
            <div id="shareModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 px-4">
                <div class="bg-[#0F3D2E] border border-emerald-300/10 w-full max-w-md p-6 rounded-2xl shadow-2xl text-white">

                    <h2 class="text-xl font-bold mb-4">Share Note</h2>

                    <input type="text" id="shareEmails"
                        placeholder="Nhập email, cách nhau dấu phẩy"
                        class="w-full border border-emerald-300/10 bg-[#071F18] text-white placeholder-[#A7F3D0]/40 px-4 py-3 rounded-xl mb-3 focus:ring-2 focus:ring-[#22C55E] outline-none">

                    <select id="sharePermission"
                        class="w-full border border-emerald-300/10 bg-[#071F18] text-white px-4 py-3 rounded-xl mb-4 focus:ring-2 focus:ring-[#22C55E] outline-none">
                        <option value="read">Read only</option>
                        <option value="edit">Can edit</option>
                    </select>

                    <button onclick="shareNote()"
                        class="w-full bg-gradient-to-r from-[#166534] to-[#22C55E] text-white py-3 rounded-xl font-semibold mb-4 hover:brightness-110 transition">
                        Share
                    </button>

                    <div id="shareList" class="space-y-2 max-h-40 overflow-y-auto"></div>

                    <button onclick="closeShareModal()" class="mt-4 text-sm text-[#A7F3D0]/70 hover:text-white">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- SETTINGS MODAL -->
        <div id="settingsModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 px-4">
            <div class="bg-[#0F3D2E] border border-emerald-300/10 p-6 rounded-2xl w-full max-w-md text-white shadow-2xl">
                <h2 class="text-xl font-bold mb-4">User Settings</h2>

                <label class="block text-sm font-medium mb-1 text-[#A7F3D0]">Font size</label>
                <select id="settingFontSize" class="w-full border border-emerald-300/10 bg-[#071F18] rounded-xl px-3 py-3 mb-4">
                    <option value="text-sm">Small</option>
                    <option value="text-base">Normal</option>
                    <option value="text-lg">Large</option>
                </select>

                <label class="block text-sm font-medium mb-1 text-[#A7F3D0]">Note color</label>
                <select id="settingNoteColor" class="w-full border border-emerald-300/10 bg-[#071F18] rounded-xl px-3 py-3 mb-4">
                    <option value="bg-[#14532D]">Dark Green</option>
                    <option value="bg-[#166534]">Emerald</option>
                    <option value="bg-[#0F3D2E]">Deep Green</option>
                    <option value="bg-white">White</option>
                </select>

                <label class="block text-sm font-medium mb-1 text-[#A7F3D0]">Theme</label>
                <select id="settingTheme" class="w-full border border-emerald-300/10 bg-[#071F18] rounded-xl px-3 py-3 mb-4">
                    <option value="dark">Dark</option>
                    <option value="light">Light</option>
                </select>

                <button onclick="saveSettings()" class="w-full bg-gradient-to-r from-[#166534] to-[#22C55E] text-white py-3 rounded-xl font-semibold">
                    Save Settings
                </button>

                <button onclick="closeSettingsModal()" class="mt-3 text-sm text-[#A7F3D0]/70 hover:text-white">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
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
                    ? "filter-chip px-4 py-2 rounded-full border border-[#22C55E]/40 bg-gradient-to-r from-[#166534] to-[#22C55E] text-white text-sm font-medium shadow-sm transition"
                    : "filter-chip px-4 py-2 rounded-full border border-emerald-300/10 bg-[#0F3D2E]/80 text-[#A7F3D0] text-sm hover:bg-[#14532D] hover:border-[#22C55E]/50 transition shadow-sm";
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

                titleEl.innerHTML = titleText.replace(regex, `<mark class="bg-[#22C55E]/30 text-white px-1 rounded">$1</mark>`);
                contentEl.innerHTML = contentText.replace(regex, `<mark class="bg-[#22C55E]/30 text-white px-1 rounded">$1</mark>`);

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
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
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
                body: JSON.stringify({
                    permission: permission
                })
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
                        <div class="flex justify-between items-center border border-emerald-300/10 bg-[#071F18]/70 p-3 rounded-xl">
                            <div>
                                <div class="text-sm text-white">${share.recipient.email}</div>
                                <div class="text-xs text-[#A7F3D0]/60">
                                    ${share.permission} • ${share.created_at}
                                </div>
                            </div>

                            <div class="flex gap-2 items-center">
                                <select class="bg-[#0F3D2E] text-white border border-emerald-300/10 rounded-lg px-2 py-1 text-xs"
                                    onchange="updatePermission(${share.recipient_id}, this.value)">
                                    <option value="read" ${share.permission === 'read' ? 'selected' : ''}>Read</option>
                                    <option value="edit" ${share.permission === 'edit' ? 'selected' : ''}>Edit</option>
                                </select>

                                <button onclick="revoke(${share.recipient_id})" class="text-red-300 hover:text-red-200">
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
            const noteColor = localStorage.getItem('noteColor') || 'bg-[#14532D]';
            const theme = localStorage.getItem('theme') || 'dark';

            document.querySelectorAll('.note-card').forEach(card => {
                const content = card.querySelector('p');
                if (content) {
                    content.classList.remove('text-sm', 'text-base', 'text-lg');
                    content.classList.add(fontSize);
                }
            });

            if (theme === 'dark') {
                document.body.classList.add('bg-[#071F18]', 'text-white');
            } else {
                document.body.classList.remove('bg-[#071F18]', 'text-white');
            }
        }

        applySettings();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const icon = document.getElementById('sidebarToggleIcon');
            const texts = document.querySelectorAll('.sidebar-text');

            const collapsed = sidebar.classList.contains('w-72');

            if (collapsed) {
                sidebar.classList.remove('w-72');
                sidebar.classList.add('w-20');

                mainContent.classList.remove('lg:ml-72');
                mainContent.classList.add('lg:ml-20');

                texts.forEach(el => el.classList.add('hidden'));

                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');

                localStorage.setItem('sidebar', 'collapsed');
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-72');

                mainContent.classList.remove('lg:ml-20');
                mainContent.classList.add('lg:ml-72');

                texts.forEach(el => el.classList.remove('hidden'));

                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');

                localStorage.setItem('sidebar', 'expanded');
            }
        }

        function applySidebarState() {
            if (localStorage.getItem('sidebar') === 'collapsed') {
                toggleSidebar();
            }
        }

        applySidebarState();
    </script>
</x-app-layout>