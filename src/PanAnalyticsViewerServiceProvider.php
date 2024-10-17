<?php

namespace Mateffy\PanAnalyticsViewer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PanAnalyticsViewerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('pan-analytics-viewer')
            ->hasConfigFile()
            ->hasViews('pan-analytics')
            ->hasRoute('api');
    }
}
