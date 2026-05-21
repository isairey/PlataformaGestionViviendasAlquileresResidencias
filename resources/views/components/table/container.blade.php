@props(['id'])

<div class="px-6 py-4">
    <table id="{{ $id }}">
        <thead>
            <tr>
                {{ $header }}
            </tr>
        </thead>
        <tbody>
            {{ $body ?? '' }}
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const table = document.getElementById("{{ $id }}");
            if (table) {
                const dataTable = new DataTable("#{{ $id }}", {
                    searchQuerySeparator: "+",
                    classes: {
                        input: "table-search",
                        selector: "table-selector",
                    }
                });
            }
        });
    </script>
@endPush
