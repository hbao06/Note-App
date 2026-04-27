<x-app-layout>
<div class="max-w-5xl mx-auto py-10">

    <h2 class="text-2xl font-bold mb-6">📨 Shared with me</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @foreach($shared as $item)
            @if($item->note)
                <div class="relative bg-white p-4 rounded-xl shadow border cursor-pointer hover:shadow-md transition"
                     onclick="window.location.href='/notes/editor/{{ $item->note->id }}'">

                    <!-- STATUS ICONS -->
                    <div class="absolute top-3 right-3 flex items-center gap-3 text-sm">

                        <!-- Shared note -->
                        <span title="Shared note" class="text-blue-500">
                            <i class="fa-solid fa-user-group"></i>
                        </span>

                        <!-- Pinned note -->
                        @if($item->note->is_pinned)
                            <span title="Pinned note" class='text-gray-900 rotate-45'>
                                <i class="fa-solid fa-thumbtack"></i>
                            </span>
                        @endif

                        <!-- Locked note -->
                        @if($item->note->note_password)
                            <span title="Locked note" class="text-yellow-500">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                        @endif

                    </div>

                    <h3 class="font-semibold text-lg pr-24">
                        {{ $item->note->title ?: 'Untitled' }}
                    </h3>

                    <p class="text-sm text-gray-500 line-clamp-3 mt-1">
                        {{ $item->note->content }}
                    </p>

                    <div class="mt-3 text-xs text-gray-400">
                        Shared by: {{ $item->owner->email }}
                    </div>

                    <div class="mt-1 text-xs font-medium">
                        @if($item->permission === 'edit')
                            <span class="text-orange-500">✏️ Can edit</span>
                        @else
                            <span class="text-gray-600">👁️ Read only</span>
                        @endif
                    </div>

                </div>
            @endif
        @endforeach

    </div>
</div>
</x-app-layout>