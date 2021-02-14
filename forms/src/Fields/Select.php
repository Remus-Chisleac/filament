<?php

namespace Filament\Fields;

use Filament\Fields\Concerns;

class Select extends InputField
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    public $emptyOptionLabel = 'None';

    public $emptyOptionsMessage = 'No options match your search.';

    public $options = [];

    public function emptyOptionLabel($label)
    {
        $this->emptyOptionLabel = $label;

        return $this;
    }

    public function emptyOptionsMessage($message)
    {
        $this->emptyOptionsMessage = $message;

        return $this;
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }
}
