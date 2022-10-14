<?php

namespace Filament\Support\Assets;

use Composer\InstalledVersions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Css extends Asset
{
    protected string | Htmlable | null $html = null;

    public function html(string | Htmlable | null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function getHref(): string
    {
        $href = '/css/filament/';

        $package = $this->getPackage();

        if (filled($package)) {
            $href .= "{$package}/";
        }

        $href .= "{$this->getName()}.css";

        return $href;
    }

    public function getHtml(): Htmlable
    {
        $html = $this->html;

        if (Str::of($html)->contains('<link')) {
            return $html instanceof Htmlable ? $html : new HtmlString($html);
        }

        $html ??= (asset($this->getHref()) . '?v=' . InstalledVersions::getVersion('filament/support'));

        return new HtmlString("<link href=\"{$html}\" rel=\"stylesheet\" />");
    }

    public function getPublicPath(): string
    {
        $path = '';

        $package = $this->getPackage();

        if (filled($package)) {
            $path .= $package;
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= "{$this->getName()}.css";

        return public_path("css/filament/{$path}");
    }
}
