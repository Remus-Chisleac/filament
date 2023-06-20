@php
    $datalistOptions = $getDatalistOptions();
    $hasMask = $hasMask();
    $id = $getId();
    $isConcealed = $isConcealed();
    $statePath = $getStatePath();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament-forms::affixes
        :state-path="$statePath"
        :prefix="$prefixLabel"
        :prefix-actions="$getPrefixActions()"
        :prefix-icon="$prefixIcon"
        :suffix="$suffixLabel"
        :suffix-actions="$getSuffixActions()"
        :suffix-icon="$suffixIcon"
        class="filament-forms-text-input-component"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    >
        <input
            @if ($hasMask)
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('text-input', 'filament/forms') }}"
                x-data="
                    textInputFormComponent({
                        getMaskOptionsUsing: (IMask) => {{ $getJsonMaskConfiguration() }},
                        state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')", lazilyEntangledModifiers: ['defer']) }},
                    })
                "
                wire:ignore
                @if ($isDebounced()) x-on:input.debounce.{{ $getDebounce() }}="$wire.$refresh" @endif
                @if ($isLazy()) x-on:blur="$wire.$refresh" @endif
            @else
                x-data="{}"
            @endif
            x-bind:class="{
                'border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500':
                    ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400':
                    @js($statePath) in $wire.__instance.serverMemo.errors,
            }"
            {{
                $getExtraInputAttributeBag()
                    ->merge($getExtraAlpineAttributes(), escape: false)
                    ->merge([
                        'autocapitalize' => $getAutocapitalize(),
                        'autocomplete' => $getAutocomplete(),
                        'autofocus' => $isAutofocused(),
                        'disabled' => $isDisabled(),
                        'dusk' => "filament.forms.{$statePath}",
                        'id' => $id,
                        'inputmode' => $getInputMode(),
                        'list' => $datalistOptions ? "{$id}-list" : null,
                        'max' => (! $isConcealed) ? $getMaxValue() : null,
                        'maxlength' => (! $isConcealed) ? $getMaxLength() : null,
                        'min' => (! $isConcealed) ? $getMinValue() : null,
                        'minlength' => (! $isConcealed) ? $getMinLength() : null,
                        'placeholder' => $getPlaceholder(),
                        'readonly' => $isReadOnly(),
                        'required' => $isRequired() && (! $isConcealed),
                        'step' => $getStep(),
                        'type' => $hasMask ? 'text' : $getType(),
                        $applyStateBindingModifiers('wire:model') => (! $hasMask) ? $statePath : null,
                    ], escape: false)
                    ->class([
                        'filament-forms-input block w-full shadow-sm outline-none transition duration-75 focus:relative focus:z-[1] focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white sm:text-sm',
                        'rounded-s-lg' => ! ($prefixLabel || $prefixIcon),
                        'rounded-e-lg' => ! ($suffixLabel || $suffixIcon),
                    ])
            }}
        />
    </x-filament-forms::affixes>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
