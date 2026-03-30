<?php

namespace App\Filament\Widgets;

use App\Models\PaymentLink;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentLinksOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Open links', (string) PaymentLink::query()->where('status', 'open')->count()),
            Stat::make('Paid links', (string) PaymentLink::query()->where('status', 'paid')->count()),
            Stat::make('Paid total', '$' . number_format((float) PaymentLink::query()->where('status', 'paid')->sum('amount'), 2)),
        ];
    }
}
