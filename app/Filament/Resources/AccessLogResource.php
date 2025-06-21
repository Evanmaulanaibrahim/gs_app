<?php

namespace App\Filament\Resources;

use App\Models\AccessLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\AccessLogResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class AccessLogResource extends Resource
{
    protected static ?string $model = AccessLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Access Logs';
    protected static ?string $modelLabel = 'Access Log';
    protected static ?string $navigationGroup = 'Access Control';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Access Details')
                    ->schema([
                        Forms\Components\Select::make('rfid_tag_id')
                            ->relationship('rfidTag', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('RFID Tag'),
                            
                        Forms\Components\Toggle::make('access_granted')
                            ->required()
                            ->inline(false),
                            
                        Forms\Components\TextInput::make('device_id')
                            ->maxLength(255)
                            ->required(),
                            
                        Forms\Components\DateTimePicker::make('access_time')
                            ->required()
                            ->default(now()),
                    ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rfidTag.name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                    
                Tables\Columns\TextColumn::make('rfidTag.position')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'director' => 'danger',
                        'manager' => 'warning',
                        default => 'success',
                    }),
                    
                Tables\Columns\IconColumn::make('access_granted')
                    ->boolean()
                    ->sortable()
                    ->label('Access'),
                    
                Tables\Columns\TextColumn::make('device_id')
                    ->searchable()
                    ->sortable()
                    ->label('Device ID'),
                    
                Tables\Columns\TextColumn::make('access_time')
                    ->dateTime()
                    ->sortable()
                    ->label('Time'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('access_granted')
                    ->label('Access Status')
                    ->options([
                        '1' => 'Granted',
                        '0' => 'Denied',
                    ]),
                    
                Tables\Filters\SelectFilter::make('rfid_tag_id')
                    ->relationship('rfidTag', 'name')
                    ->searchable()
                    ->preload()
                    ->label('RFID Tag'),
                    
                Tables\Filters\Filter::make('access_time')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('access_time', '>=', $date))
                            ->when($data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('access_time', '<=', $date));
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('access_time', 'desc')
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccessLogs::route('/'),
            'create' => Pages\CreateAccessLog::route('/create'),
            'edit' => Pages\EditAccessLog::route('/{record}/edit'),
        ];
    }
}