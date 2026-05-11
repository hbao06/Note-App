

<x-app-layout>
    <div class="h-screen overflow-hidden bg-gray-50 text-gray-900">
        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed left-0 top-0 z-40 h-screen w-64 
                bg-white border-r border-gray-200 
                transition-all duration-300 ease-in-out 
                flex flex-col overflow-hidden">
            <!-- USER -->
            <div id="sidebarUser" class="p-4 border-b border-gray-200">


                <!-- AVATAR  -->
                <a href="{{ route('profile.edit') }}" 
                onclick="loadPage(event, this.href)"
                id="userInfo"
                class="sidebar-avatar-link flex items-center gap-3 min-w-0 rounded-xl hover:bg-gray-100 transition">

                    <div id="userAvatar"
                        class="w-9 h-9 rounded-full bg-gray-900 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
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
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl transition text-gray-600 hover:bg-gray-100"
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
            <div class="sticky top-0 z-30 bg-white/70 backdrop-blur-xl px-6 pt-6 pb-5 border-b border-slate-100">
                <!-- HEADER -->
                <!-- TOP BAR -->
                <div class="flex items-start justify-between gap-4 mb-6">

                    <!-- LEFT -->
                    <div>
                        <div class="text-sm font-semibold text-slate-400 mb-2 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span id="greetingText"></span>
                        </div>

                        <h1 class="text-4xl font-black tracking-tight text-slate-950">
                            Your Notes
                        </h1>
                    </div>

                    <!-- RIGHT -->
                    <div class="flex items-center gap-3">

                        <!-- View toggle -->
                        <button onclick="toggleView()" id="viewToggleBtn"
                            class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-500 shadow-sm hover:bg-slate-50 hover:text-slate-950 transition">
                            <i class="fa-solid fa-list"></i>
                        </button>

                        <!-- Create -->
                        <button onclick="openEditorModal('{{ route('notes.editor') }}')"
                            class="h-12 px-5 rounded-2xl bg-slate-950 text-white font-bold shadow-lg shadow-slate-300/50 hover:bg-slate-800 active:scale-95 transition">
                            + Create
                        </button>
                    </div>
                </div>

                <!-- SEARCH BAR -->
                <div class="relative mb-5 group">

                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-slate-900 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input type="text"
                        id="searchInput"
                        placeholder="Search notes, ideas..."
                        class="w-full h-14 pl-12 pr-4 rounded-2xl border border-slate-200 bg-white/80 text-slate-900 placeholder:text-slate-400 shadow-sm transition outline-none
                        focus:border-slate-300 focus:ring-4 focus:ring-slate-200/60 focus:bg-white">
                </div>

                <!-- FILTER -->
                <div class="flex items-center gap-3 overflow-x-auto pb-1">

                    <!-- Label text -->
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i class="fa-solid fa-filter text-[11px]"></i>
                        Labels
                    </div>

                    <!-- Chips -->
                    <div id="filterLabelsContainer" class="flex items-center gap-2 min-w-max">

                        <button onclick="filterByLabel('all')" data-id="all"
                            class="filter-chip active px-4 py-2.5 rounded-full bg-slate-950 text-white text-sm font-bold shadow-sm">
                            All
                        </button>

                        @foreach($allLabels as $label)
                            <button onclick="filterByLabel('{{ $label->id }}')"
                                data-id="{{ $label->id }}"
                                class="filter-chip px-4 py-2.5 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 hover:text-slate-950 transition">
                                {{ $label->name }}
                            </button>
                        @endforeach

                    </div>
                </div>
            </div>

        
            <!-- NOTES GRID -->
            <div class="h-[calc(100vh-280px)] overflow-y-auto px-6 py-6">
                <div id="notesContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach ($notes as $note)
                        @php $uniqueLabels = $note->labels->unique('name'); @endphp

                        <div data-note-id="{{ $note->id }}"
                            data-url="{{ url('/notes/editor/' . $note->id) }}"
                            onclick="openEditorModal(this.dataset.url)"
                            data-labels="{{ $uniqueLabels->pluck('id')->join(',') }}"
                            class="note-card group relative overflow-hidden bg-white border border-gray-200 rounded-[1.75rem] p-5 shadow-sm hover:shadow-md hover:-translate-y-1 hover:border-gray-300 hover:bg-gray-50 transition-all duration-300 cursor-pointer flex flex-col min-h-[250px]">

                            <!-- PIN -->
                            <button type="button"   
                                onclick="event.preventDefault(); event.stopPropagation(); togglePin({{ $note->id }}, this)"
                                class="absolute top-4 right-4 z-30 w-10 h-10 p-0 flex items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm hover:bg-gray-100 opacity-0 group-hover:opacity-100 transition">
                                <i class="fa-solid fa-thumbtack {{ $note->is_pinned ? 'text-gray-900 rotate-45' : 'text-gray-400' }}"></i>
                            </button>

                            <!-- CONTENT -->
                            <div class="relative z-10 mb-4 pr-8">
                                <div class="note-status-icons flex items-center gap-2 mb-3 text-sm">
                                    @if($note->sharedNotes->count() > 0)
                                        <span title="This note is shared" class="shared-status text-gray-500">
                                            <i class="fa-solid fa-user-group"></i>
                                        </span>
                                    @endif

                                    @if($note->note_password)
                                        <span title="This note is locked" class="text-yellow-500">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                    @endif

                                    <span title="Pinned note"
                                        class="pinned-status text-gray-500 {{ $note->is_pinned ? '' : 'hidden' }}">
                                        <i class="fa-solid fa-thumbtack"></i>
                                    </span>
                                </div>

                                <h2 class="card-title text-2xl font-semibold text-gray-900 mb-3 line-clamp-2 group-hover:text-black transition">
                                    {{ $note->title ?: 'Untitled' }}
                                </h2>

                                <p class="card-content text-gray-500 text-sm leading-6 line-clamp-6">
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
                            <div class="absolute bottom-4 right-4 z-30 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition"
                                onclick="event.stopPropagation()">

                                <button type="button"
                                    onclick="event.preventDefault(); event.stopPropagation(); openShareModal({{ $note->id }})"
                                    title="Share note"
                                    class="w-10 h-10 p-0 flex items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fa-solid fa-share"></i>
                                </button>

                                <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                    class="m-0 p-0 flex items-center"
                                    onsubmit="event.stopPropagation(); return confirm('Bạn có chắc muốn xóa ghi chú này không?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        onclick="event.stopPropagation()"
                                        title="Delete note"
                                        class="w-10 h-10 p-0 flex items-center justify-center rounded-xl bg-white border border-gray-200 shadow-sm text-red-500 hover:bg-red-50 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- SHARE MODAL -->
            <div id="shareModal"
                class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm hidden items-center justify-center z-50 px-4">

                <div class="w-full max-w-lg rounded-[2rem] bg-white border border-white shadow-2xl shadow-slate-900/20 overflow-hidden">

                    <!-- Header -->
                    <div class="px-7 py-6 border-b border-slate-100 flex items-start justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center mb-4">
                                <i class="fa-solid fa-share-nodes"></i>
                            </div>

                            <h2 class="text-2xl font-black text-slate-950">
                                Share note
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Invite people by email and manage permissions.
                            </p>
                        </div>

                        <button onclick="closeShareModal()"
                            class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-7 py-6 space-y-4">

                        <!-- Email input -->
                        <div>
                            <label class="text-sm font-bold text-slate-700">Email addresses</label>
                            <div id="emailChipBox"
                                onclick="document.getElementById('emailChipInput').focus()"
                                class="mt-2 min-h-[58px] w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-3 py-2 flex flex-wrap items-center gap-2 cursor-text transition
                                focus-within:border-slate-950 focus-within:ring-4 focus-within:ring-slate-200">

                                <div id="emailChips" class="flex flex-wrap items-center gap-2"></div>

                                <input type="text"
                                        id="emailChipInput"
                                        placeholder="Type email and press Enter"
                                        class="flex-1 min-w-[180px] h-9 border-none bg-transparent px-1 py-0 text-sm text-slate-900 placeholder:text-slate-400 outline-none ring-0 shadow-none focus:border-none focus:outline-none focus:ring-0 focus:shadow-none">
                            </div>

                            <input type="hidden" id="shareEmails">

                            <p id="emailHint" class="mt-2 text-xs text-slate-400">
                                Press Enter or comma to add multiple emails.
                            </p>

                            <div id="shareEmailErrors" class="mt-2 space-y-1 text-xs font-semibold text-red-500 hidden"></div>

                            <div id="shareSuccessMessage" class="mt-2 text-xs font-semibold text-emerald-600 hidden"></div>
                        </div>

                        <!-- Permission -->
                        <div>
                            <label class="text-sm font-bold text-slate-700">Permission</label>
                            <div class="relative mt-2">
                                <select id="sharePermission"
                                    class="w-full appearance-none rounded-2xl border-slate-200 bg-slate-50/80 px-4 py-3.5 pr-10 text-slate-900 focus:border-slate-950 focus:ring-slate-950 outline-none">
                                    <option value="read">Read only</option>
                                    <option value="edit">Can edit</option>
                                </select>

                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            </div>
                        </div>

                        <!-- Button -->
                        <button id="shareNoteBtn" onclick="shareNote()"
                            class="w-full py-4 rounded-2xl bg-slate-950 text-white font-black shadow-xl shadow-slate-300/50 hover:bg-slate-800 active:scale-[0.98] transition">
                            Share note
                        </button>

                        <!-- List -->
                        <div class="pt-2">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-black text-slate-800">
                                    People with access
                                </h3>
                            </div>

                            <div id="shareList" class="space-y-2 max-h-52 overflow-y-auto pr-1"></div>
                        </div>

                    </div>
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
        <div id="settingsModal"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">

            <div class="w-full max-w-xl rounded-[28px] bg-gray-50 shadow-2xl border border-gray-200 overflow-hidden">

                <div class="px-7 py-6 border-b border-gray-100 flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-950">Settings</h2>
                        <p class="mt-1 text-sm text-gray-500">Customize your workspace.</p>
                    </div>

                    <button onclick="closeSettingsModal()"
                        class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="px-7 py-6 space-y-7 bg-gray-50">
                    <!-- Font size -->
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-800">Font size</label>
                            <span class="text-xs text-gray-400">Note content</span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 rounded-2xl bg-gray-100 p-1">
                            <button type="button" onclick="selectSetting('settingFontSize', 'text-sm', this)"
                                class="setting-option rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-white transition">
                                Small
                            </button>

                            <button type="button" onclick="selectSetting('settingFontSize', 'text-base', this)"
                                class="setting-option rounded-xl px-4 py-2.5 text-sm font-medium bg-white text-gray-950 shadow-sm transition">
                                Normal
                            </button>

                            <button type="button" onclick="selectSetting('settingFontSize', 'text-lg', this)"
                                class="setting-option rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-white transition">
                                Large
                            </button>
                        </div>

                        <input type="hidden" id="settingFontSize" value="text-base">
                    </div>

                    <!-- Note color -->
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-800">Note color</label>
                            <span class="text-xs text-gray-400">Card background</span>
                        </div>

                        <div class="grid grid-cols-4 gap-3">
                            <button type="button" onclick="selectNoteColor('bg-white', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-gray-950 bg-white shadow-sm" title="White"></button>

                            <button onclick="selectNoteColor('bg-gray-800', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-gray-800 shadow-sm">
                            </button>

                            <button type="button" onclick="selectNoteColor('bg-yellow-50', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-yellow-50 shadow-sm" title="Yellow"></button>

                            <button type="button" onclick="selectNoteColor('bg-orange-50', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-orange-50 shadow-sm" title="Orange"></button>

                            <button type="button" onclick="selectNoteColor('bg-green-50', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-green-50 shadow-sm" title="Green"></button>

                            <button type="button" onclick="selectNoteColor('bg-blue-50', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-blue-50 shadow-sm" title="Blue"></button>

                            <button type="button" onclick="selectNoteColor('bg-purple-50', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-purple-50 shadow-sm" title="Purple"></button>

                            <button type="button" onclick="selectNoteColor('bg-rose-50', this)"
                                class="note-color-option h-14 rounded-2xl border-2 border-transparent bg-rose-50 shadow-sm" title="Rose"></button>
                        </div>

                        <input type="hidden" id="settingNoteColor" value="bg-white">
                    </div>

                    <!-- Theme -->
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-800">Theme</label>
                            <span class="text-xs text-gray-400">Workspace style</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" onclick="selectTheme('light', this)"
                                class="theme-option rounded-2xl border-2 border-gray-950 bg-white px-5 py-4 text-left transition">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa-regular fa-sun text-gray-700"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-gray-950">Light</p>
                                        <p class="text-xs text-gray-400">Clean white</p>
                                    </div>
                                </div>
                            </button>

                            <button type="button" onclick="selectTheme('dark', this)"
                                class="theme-option rounded-2xl border-2 border-transparent bg-gray-100 px-5 py-4 text-left transition">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-gray-900 flex items-center justify-center">
                                        <i class="fa-regular fa-moon text-white"></i>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-gray-950">Dark</p>
                                        <p class="text-xs text-gray-400">Soft black</p>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <input type="hidden" id="settingTheme" value="light">
                    </div>
                </div>

                <div class="px-7 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button onclick="closeSettingsModal()"
                        class="px-5 py-3 rounded-2xl bg-white border border-gray-200 text-gray-700 font-medium hover:bg-gray-100 transition">
                        Cancel
                    </button>

                    <button onclick="saveSettings()"
                        class="px-6 py-3 rounded-2xl bg-gray-950 text-white font-medium hover:bg-gray-800 transition">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    @if (!auth()->user()->hasVerifiedEmail())
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-2xl">
        
        <div class="flex justify-center items-center px-5 py-4 rounded-2xl 
            bg-yellow-50 border border-yellow-200 text-yellow-900 shadow-xl">
            
            <span class="text-center text-base">
                Tài khoản của bạn chưa được xác minh. Vui lòng kiểm tra email để hoàn tất.
            </span>

        </div>
    </div>
    @endif

    <script>
        let pendingLockedNoteUrl = null;

        async function openEditorModal(url) {
            const modal = document.getElementById('editorModal');
            const content = document.getElementById('editorModalContent');

            const res = await fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            // NOTE BỊ KHÓA
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

        function closeShareModal() {
            const modal = document.getElementById('shareModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            resetShareEmailUI();
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
                    ? "filter-chip active px-4 py-2.5 rounded-full bg-slate-950 text-white text-sm font-bold shadow-sm"
                    : "filter-chip px-4 py-2.5 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 hover:text-slate-950 transition";
            });

            refreshFilterChipTheme();
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

        async function togglePin(id, btn) {
            try {
                const res = await fetch(`/notes/${id}/pin`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                if (!res.ok) {
                    throw new Error("Pin request failed");
                }

                const data = await res.json();

                const card = btn.closest('.note-card');
                const icon = btn.querySelector('i');
                const statusIcon = card.querySelector('.pinned-status');
                const container = document.getElementById('notesContainer');

                const isPinned = data.is_pinned;

                icon.classList.toggle('rotate-45', isPinned);
                icon.classList.toggle('text-gray-900', isPinned);
                icon.classList.toggle('text-gray-400', !isPinned);

                if (statusIcon) {
                    statusIcon.classList.toggle('hidden', !isPinned);
                }

                if (isPinned) {
                    container.prepend(card);
                } else {
                    location.reload();
                }

            } catch (error) {
                console.error(error);
                alert("Không ghim được note. Kiểm tra route/controller pin.");
            }
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


        function markNoteAsShared(noteId) {
            const card = document.querySelector(`.note-card[data-note-id="${noteId}"]`);
            if (!card) return;

            const iconBox = card.querySelector('.note-status-icons');
            if (!iconBox) return;

            if (iconBox.querySelector('.shared-status')) return;

            iconBox.insertAdjacentHTML('afterbegin', `
                <span title="This note is shared" class="shared-status text-gray-500">
                    <i class="fa-solid fa-user-group"></i>
                </span>
            `);
        }

        async function shareNote() {
            addEmailChip(document.getElementById('emailChipInput').value);

            const emails = shareEmailList
                .map(email => email.trim().toLowerCase())
                .filter(email => email.length > 0);

            const permission = document.getElementById('sharePermission').value;

            // clear lỗi cũ, nhưng lát nữa sẽ set lại lỗi format
            shareEmailErrors = {};

            if (!emails.length) {
                setShareEmailError('Email', 'Vui lòng nhập ít nhất một email.');
                return;
            }

            const invalidEmails = emails.filter(email => !isValidEmail(email));

            if (invalidEmails.length) {
                invalidEmails.forEach(email => {
                    setShareEmailError(email, 'Email không hợp lệ.');
                });

                renderEmailChips();
                renderShareEmailErrors();
                return;
            }

            const btn = document.getElementById('shareNoteBtn');
            const originalText = btn.innerHTML;

            try {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sharing...`;

                const res = await fetch(`/notes/${currentNoteId}/share`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        emails,
                        permission
                    })
                });

                const data = await res.json();

                let hasError = false;

                if (data.not_found?.length) {
                    hasError = true;
                    data.not_found.forEach(email => {
                        setShareEmailError(email, 'Email này chưa có tài khoản trong hệ thống.');
                    });
                }

                if (data.unverified?.length) {
                    hasError = true;
                    data.unverified.forEach(email => {
                        setShareEmailError(email, 'Tài khoản này chưa xác thực email.');
                    });
                }

                if (data.skipped?.length) {
                    hasError = true;
                    data.skipped.forEach(email => {
                        setShareEmailError(email, 'Không thể chia sẻ cho chính bạn.');
                    });
                }

                if (hasError) {
                    renderEmailChips();
                    renderShareEmailErrors();
                    return;
                }

                if (data.shared?.length) {
                    renderShareSuccess('Đã chia sẻ thành công với ' + data.shared.join(', '));

                    markNoteAsShared(currentNoteId);

                    shareEmailList = [];
                    renderEmailChips();

                    loadShares();
                    return;
                }

                setShareEmailError('Email', 'Không có email hợp lệ để chia sẻ.');

            } catch (error) {
                console.error(error);
                setShareEmailError('System', 'Có lỗi xảy ra khi share note.');
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = originalText;

                renderEmailChips();
            }
        }

        let shareEmailList = [];
        let shareEmailErrors = {};

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function setShareEmailError(email, message) {
            shareEmailErrors[email] = message;
            renderEmailChips();
            renderShareEmailErrors();
        }

        function clearShareEmailError(email) {
            delete shareEmailErrors[email];
            renderEmailChips();
            renderShareEmailErrors();
        }

        function clearAllShareEmailErrors() {
            shareEmailErrors = {};
            renderEmailChips();
            renderShareEmailErrors();
        }

        function renderShareEmailErrors() {

            const errorBox = document.getElementById('shareEmailErrors');
            const successBox = document.getElementById('shareSuccessMessage');

            if (!errorBox) return;

            const errors = Object.entries(shareEmailErrors);

            if (!errors.length) {
                errorBox.classList.add('hidden');
                errorBox.innerHTML = '';
                return;
            }

            if (successBox) {
                successBox.classList.add('hidden');
                successBox.innerHTML = '';
            }

            errorBox.classList.remove('hidden');

            errorBox.innerHTML = errors.map(([email, message]) => `
                <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 shadow-sm">

                    <div class="mt-0.5 text-red-500">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>

                    <div class="leading-6">
                        <span class="font-bold">${email}</span>: ${message}
                    </div>

                </div>
            `).join('');
        }

        function renderShareSuccess(message) {

            const successBox = document.getElementById('shareSuccessMessage');
            const errorBox = document.getElementById('shareEmailErrors');

            if (!successBox) return;

            if (errorBox) {
                errorBox.classList.add('hidden');
                errorBox.innerHTML = '';
            }

            successBox.classList.remove('hidden');

            successBox.innerHTML = `
                <div class="flex items-start gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">

                    <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500"></i>

                    <span>${message}</span>

                </div>
            `;
        }

        function renderEmailChips() {
            const chips = document.getElementById('emailChips');
            const hiddenInput = document.getElementById('shareEmails');
            const shareBtn = document.getElementById('shareNoteBtn');

            if (!chips || !hiddenInput) return;

            chips.innerHTML = '';

            shareEmailList.forEach((email, index) => {
                const hasError = !!shareEmailErrors[email] || !isValidEmail(email);

                chips.innerHTML += `
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold border transition
                        ${hasError
                            ? 'bg-red-50 text-red-600 border-red-200'
                            : 'bg-white text-slate-700 border-slate-200 shadow-sm'}">

                        <i class="fa-solid ${hasError ? 'fa-triangle-exclamation' : 'fa-envelope'} text-xs"></i>

                        <span>${email}</span>

                        <button type="button"
                            onclick="removeEmailChip(${index})"
                            class="ml-1 w-5 h-5 rounded-full flex items-center justify-center ${hasError ? 'hover:bg-red-100' : 'hover:bg-slate-100'} transition">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </span>
                `;
            });

            hiddenInput.value = shareEmailList.join(',');

            const hasAnyInvalid = shareEmailList.some(email => !isValidEmail(email));
            const hasBackendError = Object.keys(shareEmailErrors).length > 0;

            if (shareBtn) {
                shareBtn.disabled = hasAnyInvalid || hasBackendError;
                shareBtn.classList.toggle('opacity-50', shareBtn.disabled);
                shareBtn.classList.toggle('cursor-not-allowed', shareBtn.disabled);
            }
        }

        function addEmailChip(value) {
            const raw = value.trim();

            if (!raw) return;

            const parts = raw
                .split(',')
                .map(e => e.trim().toLowerCase())
                .filter(Boolean);

            parts.forEach(email => {
                if (!shareEmailList.includes(email)) {
                    shareEmailList.push(email);
                }

                if (!isValidEmail(email)) {
                    shareEmailErrors[email] = 'Email không hợp lệ.';
                } else if (shareEmailErrors[email] === 'Email không hợp lệ.') {
                    delete shareEmailErrors[email];
                }
            });

            const input = document.getElementById('emailChipInput');
            if (input) input.value = '';

            const successBox = document.getElementById('shareSuccessMessage');
            if (successBox) {
                successBox.classList.add('hidden');
                successBox.innerHTML = '';
            }

            renderEmailChips();
            renderShareEmailErrors();
        }

        function removeEmailChip(index) {
            const email = shareEmailList[index];

            shareEmailList.splice(index, 1);
            delete shareEmailErrors[email];

            renderEmailChips();
            renderShareEmailErrors();
        }

        function resetShareEmailUI() {
            shareEmailList = [];
            shareEmailErrors = {};

            const chipInput = document.getElementById('emailChipInput');
            const hiddenInput = document.getElementById('shareEmails');
            const errorBox = document.getElementById('shareEmailErrors');
            const successBox = document.getElementById('shareSuccessMessage');

            if (chipInput) chipInput.value = '';
            if (hiddenInput) hiddenInput.value = '';
            if (errorBox) {
                errorBox.classList.add('hidden');
                errorBox.innerHTML = '';
            }
            if (successBox) {
                successBox.classList.add('hidden');
                successBox.innerHTML = '';
            }

            renderEmailChips();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('emailChipInput');

            if (!input) return;

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addEmailChip(this.value);
                }

                if (e.key === 'Backspace' && this.value === '' && shareEmailList.length) {
                    shareEmailList.pop();
                    renderEmailChips();
                    renderShareEmailErrors();
                }
            });

            input.addEventListener('blur', function () {
                addEmailChip(this.value);
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();

                const text = (e.clipboardData || window.clipboardData).getData('text');
                addEmailChip(text);
            });
        });

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

                if (!data.length) {
                    container.innerHTML = `
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-5 text-center">
                            <div class="mx-auto mb-2 w-10 h-10 rounded-2xl bg-white text-slate-400 flex items-center justify-center">
                                <i class="fa-solid fa-user-plus"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-600">No one has access yet</p>
                            <p class="mt-1 text-xs text-slate-400">Share this note with others by email.</p>
                        </div>
                    `;
                    return;
                }

                data.forEach(share => {
                    const initials = share.recipient.email.charAt(0).toUpperCase();

                    container.innerHTML += `
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-slate-950 text-white flex items-center justify-center font-black flex-shrink-0">
                                    ${initials}
                                </div>

                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-slate-900 truncate">
                                        ${share.recipient.email}
                                    </div>
                                    <div class="text-xs text-slate-400 truncate">
                                        ${share.permission === 'edit' ? 'Can edit' : 'Read only'} · ${share.created_at}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 focus:ring-slate-950 focus:border-slate-950"
                                    onchange="updatePermission(${share.recipient_id}, this.value)">
                                    <option value="read" ${share.permission === 'read' ? 'selected' : ''}>Read</option>
                                    <option value="edit" ${share.permission === 'edit' ? 'selected' : ''}>Edit</option>
                                </select>

                                <button onclick="revoke(${share.recipient_id})"
                                    class="w-9 h-9 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
            });
        }

        const NOTE_COLOR_CLASSES = [
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

        const FONT_SIZE_CLASSES = [
            'text-sm',
            'text-base',
            'text-lg'
        ];

        function getSavedTheme() {
            return localStorage.getItem('theme') || 'light';
        }

        function getSavedNoteColor() {
            return localStorage.getItem('noteColor') || 'bg-white';
        }

        function getSavedFontSize() {
            return localStorage.getItem('noteFontSize') || 'text-base';
        }

        function openSettingsModal() {
            syncSettingsUI();

            const modal = document.getElementById('settingsModal');
            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeSettingsModal() {
            const modal = document.getElementById('settingsModal');
            if (!modal) return;

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function selectSetting(inputId, value, btn) {
            const input = document.getElementById(inputId);
            if (!input || !btn) return;

            input.value = value;

            const group = btn.parentElement;
            if (!group) return;

            group.querySelectorAll('.setting-option').forEach(item => {
                item.classList.remove('is-active', 'bg-white', 'text-gray-950', 'shadow-sm');
                item.classList.add('text-gray-600');
            });

            btn.classList.add('is-active', 'bg-white', 'text-gray-950', 'shadow-sm');
            btn.classList.remove('text-gray-600');
        }

        function selectNoteColor(value, btn) {
            const input = document.getElementById('settingNoteColor');
            if (!input || !btn) return;

            input.value = value;

            document.querySelectorAll('.note-color-option').forEach(item => {
                item.classList.remove('is-active', 'border-gray-950');
                item.classList.add('border-transparent');
            });

            btn.classList.add('is-active', 'border-gray-950');
            btn.classList.remove('border-transparent');
        }

        function selectTheme(value, btn) {
            const input = document.getElementById('settingTheme');
            if (!input || !btn) return;

            input.value = value;

            document.querySelectorAll('.theme-option').forEach(item => {
                item.classList.remove('is-active', 'border-gray-950', 'bg-white');
                item.classList.add('border-transparent', 'bg-gray-100');
            });

            btn.classList.add('is-active', 'border-gray-950', 'bg-white');
            btn.classList.remove('border-transparent', 'bg-gray-100');
        }

        function saveSettings() {
            const fontSizeInput = document.getElementById('settingFontSize');
            const noteColorInput = document.getElementById('settingNoteColor');
            const themeInput = document.getElementById('settingTheme');

            localStorage.setItem('noteFontSize', document.getElementById('settingFontSize').value);
            localStorage.setItem('noteColor', document.getElementById('settingNoteColor').value);
            localStorage.setItem('theme', document.getElementById('settingTheme').value);

            applySettings();
            closeSettingsModal();
        }

        /*
        Apply theme to whole page.
        Apply note color only to note cards.
        */
        function applySettings() {
            const theme = localStorage.getItem('theme') || 'light';
            const noteColor = localStorage.getItem('noteColor') || 'bg-white';
            const fontSize = localStorage.getItem('noteFontSize') || 'text-base';

            document.documentElement.classList.toggle('app-dark', theme === 'dark');

            document.querySelectorAll('.note-card, .shared-note-card').forEach(card => {
                card.classList.remove(
                    'bg-white',
                    'bg-gray-50',
                    'bg-yellow-50',
                    'bg-orange-50',
                    'bg-green-50',
                    'bg-blue-50',
                    'bg-purple-50',
                    'bg-rose-50',
                    'bg-gray-800'
                );

                card.classList.add(noteColor);

                const content = card.querySelector('.card-content, p');

                if (content) {
                    content.classList.remove('text-sm', 'text-base', 'text-lg');
                    content.classList.add(fontSize);
                }
            });
        }

        function refreshFilterChipTheme() {
            const theme = getSavedTheme();
            const isDark = theme === 'dark';

            document.querySelectorAll('.filter-chip').forEach(chip => {
                const isActive = chip.classList.contains('active');

                chip.style.backgroundColor = '';
                chip.style.borderColor = '';
                chip.style.color = '';

                if (!isDark) return;

                if (isActive) {
                    chip.style.backgroundColor = '#f8fafc';
                    chip.style.borderColor = '#f8fafc';
                    chip.style.color = '#020617';
                } else {
                    chip.style.backgroundColor = '#111827';
                    chip.style.borderColor = '#263244';
                    chip.style.color = '#e2e8f0';
                }
            });
        }

        function syncSettingsUI() {
            const fontSize = getSavedFontSize();
            const noteColor = getSavedNoteColor();
            const theme = getSavedTheme();

            const fontSizeInput = document.getElementById('settingFontSize');
            const noteColorInput = document.getElementById('settingNoteColor');
            const themeInput = document.getElementById('settingTheme');

            if (fontSizeInput) fontSizeInput.value = fontSize;
            if (noteColorInput) noteColorInput.value = noteColor;
            if (themeInput) themeInput.value = theme;

            document.querySelectorAll('.setting-option').forEach(btn => {
                btn.classList.remove('is-active', 'bg-white', 'text-gray-950', 'shadow-sm');
                btn.classList.add('text-gray-600');

                if ((btn.getAttribute('onclick') || '').includes(`'${fontSize}'`)) {
                    btn.classList.add('is-active', 'bg-white', 'text-gray-950', 'shadow-sm');
                    btn.classList.remove('text-gray-600');
                }
            });

            document.querySelectorAll('.note-color-option').forEach(btn => {
                btn.classList.remove('is-active', 'border-gray-950');
                btn.classList.add('border-transparent');

                if ((btn.getAttribute('onclick') || '').includes(`'${noteColor}'`)) {
                    btn.classList.add('is-active', 'border-gray-950');
                    btn.classList.remove('border-transparent');
                }
            });

            document.querySelectorAll('.theme-option').forEach(btn => {
                btn.classList.remove('is-active', 'border-gray-950', 'bg-white');
                btn.classList.add('border-transparent', 'bg-gray-100');

                if ((btn.getAttribute('onclick') || '').includes(`'${theme}'`)) {
                    btn.classList.add('is-active', 'border-gray-950', 'bg-white');
                    btn.classList.remove('border-transparent', 'bg-gray-100');
                }
            });
        }

        /*
        Initial load
        */
        applySettings();

        
        
        function setSidebarState(state) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const icon = document.getElementById('sidebarToggleIcon');

            const user = document.getElementById('sidebarUser');
            const userInfo = document.getElementById('userInfo');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const texts = document.querySelectorAll('.sidebar-text');

            const avatar = document.getElementById('userAvatar');

            const isCollapsed = state === 'collapsed';

            if (isCollapsed) {
                toggleBtn.style.left = 'calc(5rem - 18px)';
                userInfo.classList.add('justify-center', 'mx-auto', 'w-12', 'h-12');
                userInfo.classList.remove('gap-3');

                avatar.classList.remove('w-9', 'h-9');
                avatar.classList.add('w-8', 'h-8');
            } else {
                toggleBtn.style.left = 'calc(16rem - 18px)';

                userInfo.classList.remove('justify-center', 'mx-auto', 'w-12', 'h-12');
                userInfo.classList.add('gap-3');

                avatar.classList.remove('w-8', 'h-8');
                avatar.classList.add('w-9', 'h-9');
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

        function setActiveSidebar(route) {
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('bg-gray-100', 'text-gray-900', 'font-medium');
                link.classList.add('text-gray-600', 'hover:bg-gray-100');
            });

            const active = document.querySelector(`.sidebar-link[data-route="${route}"]`);

            if (active) {
                active.classList.add('bg-gray-100', 'text-gray-900', 'font-medium');
                active.classList.remove('text-gray-600');
            }
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

                if (url.includes('/notes/shared')) {
                    setActiveSidebar('shared');
                } else if (url.includes('/notes')) {
                    setActiveSidebar('notes');
                }

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

        if (window.location.pathname.includes('/notes/shared')) {
            setActiveSidebar('shared');
        } else {
            setActiveSidebar('notes');
        }

        
        window.refreshNotesIndex = async function () {
            const res = await fetch("{{ route('notes.index') }}", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');

            const newContainer = doc.querySelector('#notesContainer');
            const newFilters = doc.querySelector('#filterLabelsContainer');

            if (newContainer) {
                document.getElementById('notesContainer').innerHTML = newContainer.innerHTML;
            }

            if (newFilters && document.getElementById('filterLabelsContainer')) {
                document.getElementById('filterLabelsContainer').innerHTML = newFilters.innerHTML;
            }

            if (typeof applySettings === 'function') applySettings();
            if (typeof updateTimes === 'function') updateTimes();
            if (typeof applyView === 'function') applyView(currentView);
        }

        window.refreshSharedNotes = async function () {
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
            }

            if (typeof applySettings === "function") applySettings();

            if (typeof applySharedView === "function") {
                applySharedView(localStorage.getItem("sharedView") || "grid");
            }
        }

        document.addEventListener('submit', async function (event) {
            const form = event.target;

            if (!['profileInfoForm', 'passwordForm', 'deleteForm'].includes(form.id)) return;

            event.preventDefault();

            let btn;
            let messageBox;
            let successText;
            let errorText;

            if (form.id === 'profileInfoForm') {
                btn = document.getElementById('profileInfoSaveBtn');
                messageBox = document.getElementById('profileInfoMessage');
                successText = 'Cập nhật thông tin thành công.';
                errorText = 'Không thể cập nhật thông tin.';
            }

            if (form.id === 'passwordForm') {
                btn = document.getElementById('passwordSaveBtn');
                messageBox = document.getElementById('passwordMessage');
                successText = 'Đổi mật khẩu thành công.';
                errorText = 'Không thể đổi mật khẩu.';
            }

            if (form.id === 'deleteForm') {
                btn = document.getElementById('deleteBtn');
                messageBox = document.getElementById('deleteMessage');
                errorText = 'Không thể xóa tài khoản. Vui lòng kiểm tra mật khẩu.';
            }

            const formData = new FormData(form);
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');

            btn.innerHTML = form.id === 'deleteForm'
                ? `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Deleting...`
                : `<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...`;

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    if (data.errors) {
                        errorText = Object.values(data.errors).flat().join('<br>');
                    }

                    messageBox.classList.remove('hidden');
                    messageBox.innerHTML = `
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 shadow-sm">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-500"></i>
                                <div>${errorText}</div>
                            </div>
                        </div>
                    `;
                    return;
                }

                if (form.id === 'deleteForm') {
                    window.location.href = '/';
                    return;
                }

                messageBox.classList.remove('hidden');
                messageBox.innerHTML = `
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500"></i>
                            <div>${successText}</div>
                        </div>
                    </div>
                `;

                if (form.id === 'passwordForm') {
                    form.reset();
                }

                if (form.id === 'profileInfoForm') {
                    const name = formData.get('name');
                    const email = formData.get('email');

                    document.querySelectorAll('.profile-hero-name').forEach(el => el.textContent = name);
                    document.querySelectorAll('.profile-hero-email').forEach(el => el.textContent = email);
                }

            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-60', 'cursor-not-allowed');
                btn.innerHTML = originalText;
            }
        });
    </script>
</x-app-layout>