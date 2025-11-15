<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('addCategory')
                ->label('Add New Category')
                ->visible(fn () => auth()->user()->can('add new category'))
                ->modalHeading('Add New Category')
                ->modalWidth('lg')
                ->form([
                    TextInput::make('new_category')
                        ->label('New Category')
                        ->required()
                        ->autocomplete('off')
                        ->datalist(Category::pluck('name')->toArray())
                        ->rules(['unique:categories,name']),
                ])
                ->action(function (array $data, CreateInventoryItem $livewire) {
                    $name = ucfirst(trim($data['new_category'] ?? ''));

                    if ($name === '') {
                        Notification::make()
                            ->title('No category provided')
                            ->danger()
                            ->send();
                        return;
                    }

                    // If category exists, notify and select it in the form
                    $existing = Category::where('name', $name)->first();
                    if ($existing) {
                        Notification::make()
                            ->title('Category already exists')
                            ->body("Category \"{$existing->name}\" already exists and was selected.")
                            ->warning()
                            ->send();

                        // fill the form's category_id so user sees it selected
                        $livewire->form->fill([
                            'category_id' => $existing->id,
                        ]);

                        return;
                    }

                    // Create the category and set created_by (fallback to 1)
                    $category = Category::create([
                        'name' => $name,
                        'created_by' => auth()->id() ?? 1,
                    ]);

                    Notification::make()
                        ->title('Category created')
                        ->body("Category \"{$category->name}\" created successfully and selected.")
                        ->success()
                        ->send();

                    // Fill the create form's category_id with newly created category
                    $livewire->form->fill([
                        'category_id' => $category->id,
                    ]);
                }),
        ];
    }

    public static function getRedirectUrlAfterCreate(): string
    {
        return static::getUrl('index');
    }
}
