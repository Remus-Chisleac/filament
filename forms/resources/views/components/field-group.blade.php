@props([
    'errorKey' => null,
    'for' => null,
    'helpMessage' => null,
    'hint' => null,
    'label' => null,
    'labelPrefix' => null,
    'required' => false,
])

<div class="h-full flex items-center">
    <div class="space-y-2 w-full">
        @if ($label || $hint)
            <div class="flex items-center justify-between space-x-2">
                <div class="flex space-x-2">
                    {{ $labelPrefix }}

                    @if ($label)
                        <label :for="$for" class="text-sm leading-tight font-medium cursor-pointer">
                            {{ $label }}

                            @if ($required)
                                <sup class="font-medium text-red-700">*</sup>
                            @endif
                        </label>
                    @endif
                </div>

                @if ($hint)
                    <div class="hint">
                        @markdown($hint)
                    </div>
                @endif
            </div>
        @endif

        {{ $slot }}

        @if ($errorKey)
            @error($errorKey)
                <span class="block text-red-700 text-sm leading-tight">
                    {{ $message }}
                </span>
            @enderror
        @endif

        @if ($helpMessage)
            <div class="help">
                @markdown($helpMessage)
            </div>
        @endif
    </div>
</div>
