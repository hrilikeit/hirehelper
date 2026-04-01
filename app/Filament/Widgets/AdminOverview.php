<?php

namespace App\Filament\Widgets;

use App\Models\ClientProject;
use App\Models\Freelancer;
use App\Models\ProjectOffer;
use App\Models\User;
use App\Support\AdminAccess;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverview extends BaseWidget
{
    protected static ?int $sort = -3;

    public static function canView(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Clients', (string) User::query()->where('role', 'client')->count()),
            Stat::make('Freelancers', (string) Freelancer::query()->where('status', 'active')->count()),
            Stat::make('Projects', (string) ClientProject::query()->count()),
            Stat::make('Active offers', (string) ProjectOffer::query()->where('status', 'active')->count()),
            Stat::make('Pending offers', (string) ProjectOffer::query()->where('status', 'pending')->count()),
        ];
    }
}
