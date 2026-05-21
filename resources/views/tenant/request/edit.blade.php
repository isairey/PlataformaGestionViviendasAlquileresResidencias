{{-- resources/views/tenant/request/edit.blade.php --}}

<x-app-layout title="Edit Request">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Request
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-8">
            <form method="POST" action="{{ route('request.update', $request) }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <x-input.label for="title" required>Title</x-input.label>
                    <x-input.text id="title" type="text" name="title" :value="old('title', $request->title)" autofocus />
                    <x-input.error for="title" />
                </div>

                <div class="mb-6">
                    <x-input.label for="description" required>Description</x-input.label>
                    <x-input.textarea id="description" name="description"
                        rows="6">{{ old('description', $request->description) }}</x-input.textarea>
                    <x-input.error for="description" />
                </div>

                <div class="flex items-center justify-center gap-x-12 gap-y-3 flex-wrap">
                    <x-a variant="clean" href="{{ route('request.index') }}">
                        Cancel
                    </x-a>
                    <x-button type="submit">
                        Update Request
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
