<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientProjectResource\RelationManagers\TimesheetsRelationManager;
use App\Filament\Resources\ProjectActiveResource\Pages\EditProjectActive;
use App\Filament\Resources\ProjectActiveResource\Pages\ListProjectActives;
use App\Filament\Resources\ProjectActiveResource\RelationManagers\EmailLogsRelationManager;
use App\Filament\Resources\ProjectActiveResource\RelationManagers\InvoicesRelationManager;
use App\Models\ClientBillingMethod;
use App\Models\ClientProject;
use App\Models\ProjectMessage;
use App\Models\User;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectActiveResource extends Resource
{
    protected static ?string $model = ClientProject::class;

    protected static ?string $modelLabel = 'Active project';

    protected static ?string $pluralModelLabel = 'Projects Active';

    protected static ?string $navigationLabel = 'Projects Active';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';

    protected static string | UnitEnum | null $navigationGroup = 'Sales & Delivery';

    protected static ?int $navigationSort = 12;

    protected static ?string $slug = 'projects-active';

    public static function canAccess(): bool
    {
        return AdminAccess::canAccessNonSalesResource(auth()->user());
    }

    public static function getEloquentQuery(): Builder
    {
        return AdminAccess::scopeActiveProjects(
            parent::getEloquentQuery()->whereIn('status', ['active', 'accepted']),
            auth()->user()
        );
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client')
                ->schema([
                    Placeholder::make('client_name')
                        ->label('Name')
                        ->content(fn (?ClientProject $record) => $record?->user?->name ?? '—'),
                    Placeholder::make('client_email')
                        ->label('Email')
                        ->content(fn (?ClientProject $record) => $record?->user?->email ?? '—'),
                    Placeholder::make('client_company')
                        ->label('Company')
                        ->content(fn (?ClientProject $record) => $record?->user?->company ?: '—'),
                    Placeholder::make('client_country')
                        ->label('Country')
                        ->content(fn (?ClientProject $record) => $record?->user?->country ?: 'Not detected'),
                    Placeholder::make('client_last_login')
                        ->label('Last login')
                        ->content(fn (?ClientProject $record) => $record?->user?->last_login_at?->diffForHumans() ?: 'Never'),
                    Placeholder::make('client_registered')
                        ->label('Registered')
                        ->content(fn (?ClientProject $record) => $record?->user?->created_at?->format('M j, Y g:i A') ?? '—'),
                ])
                ->columns(3),

            Section::make('Project details')
                ->schema([
                    Placeholder::make('title_display')
                        ->label('Title')
                        ->content(fn (?ClientProject $record) => $record?->title ?? '—')
                        ->columnSpanFull(),
                    Placeholder::make('description_display')
                        ->label('Description')
                        ->content(fn (?ClientProject $record) => $record?->description ?? '—')
                        ->columnSpanFull(),
                    Placeholder::make('experience_display')
                        ->label('Experience level')
                        ->content(fn (?ClientProject $record) => $record?->experience_level ?? '—'),
                    Placeholder::make('timeframe_display')
                        ->label('Timeframe')
                        ->content(fn (?ClientProject $record) => $record?->timeframe ?? '—'),
                    Placeholder::make('external_ref_display')
                        ->label('External reference')
                        ->content(fn (?ClientProject $record) => $record?->external_reference ?: '—'),
                    Placeholder::make('payment_method_display')
                        ->label('Payment method')
                        ->content(function (?ClientProject $record) {
                            if (! $record?->user_id) {
                                return '—';
                            }
                            $method = ClientBillingMethod::where('user_id', $record->user_id)
                                ->where('is_default', true)
                                ->first();
                            return $method ? $method->display_label . ($method->verified_at ? ' (verified)' : ' (not verified)') : 'Not added';
                        }),
                    Placeholder::make('freelancer_display')
                        ->label('Freelancer')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                            return $offer ? $offer->freelancer_display_name . ' — $' . number_format((float) $offer->hourly_rate, 2) . '/hr' : '—';
                        }),
                    Placeholder::make('sales_display')
                        ->label('Sales manager')
                        ->content(fn (?ClientProject $record) => $record?->salesManager?->name ?? '—'),
                ])
                ->columns(2),

            Section::make('Status')
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending (move to Projects Pending)',
                            'active' => 'Active',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                            'archived' => 'Archived (move to Archive)',
                        ])
                        ->required(),
                    Select::make('project_manager_id')
                        ->label('Project manager')
                        ->options(fn () => User::query()
                            ->whereIn('role', ['superadmin', 'admin', 'project_manager'])
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->searchable(),
                    Textarea::make('acceptance_notes')
                        ->rows(5)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('PayPal & Payments')
                ->schema([
                    Placeholder::make('total_paid_display')
                        ->label('Total paid')
                        ->content(function (?ClientProject $record) {
                            if (! $record) {
                                return '$0.00';
                            }
                            $total = \App\Models\Invoice::where('client_project_id', $record->id)
                                ->where('status', 'paid')
                                ->sum('amount');
                            return '$' . number_format((float) $total, 2);
                        }),
                    Placeholder::make('total_pending_display')
                        ->label('Outstanding balance')
                        ->content(function (?ClientProject $record) {
                            if (! $record) {
                                return '$0.00';
                            }
                            $offer = $record->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                            if (! $offer) {
                                return '$0.00';
                            }
                            $pending = \App\Models\Timesheet::where('project_offer_id', $offer->id)
                                ->where('status', 'pending')
                                ->sum('amount');
                            return '$' . number_format((float) $pending, 2);
                        }),
                    Placeholder::make('paypal_subscription_id_display')
                        ->label('Subscription ID')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                            $sub = $offer ? \App\Models\WeeklySubscription::where('project_offer_id', $offer->id)->latest()->first() : null;
                            return $sub?->paypal_subscription_id ?: '—';
                        }),
                    Placeholder::make('paypal_status_display')
                        ->label('PayPal status')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                            $sub = $offer ? \App\Models\WeeklySubscription::where('project_offer_id', $offer->id)->latest()->first() : null;
                            return $sub?->paypal_subscription_status ?: '—';
                        }),
                    Placeholder::make('paypal_payer_email_display')
                        ->label('Payer email')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                            $sub = $offer ? \App\Models\WeeklySubscription::where('project_offer_id', $offer->id)->latest()->first() : null;
                            return $sub?->paypal_payer_email ?: '—';
                        }),
                    Placeholder::make('paypal_next_billing_display')
                        ->label('Next billing')
                        ->content(function (?ClientProject $record) {
                            $offer = $record?->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                            $sub = $offer ? \App\Models\WeeklySubscription::where('project_offer_id', $offer->id)->latest()->first() : null;
                            return $sub?->next_billing_at?->format('M j, Y g:i A') ?: '—';
                        }),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ClientProject $record) => $record->user?->email ?? ''),
                TextColumn::make('title')->label('Project')->searchable()->sortable()->limit(35)->wrap(),
                TextColumn::make('offers_summary')
                    ->label('Freelancer')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                        return $offer ? $offer->freelancer_display_name : '—';
                    }),
                TextColumn::make('hired_rate')
                    ->label('Rate')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                        return $offer ? '$' . number_format((float) $offer->hourly_rate, 2) . '/hr' : '—';
                    })
                    ->description(function (ClientProject $record) {
                        $offer = $record->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                        if (! $offer || ! $offer->weekly_limit) {
                            return '';
                        }
                        return $offer->weekly_limit . ' hrs/week';
                    }),
                TextColumn::make('this_week_debit')
                    ->label('This week')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                        if (! $offer) {
                            return '$0.00';
                        }
                        $weekStart = \App\Models\Timesheet::weekStartFor(now());
                        $amount = \App\Models\Timesheet::where('project_offer_id', $offer->id)
                            ->where('week_start', $weekStart)
                            ->value('amount') ?? 0;
                        return '$' . number_format((float) $amount, 2);
                    })
                    ->sortable(false),
                TextColumn::make('total_debt')
                    ->label('Total debt')
                    ->state(function (ClientProject $record) {
                        $offer = $record->offers()->whereIn('status', ['active', 'pending', 'accepted'])->first();
                        if (! $offer) {
                            return '$0.00';
                        }
                        $pending = \App\Models\Timesheet::where('project_offer_id', $offer->id)
                            ->where('status', 'pending')
                            ->sum('amount');
                        return '$' . number_format((float) $pending, 2);
                    })
                    ->sortable(false)
                    ->color(fn (string $state) => $state !== '$0.00' ? 'danger' : null),
                TextColumn::make('user.last_login_at')
                    ->label('Last login')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                Action::make('openMessages')
                    ->label(function (ClientProject $record) {
                        $unread = $record->messages()
                            ->whereNull('admin_read_at')
                            ->where('sender_type', 'client')
                            ->count();
                        return $unread > 0 ? '+' . $unread : '';
                    })
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color(function (ClientProject $record) {
                        $unread = $record->messages()
                            ->whereNull('admin_read_at')
                            ->where('sender_type', 'client')
                            ->count();
                        return $unread > 0 ? 'danger' : 'gray';
                    })
                    ->badge(function (ClientProject $record) {
                        $unread = $record->messages()
                            ->whereNull('admin_read_at')
                            ->where('sender_type', 'client')
                            ->count();
                        return $unread > 0 ? $unread : null;
                    })
                    ->badgeColor('danger')
                    ->tooltip(function (ClientProject $record) {
                        $unread = $record->messages()
                            ->whereNull('admin_read_at')
                            ->where('sender_type', 'client')
                            ->count();
                        return $unread > 0 ? $unread . ' unread message(s)' : 'No new messages';
                    })
                    ->url(fn (ClientProject $record) => ConversationResource::getUrl('edit', ['record' => $record])),
                Action::make('viewNotes')
                    ->label('')
                    ->icon('heroicon-o-bell')
                    ->color(fn (ClientProject $record) => filled($record->acceptance_notes) ? 'warning' : 'gray')
                    ->tooltip(fn (ClientProject $record) => filled($record->acceptance_notes) ? 'View acceptance notes' : 'No notes')
                    ->modalHeading('Acceptance Notes')
                    ->form([
                        Textarea::make('acceptance_notes')
                            ->label('Notes')
                            ->rows(8)
                            ->default(fn (ClientProject $record) => $record->acceptance_notes),
                    ])
                    ->modalSubmitActionLabel('Save')
                    ->action(function (ClientProject $record, array $data) {
                        $record->update(['acceptance_notes' => $data['acceptance_notes'] ?? '']);
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn () => AdminAccess::isSuperAdmin(auth()->user())),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\ProjectActiveResource\Widgets\ProjectActiveOverview::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            TimesheetsRelationManager::class,
            InvoicesRelationManager::class,
            EmailLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectActives::route('/'),
            'edit' => EditProjectActive::route('/{record}/edit'),
        ];
    }
}
