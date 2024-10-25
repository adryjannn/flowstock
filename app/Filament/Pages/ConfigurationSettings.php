<?php

namespace App\Filament\Pages;

use App\Models\Configuration;
use App\Services\SyncPrestashopService;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ConfigurationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Integracje';
    protected static ?string $navigationGroup = 'Użytkownicy';
    protected static string $view = 'filament.pages.configuration-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'PRESTASHOP_URL' => Configuration::getValue('PRESTASHOP_URL'),
            'PRESTASHOP_API_KEY' => Configuration::getValue('PRESTASHOP_API_KEY'),
            'FAKTUROWNIA_API_KEY' => Configuration::getValue('FAKTUROWNIA_API_KEY'),
            'FAKTUROWNIA_URL' => Configuration::getValue('FAKTUROWNIA_URL'),
            'INPOST_ID_ORGANIZATION' => Configuration::getValue('INPOST_ID_ORGANIZATION'),
            'INPOST_API_KEY' => Configuration::getValue('INPOST_API_KEY'),
            'INPOST_API_URL' => Configuration::getValue('INPOST_API_URL')
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('Configuration Settings'))->tabs([
                    Tab::make(__('PrestaShop'))->schema([
                        Section::make(__('PrestaShop Configuration'))->schema([
                            TextInput::make('PRESTASHOP_URL')
                                ->label(__('PrestaShop URL'))
                                ->required()
                                ->default(Configuration::getValue('PRESTASHOP_URL')),
                            TextInput::make('PRESTASHOP_API_KEY')
                                ->label(__('PrestaShop API Key'))
                                ->required()
                                ->default(Configuration::getValue('PRESTASHOP_API_KEY'))
                        ])->columns(2),
                    ]),
                ])
            ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save'))
                ->submit('save')
             ,
        ];
    }

    public function save(): void
    {
        $configurationData = $this->form->getState();

        foreach ($configurationData as $key => $value) {
            Configuration::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Notification::make()
            ->success()
            ->title(__('Configuration saved successfully'))
            ->send();
    }

    public function testPrestashopConnection()
    {
        if (SyncPrestashopService::checkConnection()) {
            Notification::make()
                ->success()
                ->title(__('Connected'))
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title(__('Error'))
                ->send();
        }
    }

    public function syncPrestashopData()
    {
        try {
            $service = new SyncPrestashopService();
            $service->syncAllData();

            $currentDateTime = Carbon::now()->toDateTimeString();

            Notification::make()
                ->success()
                ->title(__('PrestaShop synchronized successfully'))
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('PrestaShop synchronization failed: ') . $e->getMessage())
                ->persistent()
                ->send();
        }
    }
}
