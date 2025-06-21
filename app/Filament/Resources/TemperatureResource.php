<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemperatureResource\Pages;
use App\Models\Temperature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Route;
use Illuminate\Support\Carbon;

class TemperatureResource extends Resource
{
    protected static ?string $model = Temperature::class;
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $modelLabel = 'Data Sensor';
    protected static ?string $navigationGroup = 'Monitoring Server';
    protected static ?string $navigationLabel = 'Monitoring Suhu';
    protected static ?string $slug = 'temperature-monitoring';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sensor Data')
                    ->schema([
                        Forms\Components\TextInput::make('temperature')
                            ->label('Suhu (°C)')
                            ->numeric()
                            ->required()
                            ->maxValue(100),
                        Forms\Components\TextInput::make('humidity')
                            ->label('Kelembaban (%)')
                            ->numeric()
                            ->required()
                            ->maxValue(100),
                        Forms\Components\Select::make('status')
                            ->label('Status Asap')
                            ->options([
                                'Normal' => 'Normal',
                                'Detected' => 'Detected',
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
                Tables\Columns\TextColumn::make('temperature')
                    ->label('Suhu (°C)')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Kelembaban (%)')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('air')
                    ->label('Level Udara (ppm)')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Asap')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Detected' => 'danger',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Pencatatan')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->searchable()
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Asap')
                    ->options([
                        'Normal' => 'Normal',
                        'Detected' => 'Detected',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', Carbon::parse($date)),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', Carbon::parse($date)),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Data')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->required(),
                        Forms\Components\Select::make('status_filter')
                            ->label('Filter Status')
                            ->options([
                                'all' => 'Semua Status',
                                'Normal' => 'Normal',
                                'Detected' => 'Detected',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $query = Temperature::query()
                            ->whereBetween('created_at', [
                                Carbon::parse($data['start_date'])->startOfDay(),
                                Carbon::parse($data['end_date'])->endOfDay()
                            ]);
                        
                        if ($data['status_filter'] !== 'all') {
                            $query->where('status', $data['status_filter']);
                        }
                        
                        $records = $query->get();
                        
                        $filename = 'temperature_data_export_'.now()->format('Ymd_His').'.csv';
                        
                        $headers = [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => "attachment; filename=$filename",
                        ];
                        
                        return response()->streamDownload(function () use ($records) {
                            $file = fopen('php://output', 'w');
                            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM
                            
                            fputcsv($file, [
                                'Suhu (°C)', 
                                'Kelembaban (%)', 
                                'Status Asap', 
                                'Waktu Pencatatan'
                            ]);
                            
                            foreach ($records as $record) {
                                fputcsv($file, [
                                    $record->temperature,
                                    $record->humidity,
                                    $record->status,
                                    $record->created_at->format('d/m/Y H:i:s')
                                ]);
                            }
                            
                            fclose($file);
                        }, $filename, $headers);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemperatures::route('/'),
            'create' => Pages\CreateTemperature::route('/create'),
            'edit' => Pages\EditTemperature::route('/{record}/edit'),
        ];
    }
}