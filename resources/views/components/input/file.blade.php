@props([
    'id',
    'name',
    'help' => null,
    'disabled' => false,
    'multiple' => false,
    'accept' => null,
    'preview' => null,
    'circle' => false,
])

@php
    $helpId = $id . '_help';
@endphp

<div x-data="{
    files: [],
    multiple: {{ $multiple ? 'true' : 'false' }},
    addFiles(newFiles) {
        if (this.multiple) return;
        if (!newFiles || newFiles.length === 0) return;

        const file = newFiles[0];
        const isImage = file.type.startsWith('image/');

        if (isImage) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.files = [{
                    file: file,
                    name: file.name,
                    isImage: isImage,
                    preview: e.target.result
                }];
            };
            reader.readAsDataURL(file);
        } else {
            this.files = [{
                file: file,
                name: file.name,
                isImage: false,
                preview: null
            }];
        }

        if (this.$refs.fileInput) {
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.fileInput.files = dt.files;
        }
    },
    removeFile() {
        this.files = [];
        if ($refs.fileInput) $refs.fileInput.value = '';
    }
}">
    @if (!$multiple)
        <!-- Single file mode with drag and drop + preview -->
        <div x-show="files.length === 0">
            <label for="{{ $id }}"
                class="relative flex flex-col items-center justify-center w-full px-4 py-6 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer
                   hover:border-lime-600 bg-gray-50 hover:bg-gray-100
                   focus-within:border-lime-600 focus-within:bg-gray-100
                   transition-all duration-200"
                @dragover.prevent="$el.classList.add('border-lime-600', 'bg-gray-50')"
                @dragleave.prevent="$el.classList.remove('border-lime-600', 'bg-gray-50')"
                @drop.prevent="$el.classList.remove('border-lime-600', 'bg-gray-50'); addFiles($event.dataTransfer.files)">

                <div class="text-center text-gray-600">
                    <p class="font-medium">Click to upload{{ $accept === 'image/*' ? ' an image' : ' a file' }}</p>
                    <p class="text-sm">or drag and drop</p>
                </div>

                <input x-ref="fileInput" type="file" name="{{ $name }}" id="{{ $id }}"
                    @if ($accept) accept="{{ $accept }}" @endif
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    @change="addFiles($event.target.files)" @disabled($disabled)
                    aria-describedby="{{ $helpId }}" />
            </label>
        </div>

        @if ($help)
            <p id="{{ $helpId }}" class="text-xs text-gray-500 mt-1">{{ $help }}</p>
        @endif

        <!-- Preview for single file uploads -->
        <template x-if="files.length > 0">
            @if ($circle)
                <div x-cloak class="relative max-w-sm mt-4 mx-auto">
                @else
                    <div x-cloak class="relative w-full mt-4 mx-auto">
            @endif
            <!-- Image preview -->

            <template x-if="files[0].isImage">
                @if ($circle)
                    <img :src="files[0].preview" :alt="files[0].name"
                        class="border border-gray-200 shadow-xs aspect-square w-full object-cover rounded-full" />
                @else
                    <div
                        class="w-full rounded-lg border border-gray-200 shadow-xs object-cover flex justify-center items-center">
                        <img :src="files[0].preview" :alt="files[0].name" class="rounded-lg" />
                    </div>
                @endif
            </template>




            <!-- Non-image file preview -->
            <template x-if="!files[0].isImage">
                <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200 shadow-xs">
                    <div class="shrink-0 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="files[0].name"></p>
                    </div>
                </div>
            </template>

            <!-- File info and remove button -->
            <div class="absolute top-2 right-2 flex space-x-1">
                {{-- @if (!$circle) --}}
                <span class="sm:block hidden bg-black bg-opacity-50 text-white text-xs rounded-md px-2 py-1"
                    x-text="files[0].name"></span>
                {{-- @endif --}}
                <button type="button" @click="removeFile()"
                    class="bg-white rounded-full p-1 shadow-lg hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    @else
        <input type="file" name="{{ $name }}" id="{{ $id }}" multiple
            @if ($accept) accept="{{ $accept }}" @endif
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
            focus:ring-lime-600 focus:border-lime-600 block w-full focus:outline-hidden
            file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:px-4 file:rounded-sm file:font-outfit file:text-xs file:mr-3
            file:bg-gray-800 file:hover:bg-gray-700 file:text-white"
            @disabled($disabled) aria-describedby="{{ $helpId }}" />

        @if ($help)
            <p id="{{ $helpId }}" class="text-xs text-gray-500 mt-1">{{ $help }}</p>
        @endif
    @endif
</div>
