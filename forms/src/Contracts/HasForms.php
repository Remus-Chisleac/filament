<?php

namespace Filament\Forms\Contracts;

use Filament\Support\Contracts\TranslatableContentDriver;
use Livewire\TemporaryUploadedFile;

interface HasForms
{
    public function dispatchFormEvent(mixed ...$args): void;

    public function getActiveFormLocale(): ?string;

    public function makeFormTranslatableContentDriver(): ?TranslatableContentDriver;

    public function getFormComponentFileAttachment(string $statePath): ?TemporaryUploadedFile;

    public function getFormComponentFileAttachmentUrl(string $statePath): ?string;

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getFormSelectOptionLabels(string $statePath): array;

    public function getFormSelectOptionLabel(string $statePath): ?string;

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getFormSelectOptions(string $statePath): array;

    /**
     * @return array<array{'label': string, 'value': string}>
     */
    public function getFormSelectSearchResults(string $statePath, string $search): array;

    /**
     * @return array<array{name: string, size: int, type: string, url: string} | null> | null
     */
    public function getFormUploadedFiles(string $statePath): ?array;

    public function removeFormUploadedFile(string $statePath, string $fileKey): void;

    /**
     * @param  array<array-key>  $fileKeys
     */
    public function reorderFormUploadedFiles(string $statePath, array $fileKeys): void;

    /**
     * @param  array<string, array<mixed>> | null  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     * @return array<string, mixed>
     */
    public function validate(?array $rules = null, array $messages = [], array $attributes = []): array;
}
