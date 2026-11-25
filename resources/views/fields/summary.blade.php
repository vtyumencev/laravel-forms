@php use Xenio\LivewireForms\Fields\Summary; @endphp
@props([
    'field',
    'statePath' => 'state',
])

@php
    /**
     * @var Summary $field
     */
@endphp

<div class="col-span-full" x-data="{ isShown: false }">
    <button class="text-gamma font-bold flex items-center gap-2" type="button" x-on:click="isShown = ! isShown">
        <span x-show="isShown">
            Zusammenfassung ausblenden
        </span>
        <span x-show="!isShown">
            Zusammenfassung anzeigen
        </span>
        <span class="size-4 flex justify-center items-center">
            <svg class="stroke-current h-full w-auto duration-200" x-bind:class="{ 'rotate-90': isShown, 'rotate-270': !isShown }" xmlns="http://www.w3.org/2000/svg" width="23" height="53" viewBox="0 0 23 53" fill="none">
                <path d="M20.5 50.6653L1.99609 26.3976L20.1722 2" stroke-width="4" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </span>
    </button>
    <div class="mt-2" x-show="isShown" x-collapse>
        <table class="max-md:wrap-anywhere">
            @foreach($this->getFormSummary() as $item)
                <tr @class(['relative', 'group' => $item['type'] === 'input'])>
                    <td @class(['py-2 px-3 min-w-28', 'font-bold pt-6' => $item['type'] !== 'input'])
                        colspan="{!! $item['type'] === 'input' ? '1' : '0' !!}">
                        <div class="absolute inset-0 group-even:bg-gamma/10 rounded-md"></div>
                        <div class="relative">
                            {!! $item['label'] !!}
                        </div>
                    </td>
                    @if($item['value'])
                        <td class="py-2 px-3">
                            {{ $item['value'] }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</div>
