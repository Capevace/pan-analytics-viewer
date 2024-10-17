<?php

Route::get(config('pan-analytics-viewer.endpoint'), \Mateffy\PanAnalyticsViewer\GetPanViewerData::class)
    ->name('pan-analytics-viewer.endpoint');
