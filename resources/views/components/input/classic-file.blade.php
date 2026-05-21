@props(['id', 'disabled' => false, 'help' => null, 'imgId' => null])

@php
    $helpId = $id . '_help';
@endphp

<input type="file" id="{{ $id }}"
    {{ $attributes->merge([
        'class' => "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
            focus:ring-lime-600 focus:border-lime-600 block w-full
            file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:rounded-sm file:font-outfit file:text-xs file:mr-3
            file:bg-gray-800 file:hover:bg-gray-700 file:text-white",
    ]) }}
    @disabled($disabled) aria-describedby="{{ $helpId }}" />

@if ($help)
    <p id="{{ $helpId }}" class="text-xs text-gray-500 mt-1">{{ $help }}</p>
@endif

@if ($imgId)
    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.getElementById(@json($id));
                const imgId = @json($imgId);
                console.log(input);

                if (input && imgId) {
                    input.addEventListener('change', function(event) {
                        const file = input.files && input.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.getElementById(imgId);
                                if (img) {
                                    img.src = e.target.result;
                                }
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });
        </script>
    @endPushOnce
@endif
