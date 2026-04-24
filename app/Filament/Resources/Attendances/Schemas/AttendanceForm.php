<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                DateTimePicker::make('check_in_time')
                    ->required(),
                TextInput::make('check_in_lat')
                    ->required()
                    ->numeric(),
                TextInput::make('check_in_lng')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('check_out_time'),
                TextInput::make('check_out_lat')
                    ->numeric(),
                TextInput::make('check_out_lng')
                    ->numeric(),
                Toggle::make('is_late')
                    ->required(),
                TextInput::make('late_duration')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
