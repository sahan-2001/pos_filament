<?php

namespace App\Filament\Resources\NonInventoryItemResource\Pages;

use App\Filament\Resources\NonInventoryItemResource;
use App\Models\NonInventoryCategory;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateNonInventoryItem extends CreateRecord
{
    protected static string $resource = NonInventoryItemResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('addCategory')
                ->label('Add New Category')
                ->visible(fn () => auth()->user()->can('add new category'))
                ->action(function (array $data) {
                    static::getResource()::addCategory($data);
                })
                ->modalHeading('Add New Non-Inventory Category')
                ->modalWidth('lg')
                ->form([
                    TextInput::make('new_category')
                        ->label('New Category')
                        ->required()
                        ->autocomplete('off')
                        ->datalist(NonInventoryCategory::pluck('name')->toArray())
                        ->rules(['unique:non_inventory_categories,name']),
                ]),
        ];
    }

    public static function getRedirectUrlAfterCreate(): string
    {
        return static::getUrl('index');
    }
}
