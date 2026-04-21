<x-app-layout>
<div class="max-w-5xl mx-auto py-10">

    <h2 class="text-2xl font-bold mb-6">📨 Shared with me</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

       @foreach($shared as $item)
        @if($item->note)
            <div class="bg-white p-4 rounded-xl shadow border cursor-pointer"
                 onclick="window.location.href='/notes/editor/{{ $item->note->id }}'">

                <h3 class="font-semibold text-lg">
                    {{ $item->note->title }}
                </h3>

                <p class="text-sm text-gray-500 line-clamp-3">
                    {{ $item->note->content }}
                </p>

                <div class="mt-2 text-xs text-gray-400">
                    Shared by: {{ $item->owner->email }}
                </div>

                <div class="mt-1 text-xs">
                    @if($item->permission === 'edit')
                        ✏️ Can edit
                    @else
                        👁️ Read only
                    @endif
                </div>

            </div>
            @endif
        @endforeach

    </div>
</div>
</x-app-layout>