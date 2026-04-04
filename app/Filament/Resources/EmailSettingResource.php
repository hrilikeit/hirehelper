<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSettingResource\Pages\EditEmailSetting;
use App\Filament\Resources\EmailSettingResource\Pages\ListEmailSettings;
use App\Models\EmailSetting;
use App\Support\AdminAccess;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EmailSettingResource extends Resource
{
    protected static ?string $model = EmailSetting::class;

    protected static ?string $modelLabel = 'Email';

    protected static ?string $pluralModelLabel = 'Emails';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | UnitEnum | null $navigationGroup = 'Access Control';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'email-settings';

    public static function canAccess(): bool
    {
        return AdminAccess::isSuperAdmin(auth()->user());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('is_active')
                ->label('Active')
                ->helperText('When disabled, this email will not be sent to users.'),
        ]);
    }

    /**
     * Map email setting key to the blade view name.
     */
    protected static function getTemplateView(string $key): ?string
    {
        $map = [
            'contract_active' => 'emails.contract-active',
            'verify_email' => 'emails.verify-email',
            'get_started' => 'emails.get-started',
            'password_reset' => null, // uses Laravel default
            'payment_failed' => 'emails.payment-failed',
            'payment_method_added' => 'emails.payment-method-added',
            'unread_message' => 'emails.unread-message',
            'weekly_tracked_hours' => 'emails.weekly-tracked-hours',
        ];

        return $map[$key] ?? null;
    }

    /**
     * Render a preview of the email template with dummy data.
     */
    protected static function renderPreview(string $key): string
    {
        $viewName = static::getTemplateView($key);

        if (! $viewName || ! view()->exists($viewName)) {
            return '<div style="text-align:center;padding:48px;color:#6b7280">No preview available for this email template.</div>';
        }

        try {
            // Provide dummy data for each template
            $data = match ($key) {
                'contract_active' => [
                    'userName' => 'John Doe',
                    'offer' => (object) [
                        'freelancer_display_name' => 'Jane Smith',
                        'freelancer_display_title' => 'Senior Developer',
                        'hourly_rate' => '45.00',
                        'weekly_limit' => 40,
                        'weekly_amount' => 1800.00,
                    ],
                    'projectUrl' => '#',
                ],
                'verify_email' => [
                    'user' => (object) ['name' => 'John Doe'],
                    'verificationUrl' => '#',
                ],
                'get_started' => [
                    'user' => (object) ['name' => 'John Doe'],
                ],
                'payment_failed' => [
                    'userName' => 'John Doe',
                    'offer' => (object) [
                        'freelancer_display_name' => 'Jane Smith',
                        'weekly_amount' => 1800.00,
                    ],
                    'billingUrl' => '#',
                    'amount' => '$1,800.00',
                ],
                'payment_method_added' => [
                    'user' => (object) ['name' => 'John Doe'],
                    'method' => (object) ['display_label' => 'PayPal (john@example.com)'],
                ],
                'unread_message' => [
                    'user' => (object) ['name' => 'John Doe'],
                    'senderName' => 'Jane Smith',
                    'messagePreview' => 'Hi John, I wanted to discuss the project timeline...',
                    'messagesUrl' => '#',
                ],
                'weekly_tracked_hours' => [
                    'userName' => 'John Doe',
                    'offer' => (object) [
                        'freelancer_display_name' => 'Jane Smith',
                        'hourly_rate' => '45.00',
                    ],
                    'hoursTracked' => 32.5,
                    'weekLabel' => 'Mar 24 – Mar 30',
                    'reportsUrl' => '#',
                ],
                default => [],
            };

            return view($viewName, $data)->render();
        } catch (\Throwable $e) {
            return '<div style="text-align:center;padding:48px;color:#dc2626">Error rendering preview: ' . e($e->getMessage()) . '</div>';
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->label('Key')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->paginated(false)
            ->recordActions([
                Action::make('previewEmail')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (EmailSetting $record) => $record->name . ' — Email Preview')
                    ->modalContent(fn (EmailSetting $record): HtmlString => new HtmlString(
                        '<div style="max-height:500px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px">'
                        . '<iframe srcdoc="' . e(static::renderPreview($record->key)) . '" style="width:100%;height:500px;border:none"></iframe>'
                        . '</div>'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailSettings::route('/'),
            'edit' => EditEmailSetting::route('/{record}/edit'),
        ];
    }
}
