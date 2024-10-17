<?php

namespace Mateffy\PanAnalyticsViewer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pan\Contracts\AnalyticsRepository;
use Pan\ValueObjects\Analytic;

class GetPanViewerData
{
    public function __invoke(Request $request, AnalyticsRepository $repository): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'error' => 'Invalid signature'
            ], 401);
        }

        $eventsQuery = $request->header('X-Pan-Events');
        $events = explode(',', $eventsQuery ?? '');

        if (!is_array($events)) {
            $events = [];
        }

        $events = array_filter($events);

        $analytics = collect($repository->all())
            ->when(count($events) > 0, fn (Collection $analytics) => $analytics
                ->filter(fn (Analytic $analytic) => in_array($analytic->name, $events))
            )
            ->map(fn (Analytic $analytic) => $analytic->toArray())
            ->values()
            ->toArray();

        return response()->json([
            'analytics' => $analytics
        ]);
    }
}
