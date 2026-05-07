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

        @if($shareNotifications->count() > 0)
            <div class="mb-5 space-y-3">
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
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

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

<script>
    let sharedCurrentView = localStorage.getItem('sharedView') || 'grid';

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

    
</script>