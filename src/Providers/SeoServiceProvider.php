<?php

declare(strict_types=1);

namespace Honeystone\Seo\Providers;

use Honeystone\Seo\Commands\GenerateFavicons;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Honeystone\Seo\Contracts\RegistersGenerators;
use Honeystone\Seo\MetadataDirector;
use Honeystone\Seo\Registry;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function app;
use function dirname;
use function function_exists;

final class SeoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('honeystone-seo')
            ->setBasePath(dirname(__DIR__))
            ->hasCommand(GenerateFavicons::class)
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->registerMetadataDirectory();
    }

    public function packageBooted(): void
    {
        Blade::directive('metadata', static function (string ...$only): string {
            if (function_exists('csp_nonce')) {
                return '<?php echo seo()->jsonLdNonce(csp_nonce())->generate(...($only ?? [])); ?>';
            }
            return '<?php echo seo()->generate(...($only ?? [])); ?>';
        });
        Blade::directive('openGraphPrefix', static fn (): string => '<?php echo seo()->openGraphPrefix(); ?>');
    }

    private function registerMetadataDirectory(): void
    {
        app()->singleton(BuildsMetadata::class, MetadataDirector::class);
        app()->bind(RegistersGenerators::class, Registry::class);
    }
}
