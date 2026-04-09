<?php

namespace App\Filament\Resources\ProjectActiveResource\Widgets;

use App\Models\ClientProject;
use App\Models\Timesheet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectActiveOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $activeProjectIds = ClientProject::where('status', 'active')->pluck('id');

        // Get offer IDs for all active projects
        $offerIds = \App\Models\ProjectOffer::whereIn('client_project_id', $activeProjectIds)
            ->whereIn('status', ['active', 'pending', 'accepted'])
            ->pluck('id');

        // This week debit (current week timesheet amounts)
        $weekStart = Timesheet::weekStartFor(now());
        $thisWeekTotal = Timesheet::whereIn('project_offer_id', $offerIds)
            ->where('week_start', $weekStart)
            ->sum('amount');

        // Total debt (all pending/unpaid timesheets)
        $totalDebt = Timesheet::whereIn('project_offer_id', $offerIds)
            ->where('status', 'pending')
            ->sum('amount');

        return [
            Stat::make('This week total', '$' . number_format((float) $thisWeekTotal, 2))
                ->description('Hours tracked this week across all projects'),
            Stat::make('Total outstanding debt', '$' . number_format((float) $totalDebt, 2))
                ->description('Unpaid timesheet balance across all projects')
                ->color($totalDebt > 0 ? 'danger' : 'success'),
        ];
    }
}
