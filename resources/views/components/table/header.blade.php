    @props(['chevron' => true])

    <th class="bg-lime-700 text-white px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
        <span class="flex items-center">
            {{ $slot }}
            @if ($chevron)
                <i class="ph-bold ph-caret-up-down ms-1"></i>
            @endif
        </span>
    </th>
