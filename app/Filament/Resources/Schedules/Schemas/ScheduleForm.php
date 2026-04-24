<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TimePicker::make('check_in_time')
                    ->required(),
                TimePicker::make('check_out_time')
                    ->required(),
            ]);
    }
}
