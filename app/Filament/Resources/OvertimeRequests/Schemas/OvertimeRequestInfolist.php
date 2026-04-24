<?php

namespace App\Filament\Resources\OvertimeRequests\Schemas;

use App\Models\OvertimeRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OvertimeRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('attendance.id')
                    ->label('Attendance'),
                TextEntry::make('request_time')
                    ->dateTime(),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('approved_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('approved_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (OvertimeRequest $record): bool => $record->trashed()),
            ]);
    }
}
