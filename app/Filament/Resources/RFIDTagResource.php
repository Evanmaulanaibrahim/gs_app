<?php

namespace App\Filament\Resources;

use App\Models\RFIDTag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\RFIDTagResource\Pages;

class RFIDTagResource extends Resource
{
    protected static ?string $model = RFIDTag::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'RFID Tags';
    protected static ?string $modelLabel = 'RFID Tag';
    protected static ?string $navigationGroup = 'Access Control';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tag Information')
                    ->schema([
                        Forms\Components\TextInput::make('uid')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('RFID UID'),
                            
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('position')
                            ->options([
                                'employee' => 'Employee',
                                'manager' => 'Manager',
                                'director' => 'Director',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uid')
                    ->searchable()
                    ->sortable()
                    ->label('RFID UID'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('position')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'director' => 'danger',
                        'manager' => 'warning',
                        default => 'success',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->options([
                        'employee' => 'Employee',
                        'manager' => 'Manager',
                        'director' => 'Director',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRFIDTags::route('/'),
            'create' => Pages\CreateRFIDTag::route('/create'),
            'edit' => Pages\EditRFIDTag::route('/{record}/edit'),
        ];
    }
}