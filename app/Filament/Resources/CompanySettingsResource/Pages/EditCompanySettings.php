<?php

namespace App\Filament\Resources\CompanySettingsResource\Pages;

use App\Filament\Resources\CompanySettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class EditCompanySettings extends EditRecord
{
    protected static string $resource = CompanySettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
{
    return [
        $this->getSaveFormAction()
            ->label('Save Changes')
            ->icon('heroicon-o-lock-closed')
            ->action(function ($action) {
                // This will open the password confirmation modal
                $action->requiresConfirmation();
            })
            ->requiresConfirmation()
            ->modalHeading('Security Verification Required')
            ->modalDescription('For security reasons, please confirm your identity to save these changes.')
            ->modalSubmitActionLabel('Confirm & Save')
            ->modalIcon('heroicon-o-shield-check')
            ->form([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->required()
                    ->autocomplete('current-password')
                    ->rule(function () {
                        return function (string $attribute, $value, $fail) {
                            // Rate limiting to prevent brute force attacks
                            $executed = RateLimiter::attempt(
                                'password-confirmation:'.auth()->id(),
                                3, // 3 attempts
                                function() use ($value) {
                                    return Hash::check($value, auth()->user()->password);
                                },
                                60 // 1 minute cooldown
                            );

                            if (!$executed) {
                                $fail('Too many attempts. Please try again in 60 seconds.');
                                return;
                            }

                            if (!Hash::check($value, auth()->user()->password)) {
                                $fail('The password you entered is incorrect. Please try again.');
                            }
                        };
                    })
                    ->extraInputAttributes(['class' => 'focus:ring-primary-500']),
            ]),
            
        $this->getCancelFormAction()
            ->label('Discard Changes')
            ->color('gray'),
    ];
}
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Company Settings Updated')
            ->body('The company settings were successfully updated by ' . auth()->user()->email)
            ->duration(5000)
            ->icon('heroicon-o-check-circle')
            // Choose one of these options:
            // Option 1: For database notifications (requires notifications table)
            ->sendToDatabase(auth()->user())
            ;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        
        if (isset($data['owner'])) {
            $data['owner']['updated_by'] = auth()->id();
        }
        
        if (isset($data['management'])) {
            foreach ($data['management'] as &$item) {
                if (isset($item)) {
                    $item['updated_by'] = auth()->id();
                }
            }
        }
        
        return $data;
    }

    protected function fillForm(): void
    {
        $state = $this->getRecord()->load(['owner', 'management'])->toArray();
        
        if (isset($state['management'])) {
            $state['management'] = array_map(function ($item) {
                return [
                    'id' => $item['id'] ?? null,
                    'user_id' => $item['user_id'] ?? null,
                    'position' => $item['position'] ?? null,
                    'appointed_date' => $item['appointed_date'] ?? null,
                ];
            }, $state['management']);
        }
        
        $this->form->fill($state);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            // Save owner data
            if (isset($data['owner'])) {
                $ownerData = $data['owner'];
                if ($record->owner) {
                    $record->owner()->update($ownerData);
                } else {
                    $record->owner()->create($ownerData);
                }
            }
            
            // Save management data
            if (isset($data['management'])) {
                $this->saveManagementData($record, $data['management']);
            }
            
            // Save company data
            $record->update($data);
            
            return $record;
        });
    }

    protected function saveManagementData(Model $company, array $managementData): void
    {
        $currentIds = $company->management()->pluck('id')->toArray();
        $updatedIds = [];
        
        foreach ($managementData as $item) {
            if (!isset($item)) continue;
            
            if (isset($item['id'])) {
                // Update existing record
                $company->management()->where('id', $item['id'])->update($item);
                $updatedIds[] = $item['id'];
            } else {
                // Create new record
                $created = $company->management()->create($item);
                $updatedIds[] = $created->id;
            }
        }
        
        // Delete removed items
        $toDelete = array_diff($currentIds, $updatedIds);
        if (!empty($toDelete)) {
            $company->management()->whereIn('id', $toDelete)->delete();
        }
    }

    public static function getRedirectUrlAfterCreate(): string
    {
        return static::getUrl('index');
    }
}