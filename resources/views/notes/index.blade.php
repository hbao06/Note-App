<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Your Notes</h1>

            <a href="{{ route('notes.editor') }}"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                + Create Note
            </a>
        </div>

        @if ($notes->count() == 0)
            <p class="text-gray-500 text-lg">You have no notes yet.</p>
        @else

            <!-- GRID LAYOUT -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($notes as $note)
                    <div x-on:click="window.location='{{ route('notes.editor.edit', $note->id) }}'"

                        class="cursor-pointer relative group p-4 rounded-xl shadow-md border bg-yellow-50 hover:shadow-lg hover:-translate-y-1 transition transform duration-150">
                        
                        <!-- TITLE -->
                        <h2 class="text-xl font-semibold mb-2 text-gray-800">
                            {{ $note->title }}
                        </h2>

                        <!-- CONTENT -->
                        <p class="text-gray-700 text-sm line-clamp-3">
                            {{ $note->content }}
                        </p>

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
    </script>
     
</x-app-layout>



