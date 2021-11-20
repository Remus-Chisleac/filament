<table {{ $attributes->class(['w-full text-left divide-y table-auto']) }}>
    <thead>
        <tr class="bg-gray-50">
            {{ $header }}
        </tr>
    </thead>

    <tbody class="divide-y whitespace-nowrap">
        {{ $slot }}
    </tbody>

    @if ($footer)
        <tfoot>
            <tr class="bg-gray-50">
                {{ $footer }}
            </tr>
        </tfoot>
    @endif
</table>
