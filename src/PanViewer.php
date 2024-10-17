<?php

declare(strict_types=1);

namespace Mateffy\PanAnalyticsViewer;

use Illuminate\Support\Collection;
use Pan\Contracts\AnalyticsRepository;
use Pan\ValueObjects\Analytic;

class PanViewer
{
    public function __construct(protected AnalyticsRepository $analyticsRepository)
    {
    }

    public function get(array $events): Collection
    {
        return collect($this->analyticsRepository->all())
            ->map(fn (Analytic $item) => $item->toArray());
    }
}
