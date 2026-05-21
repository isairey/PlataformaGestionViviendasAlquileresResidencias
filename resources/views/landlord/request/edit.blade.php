{{-- resources/views/landlord/request/edit.blade.php --}}

<x-app-layout title="Edit Request Status">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Request Status
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-8">
            <form method="POST" action="{{ route('request.update', $request) }}">
                @csrf
                @method('PUT')

                {{-- Display Title and Description as read-only for context --}}
                <div class="mb-6">
                    <x-input.label for="title">Title</x-input.label>
                    <x-input.text id="title" type="text" name="title" :value="$request->title" disabled />
                </div>

                <div class="mb-6">
                    <x-input.label for="description">Description</x-input.label>
                    <x-input.textarea id="description" name="description" rows="6" disabled>{{ $request->description }}</x-input.textarea>
                </div>

                <div class="mb-6">
                    <x-input.label for="status" required>Status</x-input.label>
                    @php
                        $statusOptions = [
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            'rejected' => 'Rejected',
                        ];
                    @endphp
                    <x-input.select
                        id="status"
                        name="status"
                        :options="$statusOptions"
                        :selected="old('status', $request->status)"
                        placeholder="Select Status"
                    />
                    <x-input.error for="status" />
                </div>

                <div class="flex items-center justify-center gap-x-12 gap-y-3 flex-wrap">
                    <x-a variant="clean" href="{{ route('request.index') }}">
                        Cancel
                    </x-a>
                    <x-button type="submit">
                        Update Status
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
