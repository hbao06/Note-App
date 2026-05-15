<x-app-layout>
     @php
        $user = auth()->user();

        // Màu avatar mặc định: màu đậm, không có trắng
        $avatarColors = [
            '#334155',
            '#374151',
            '#3f3f46',
            '#44403c',
            '#dc2626',
            '#ea580c',
            '#d97706',
            '#65a30d',
            '#16a34a',
            '#059669',
            '#0d9488',
            '#0891b2',
            '#0284c7',
            '#2563eb',
            '#4f46e5',
            '#7c3aed',
            '#9333ea',
            '#c026d3',
            '#db2777',
            '#e11d48',
        ];

        $avatarColor = $avatarColors[abs(crc32($user->email)) % count($avatarColors)];
        $initial = strtoupper(mb_substr($user->name ?? 'U', 0, 1, 'UTF-8'));

        $hasAvatar = $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar);
    @endphp
    <div class="h-screen overflow-hidden bg-gray-50 text-gray-900">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed left-0 top-0 z-40 h-screen w-64 
                bg-white border-r border-gray-200 
                transition-all duration-300 ease-in-out 
                flex flex-col overflow-hidden">
            <!-- USER -->
            <div id="sidebarUser" class="p-4 border-b border-gray-200">
                <a href="{{ route('profile.edit') }}"
                    id="userInfo"
                    class="sidebar-avatar-link flex items-center gap-3 min-w-0 rounded-xl hover:bg-gray-100 transition">

                    <div id="userAvatar"
                        class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md"
                        style="background-color: {{ $hasAvatar ? '#e5e7eb' : $avatarColor }};">

                        @if ($hasAvatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                alt="Avatar"
                                class="w-full h-full object-cover">
                        @else
                            <span class="select-none">{{ $initial }}</span>
                        @endif
                    </div>

                    <div class="sidebar-text min-w-0">
                        <div class="font-semibold text-gray-900 truncate">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-xs text-gray-400 truncate">
                            {{ auth()->user()->email }}
                        </div>
                    </div>
                </a>
            </div>

            <!-- MENU -->
            <nav class="p-3 space-y-2">

                <a href="{{ route('notes.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl transition text-gray-600 hover:bg-gray-100"
                data-route="notes">
                    <i class="fa-solid fa-note-sticky w-5"></i>
                    <span class="sidebar-text">My Notes</span>
                </a>

                <a href="{{ route('notes.shared') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl transition bg-gray-100 text-gray-900 font-medium"
                    data-route="shared">

                    <i class="fa-solid fa-user-group w-5"></i>

                    <span class="sidebar-text flex-1">Shared with me</span>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="sidebar-text min-w-[22px] h-[22px] px-2 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>

                <button onclick="openSettingsModal()"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-gear w-5"></i>
                    <span class="sidebar-text">Settings</span>
                </button>

            </nav>

            <!-- RECENT NOTES -->
            <div class="px-4 mt-4 sidebar-text flex-1 min-h-0 flex flex-col">
                <h3 class="text-xs uppercase tracking-widest text-gray-400 mb-3 flex-shrink-0">
                    Gần đây
                </h3>

                <div class="space-y-2 overflow-y-auto pr-1 flex-1 min-h-0">
                    @foreach($notes->take(20) as $recent)
                        <a href="{{ url('/notes/editor/' . $recent->id) }}"
                            onclick="event.preventDefault(); openEditorModal(this.href)"
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

            <!-- LOGOUT -->
            <div class="mt-auto px-3 pb-4 w-full">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="sidebar-link w-full flex items-center gap-3 px-3 py-3 rounded-xl text-red-500 hover:bg-red-50 transition">
                        <i class="fa-solid fa-right-from-bracket w-5 flex-shrink-0"></i>
                        <span class="sidebar-text">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </aside>

        <button onclick="toggleSidebar()"
            id="sidebarToggleBtn"
            class="fixed top-8 z-50 
                w-9 h-9 flex items-center justify-center
                rounded-full bg-white/90 backdrop-blur
                border border-gray-200 shadow-sm
                text-gray-600 hover:scale-105 hover:shadow-md
                transition-all duration-200">

            <i id="sidebarToggleIcon" class="fa-solid fa-chevron-left text-xs"></i>
        </button>

        <div class="pointer-events-none fixed inset-0 bg-white"></div>


        <div id="mainContent" class="relative h-screen overflow-hidden transition-all duration-300 ml-64 bg-gray-50">

            <div class="h-full flex flex-col overflow-hidden max-w-7xl mx-auto px-6 py-6">

                <!-- FIXED HEADER + STATS -->
                <div class="shrink-0">
                    <!-- HEADER -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-gray-900 leading-tight">
                                Shared with me
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                Quản lý những ghi chú người khác đã chia sẻ với bạn.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button onclick="toggleSharedView()" id="sharedViewToggleBtn"
                                class="w-10 h-10 rounded-2xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 transition shadow-sm">
                                <i class="fa-solid fa-list"></i>
                            </button>

                            <a href="{{ route('notes.index') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-black text-white font-semibold hover:bg-gray-800 active:scale-95 transition shadow-sm">
                                <i class="fa-solid fa-arrow-left"></i>
                                My Notes
                            </a>
                        </div>
                    </div>

                    @if($shared->count() > 0)
                        <!-- STATS -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white border border-gray-200 rounded-3xl p-5 shadow-sm">
                                <p class="text-sm text-gray-500">Total shared</p>
                                <h3 class="text-3xl font-bold text-gray-950 mt-2">
                                    {{ $shared->count() }}
                                </h3>
                            </div>

                            <div class="bg-white border border-gray-200 rounded-3xl p-5 shadow-sm">
                                <p class="text-sm text-gray-500">Editable</p>
                                <h3 class="text-3xl font-bold text-gray-950 mt-2">
                                    {{ $shared->where('permission', 'edit')->count() }}
                                </h3>
                            </div>

                            <div class="bg-white border border-gray-200 rounded-3xl p-5 shadow-sm">
                                <p class="text-sm text-gray-500">Read only</p>
                                <h3 class="text-3xl font-bold text-gray-950 mt-2">
                                    {{ $shared->where('permission', 'read')->count() }}
                                </h3>
                            </div>
                        </div>
                    @endif
                </div>

                @php
                    $shareNotifications = auth()->user()->unreadNotifications
                        ->where('type', 'App\Notifications\NoteSharedNotification');
                @endphp

                @php
                    $shareNotifications = $shareNotifications ?? auth()->user()->unreadNotifications
                        ->where('type', 'App\Notifications\NoteSharedNotification');
                @endphp

                <div id="sharedNotificationBox" class="mb-5 space-y-3 {{ $shareNotifications->count() > 0 ? '' : 'hidden' }}">
                    @foreach($shareNotifications as $notification)
                        <a href="{{ $notification->data['url'] ?? route('notes.shared') }}"
                            class="block rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-blue-900 hover:bg-blue-100 transition">

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center">
                                    <i class="fa-solid fa-bell"></i>
                                </div>

                                <div>
                                    <div class="font-bold">
                                        New shared note
                                    </div>

                                    <div class="text-sm mt-1">
                                        {{ $notification->data['message'] ?? 'Một ghi chú mới đã được chia sẻ với bạn.' }}
                                    </div>

                                    @if(!empty($notification->data['note_title']))
                                        <div class="text-xs mt-1 text-blue-700/70">
                                            {{ $notification->data['note_title'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- SCROLL AREA -->
                <div class="flex-1 min-h-0 overflow-y-auto pr-2 pb-6">
                    @if($shared->count() > 0)

                        <div id="sharedNotesContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach($shared as $item)
                                @if($item->note)
                                    <div onclick="openEditorModal('{{ url('/notes/editor/' . $item->note->id) }}')"
                                        class="shared-note-card group relative overflow-hidden rounded-[1.75rem] bg-white border border-gray-200 p-6 min-h-[280px] cursor-pointer shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-gray-300 transition-all duration-300">

                                        <div class="flex items-start justify-between gap-4 mb-6">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-12 h-12 rounded-2xl bg-black text-white flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-user-group"></i>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="text-xs text-gray-400">Shared by</p>
                                                    <p class="text-sm font-semibold text-gray-700 truncate max-w-[180px]">
                                                        {{ $item->owner->email }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                @if($item->note->is_pinned)
                                                    <span title="Pinned note"
                                                        class="w-9 h-9 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center rotate-45">
                                                        <i class="fa-solid fa-thumbtack"></i>
                                                    </span>
                                                @endif

                                                @if($item->note->note_password)
                                                    <span title="Locked note"
                                                        class="w-9 h-9 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center">
                                                        <i class="fa-solid fa-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pb-16">
                                            <h3 class="text-2xl font-bold text-gray-950 line-clamp-2">
                                                {{ $item->note->title ?: 'Untitled' }}
                                            </h3>

                                            <p class="mt-4 text-sm leading-6 text-gray-600 line-clamp-4">
                                                {{ $item->note->content }}
                                            </p>
                                        </div>

                                        <div class="absolute left-6 right-6 bottom-5 flex items-center justify-between gap-3">
                                            @if($item->permission === 'edit')
                                                <span class="inline-flex items-center gap-2 rounded-full bg-black px-4 py-2 text-xs font-semibold text-white whitespace-nowrap">
                                                    <i class="fa-solid fa-pen"></i>
                                                    Can edit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 whitespace-nowrap">
                                                    <i class="fa-solid fa-eye"></i>
                                                    Read only
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    @else
                        <div class="bg-white border border-dashed border-gray-300 rounded-[2rem] p-12 text-center shadow-sm">
                            <div class="mx-auto mb-6 w-20 h-20 rounded-[1.5rem] bg-gray-100 flex items-center justify-center text-gray-700">
                                <i class="fa-solid fa-user-group text-3xl"></i>
                            </div>

                            <h2 class="text-3xl font-bold text-gray-950">
                                Chưa có ghi chú nào được chia sẻ
                            </h2>

                            <p class="mt-3 text-gray-500 max-w-md mx-auto">
                                Khi ai đó chia sẻ ghi chú với bạn, chúng sẽ xuất hiện tại đây.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('components.email-verify-warning')

    <script>
        let sharedCurrentView = localStorage.getItem('sharedView') || 'grid';

        let pendingLockedNoteUrl = null;

        async function openEditorModal(url) {
            const modal = document.getElementById('editorModal');
            const content = document.getElementById('editorModalContent');

            if (!modal || !content) {
                window.location.href = url;
                return;
            }

            const res = await fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            if (res.status === 423) {
                pendingLockedNoteUrl = url;

                document.getElementById('modalPassword').value = '';
                document.getElementById('passwordModal').classList.remove('hidden');
                document.getElementById('modalPassword').focus();

                return;
            }

            if (!res.ok) {
                alert("Không thể mở note");
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            content.innerHTML = `
                <div class="p-10 text-center text-gray-400">
                    Loading editor...
                </div>
            `;

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

        function closeEditorModal() {
            const modal = document.getElementById('editorModal');
            const content = document.getElementById('editorModalContent');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            content.innerHTML = "";
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('modalPassword').value = '';
            pendingLockedNoteUrl = null;
        }

        function submitPassword() {
            if (!pendingLockedNoteUrl) return;

            const noteId = pendingLockedNoteUrl.split('/').pop();
            const password = document.getElementById('modalPassword').value;

            fetch(`/notes/${noteId}/verify-password`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ password })
            })
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(() => {
                const url = pendingLockedNoteUrl;

                document.getElementById('passwordModal').classList.add('hidden');
                document.getElementById('modalPassword').value = '';
                pendingLockedNoteUrl = null;

                openEditorModal(url);
            })
            .catch(() => {
                alert("Sai mật khẩu!");
            });
        }

        function applySharedView(mode) {
            const container = document.getElementById('sharedNotesContainer');
            const btn = document.getElementById('sharedViewToggleBtn');

            if (!container || !btn) return;

            const cards = document.querySelectorAll('.shared-note-card');

            if (mode === 'list') {
                container.className = "flex flex-col gap-4";

                cards.forEach(card => {
                    card.classList.remove('min-h-[280px]');
                    card.classList.add('min-h-[210px]');
                });

                btn.innerHTML = '<i class="fa-solid fa-table-cells"></i>';
            } else {
                container.className = "grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5";

                cards.forEach(card => {
                    card.classList.remove('min-h-[210px]');
                    card.classList.add('min-h-[280px]');
                });

                btn.innerHTML = '<i class="fa-solid fa-list"></i>';
            }
        }

        function toggleSharedView() {
            sharedCurrentView = sharedCurrentView === 'grid' ? 'list' : 'grid';
            localStorage.setItem('sharedView', sharedCurrentView);
            applySharedView(sharedCurrentView);
        }

        applySharedView(sharedCurrentView);

        function setSidebarState(state) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const icon = document.getElementById('sidebarToggleIcon');

            const userInfo = document.getElementById('userInfo');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const texts = document.querySelectorAll('.sidebar-text');
            const avatar = document.getElementById('userAvatar');

            if (!sidebar || !mainContent || !icon || !userInfo || !toggleBtn || !avatar) return;

            const isCollapsed = state === 'collapsed';

            if (isCollapsed) {
                toggleBtn.style.left = 'calc(5rem - 18px)';
                userInfo.classList.add('justify-center', 'mx-auto', 'w-12', 'h-12');
                userInfo.classList.remove('gap-3');

                avatar.classList.remove('w-9', 'h-9');
                avatar.classList.add('w-10', 'h-10');
            } else {
                toggleBtn.style.left = 'calc(16rem - 18px)';

                userInfo.classList.remove('justify-center', 'mx-auto', 'w-12', 'h-12');
                userInfo.classList.add('gap-3');

                avatar.classList.remove('w-8', 'h-8');
                avatar.classList.add('w-11', 'h-11');
            }

            sidebar.classList.toggle('w-64', !isCollapsed);
            sidebar.classList.toggle('w-20', isCollapsed);

            mainContent.classList.toggle('ml-64', !isCollapsed);
            mainContent.classList.toggle('ml-20', isCollapsed);

            texts.forEach(el => {
                el.classList.toggle('hidden', isCollapsed);
            });

            icon.classList.toggle('fa-chevron-left', !isCollapsed);
            icon.classList.toggle('fa-chevron-right', isCollapsed);

            localStorage.setItem('sidebar', state);

            const menuItems = document.querySelectorAll('.sidebar-link');

            menuItems.forEach(item => {
                if (isCollapsed) {
                    item.classList.remove('gap-3', 'px-3');
                    item.classList.add('justify-center', 'px-0', 'mx-auto', 'w-12', 'h-12');
                } else {
                    item.classList.remove('justify-center', 'px-0', 'mx-auto', 'w-12', 'h-12');
                    item.classList.add('gap-3', 'px-3');
                }
            });
        }

        function toggleSidebar() {
            const currentState = localStorage.getItem('sidebar') || 'expanded';
            const nextState = currentState === 'expanded' ? 'collapsed' : 'expanded';

            setSidebarState(nextState);
        }

        setSidebarState(localStorage.getItem('sidebar') || 'expanded');

        window.refreshSharedNotes = async function () {
            console.log("Refreshing shared notes...");

            const res = await fetch("/notes/shared", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, "text/html");

            const newContainer = doc.querySelector("#sharedNotesContainer");
            const currentContainer = document.getElementById("sharedNotesContainer");

            if (newContainer && currentContainer) {
                currentContainer.innerHTML = newContainer.innerHTML;
            } else {
                window.location.reload();
                return;
            }

            if (typeof applySharedView === "function") {
                applySharedView(localStorage.getItem("sharedView") || "grid");
            }

            if (typeof applySettings === "function") {
                applySettings();
            }
        };

        function showSharedNotification(event) {
            const box = document.getElementById('sharedNotificationBox');

            if (!box) {
                console.warn("sharedNotificationBox not found");
                return;
            }

            const notification = event.notification || {};
            const message = notification.message || 'Một ghi chú mới đã được chia sẻ với bạn.';
            const url = notification.url || '/notes/shared';
            const title = notification.note_title || event.title || 'Untitled';

            box.classList.remove('hidden');

            box.insertAdjacentHTML('afterbegin', `
                <a href="${url}"
                    class="block rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-blue-900 hover:bg-blue-100 transition">

                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center">
                            <i class="fa-solid fa-bell"></i>
                        </div>

                        <div>
                            <div class="font-bold">
                                New shared note
                            </div>

                            <div class="text-sm mt-1">
                                ${message}
                            </div>

                            <div class="text-xs mt-1 text-blue-700/70">
                                ${title}
                            </div>
                        </div>
                    </div>
                </a>
            `);
        }

        function bootSharedNotesRealtime() {
            const currentUserId = "{{ auth()->id() }}";

            console.log("Realtime boot started for user:", currentUserId);

            if (!window.Echo || !currentUserId) {
                console.warn("Echo is not ready");
                return;
            }

            if (window.__sharedNotesRealtimeBooted) {
                console.log("Realtime already booted");
                return;
            }

            window.__sharedNotesRealtimeBooted = true;

            window.Echo.private(`user.${currentUserId}`)
                .listen('.note.shared', async function (event) {
                    console.log("Realtime note shared:", event);

                    showSharedNotification(event);

                    if (window.location.pathname.includes('/notes/shared')) {
                        await window.refreshSharedNotes();
                    }
                });
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", bootSharedNotesRealtime);
        } else {
            bootSharedNotesRealtime();
        }
    </script>

    <!-- EDITOR MODAL -->
    <div id="editorModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 px-4">
        <div id="editorModalContent"
            class="w-full max-w-3xl h-[620px] overflow-hidden rounded-3xl bg-white shadow-2xl">
        </div>
    </div>

    <!-- PASSWORD MODAL -->
    <div id="passwordModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6">
            <h2 class="text-xl font-bold mb-4">🔒 Note bị khóa</h2>

            <input
                type="password"
                id="modalPassword"
                placeholder="Nhập mật khẩu"
                autocomplete="off"
                data-lpignore="true"
                data-form-type="other"
                class="w-full px-4 py-3 border rounded-2xl mb-4">

            <div class="flex gap-3">
                <button onclick="closePasswordModal()"
                    class="flex-1 px-4 py-3 rounded-2xl bg-gray-100">
                    Hủy
                </button>

                <button onclick="submitPassword()"
                    class="flex-1 px-4 py-3 rounded-2xl bg-black text-white">
                    Mở khóa
                </button>
            </div>
        </div>
    </div>

    
</x-app-layout>