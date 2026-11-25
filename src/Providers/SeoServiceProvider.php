<?php

declare(strict_types=1);

namespace Honeystone\Seo\Providers;

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
use function resource_path;

final class SeoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('honeystone-seo')->setBasePath(dirname(__DIR__))->hasConfigFile()->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->registerMetadataDirectory();
    }

    public function packageBooted(): void
    {
        $this->registerInertiaPublishing();

        Blade::directive('metadata', static function (string ...$only): string {
            if (function_exists('csp_nonce')) {
                return '<?php echo seo()->jsonLdNonce(csp_nonce())->generate(...($only ?? [])); ?>';
            }

            if (config('csp.nonce_generator') !== null) {
                return '<?php echo seo()->jsonLdNonce(app(config(\'csp.nonce_generator\'))->generate())->generate(...($only ?? [])); ?>';
            }

            return '<?php echo seo()->generate(...($only ?? [])); ?>';
        });
        Blade::directive('openGraphPrefix', static fn (): string => '<?php echo seo()->openGraphPrefix(); ?>');
    }

    private function registerInertiaPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $source = $this->package->basePath('/../resources/js/inertia');
        $destination = resource_path('js/vendor/honeystone-seo');

        $shared = ["$source/shared/seo.ts" => "$destination/shared/seo.ts"];
        $react = ["$source/react/SeoHead.tsx" => "$destination/react/SeoHead.tsx"];
        $vue = ["$source/vue3/SeoHead.vue" => "$destination/vue3/SeoHead.vue"];

        $this->publishes($shared + $react, 'honeystone-seo-inertia-react');
        $this->publishes($shared + $vue, 'honeystone-seo-inertia-vue');
        $this->publishes($shared + $react + $vue, 'honeystone-seo-inertia');
    }

    private function registerMetadataDirectory(): void
    {
        app()->singleton(BuildsMetadata::class, MetadataDirector::class);
        app()->bind(RegistersGenerators::class, Registry::class);
    }
}
