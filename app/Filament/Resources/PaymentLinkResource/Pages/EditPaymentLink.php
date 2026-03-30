<?php

namespace App\Filament\Resources\PaymentLinkResource\Pages;

use App\Filament\Resources\PaymentLinkResource;
use Filament\Resources\Pages\EditRecord;

class EditPaymentLink extends EditRecord
{
    protected static string $resource = PaymentLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
