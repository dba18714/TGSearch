<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use SergiX44\Nutgram\Nutgram;

class TelegramWebhook extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Telegram Webhook';
    protected static ?string $title = 'Telegram Webhook 管理';
    protected static ?string $navigationGroup = '系统管理';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.admin.pages.telegram-webhook';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('webhook_url')
                    ->label('Webhook URL')
                    ->placeholder(url('/api/telegram/webhook'))
                    ->url()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(Nutgram $bot): void
    {
        $data = $this->form->getState();

        try {
            $result = $bot->setWebhook($data['webhook_url']);

            if ($result) {
                Notification::make()
                    ->title('Webhook设置成功')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Webhook设置失败')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Webhook设置失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteWebhook(Nutgram $bot): void
    {
        try {
            $result = $bot->deleteWebhook();

            if ($result) {
                Notification::make()
                    ->title('Webhook删除成功')
                    ->success()
                    ->send();

                // 清空表单
                $this->form->fill();
            } else {
                Notification::make()
                    ->title('Webhook删除失败')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Webhook删除失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getWebhookInfo(Nutgram $bot): void
    {
        try {
            $info = $bot->getWebhookInfo();
            
            $message = "URL: {$info->url}\n";
            $message .= "Has Custom Certificate: " . ($info->has_custom_certificate ? 'Yes' : 'No') . "\n";
            $message .= "Pending Update Count: {$info->pending_update_count}\n";
            
            if ($info->last_error_message) {
                $message .= "Last Error: {$info->last_error_message}\n";
            }

            if ($info->url) {
                $this->form->fill(['webhook_url' => $info->url]);
            }

            Notification::make()
                ->title('Webhook信息')
                ->body($message)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('获取Webhook信息失败')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}