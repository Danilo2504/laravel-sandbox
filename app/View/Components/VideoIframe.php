<?php

namespace App\View\Components;

use Closure;
use DOMDocument;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VideoIframe extends Component
{
    protected $isLandscape;
    public $iframe;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $iframe)
    {
        if(trim($iframe) === ''){
            throw new \InvalidArgumentException("El iframe debe ser de tipo string");
        }

        $this->iframe = $iframe;

        $dimensions = $this->extractIframeDimensions(iframeHtml: $iframe);

        if(!$dimensions){
            throw new \InvalidArgumentException("Impossibile rilevare le dimensioni (width e height) dall'iframe. Carica un iframe con le dimensioni specificate.");
        }

        $this->isLandscape = $this->isHorizontalIframe(width: $dimensions['width'], height: $dimensions['height']);

    }

    protected function extractIframeDimensions(string $iframeHtml): ?array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($iframeHtml);

        $iframe = $dom->getElementsByTagName('iframe')->item(0);
        if (!$iframe) {
            return null;
        }

        $width = $iframe->getAttribute('width') ?: null;
        $height = $iframe->getAttribute('height') ?: null;

        return compact('width', 'height');
    }

    public function isHorizontalIframe(int $width, int $height): bool
    {
        return $width > $height;
    }

    public function getContentClasses(): string
    {
        return $this->isLandscape ? 'video-content' : 'short-content';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.video-iframe');
    }
}

