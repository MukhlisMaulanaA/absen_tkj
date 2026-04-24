<?php

namespace App\Filament\Resources\OvertimeRequests\Pages;

use App\Filament\Resources\OvertimeRequests\OvertimeRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOvertimeRequest extends ViewRecord
{
    protected static string $resource = OvertimeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
