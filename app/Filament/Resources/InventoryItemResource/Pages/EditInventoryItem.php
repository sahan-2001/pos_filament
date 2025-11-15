<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use App\Models\Category; // Import the Category model
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;

class EditInventoryItem extends EditRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('addCategory')
                ->label('Add New Category')
                ->visible(fn () => auth()->user()->can('add new category'))
                ->action(fn (array $data) => static::getResource()::addCategory($data))
                ->modalHeading('Add New Category')
                ->modalWidth('lg')
                ->form([
                    TextInput::make('new_category')
                        ->label('New Category')
                        ->required()
                        ->autocomplete('off')
                        ->datalist(Category::pluck('name')->toArray())
                        ->rules(['unique:categories,name']),
                ]),
        ];
    }

    public static function getRedirectUrlAfterCreate(): string
    {
        return static::getUrl('index');
    }
}