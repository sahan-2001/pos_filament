<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Procurement/Traders';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('shop_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->unique(Supplier::class, 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('phone_1')
                    ->required()
                    ->maxLength(255)
                    ->unique(Supplier::class, 'phone_1', ignoreRecord: true),
                Forms\Components\TextInput::make('phone_2')
                    ->maxLength(255),
                Forms\Components\Hidden::make('outstanding_balance')
                    ->default(0),
                Forms\Components\Hidden::make('added_by')
                    ->default(fn () => auth()->user()->id),
                Forms\Components\Hidden::make('approved_by'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier_id')->sortable()->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT)),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('shop_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone_1')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('outstanding_balance')->sortable()->searchable(),
                ...(
                Auth::user()->can('view audit columns')
                    ? [
                        TextColumn::make('created_by')->label('Created By')->toggleable()->sortable(),
                        TextColumn::make('updated_by')->label('Updated By')->toggleable()->sortable(),
                        TextColumn::make('created_at')->label('Created At')->toggleable()->dateTime()->sortable(),
                        TextColumn::make('updated_at')->label('Updated At')->toggleable()->dateTime()->sortable(),
                    ]
                    : []
                    ),
            ])
            ->filters([
                // Define your filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Supplier $record) => auth()->user()->can('edit suppliers')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Supplier $record) => auth()->user()->can('delete suppliers')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->can('delete suppliers')),
            ])
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            // Define any related models or relations
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}