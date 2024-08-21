<?php

declare(strict_types=1);

namespace Honeystone\Seo\Commands;

use Illuminate\Console\Command;

use function seo;

final class GenerateFavicons extends Command
{
    /**
     * @var string
     */
    protected $signature = 'seo:generate-favicons {key?}}';

    /**
     * @var string
     */
    protected $description = 'Generate the favicons.';

    public function handle(): void
    {
        seo()->faviconFetch($this->argument('key'));
    }
}
