<?php

declare(strict_types=1);

namespace Honeystone\Seo\Tests\Browser\Support;

use Honeystone\Seo\Contracts\BuildsMetadata;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function app;
use function request;
use function seo;
use function view;

abstract class Page extends Component
{
    abstract protected function pageName(): string;

    abstract protected function otherPageUrl(): string;

    public function mount(): void
    {
        app()->forgetInstance(BuildsMetadata::class);

        seo()->title($this->pageName(), template: false)->description('Description for ' . $this->pageName());
    }

    public function render(): View
    {
        return view('seo-test::page', [
            'name' => $this->pageName(),
            'link' => '/' . request()->segment(1) . $this->otherPageUrl(),
        ])->layout('seo-test::layout-' . request()->segment(1));
    }
}

