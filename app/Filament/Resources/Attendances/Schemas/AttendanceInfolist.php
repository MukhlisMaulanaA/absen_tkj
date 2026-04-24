<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Attendance;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('check_in_time')
                    ->dateTime(),
                TextEntry::make('check_in_lat')
                    ->numeric(),
                TextEntry::make('check_in_lng')
                    ->numeric(),
                TextEntry::make('check_out_time')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('check_out_lat')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('check_out_lng')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_late')
                    ->boolean(),
                TextEntry::make('late_duration')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Attendance $record): bool => $record->trashed()),
            ]);
    }
}
