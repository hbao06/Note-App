<x-app-layout>
    @include('notes.partials.editor', [
        'note' => $note,
        'canEdit' => $canEdit
    ])
</x-app-layout>