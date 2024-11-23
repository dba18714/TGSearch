<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Services\OpenAiModerationService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Filament\Support\Exceptions\Halt;
use Filament\Actions\Action;

class ContentModerationByOpenAi extends \Filament\Pages\Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = '内容审核 (by OpenAi)';
    protected static ?string $title = '内容审核';
    protected static ?string $slug = 'content-moderation';
    protected static ?string $navigationGroup = '系统管理';
    protected static string $view = 'filament.admin.pages.content-moderation';

    public array $data = [
        'content' => '',
    ];

    public ?array $result = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Textarea::make('content')
                            ->label('待检测内容')
                            ->required()
                            ->rows(5)
                            ->placeholder('请输入需要检测的内容...')
                            ->columnSpan('full'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('check')
                ->label('检测内容')
                ->action('check')
                ->color('primary'),
        ];
    }

    public function check(): void
    {
        try {
            $data = $this->form->getState();

            $moderationService = app(OpenAiModerationService::class);
            $this->result = $moderationService->getDetailedAnalysis($data['content']);

            if ($this->result['safe']) {
                Notification::make()
                    ->title('内容检测完成')
                    ->success()
                    ->body('该内容未发现问题')
                    ->send();
            } else {
                $issues = collect($this->result['issues'])
                    ->pluck('category')
                    ->join(', ');

                Notification::make()
                    ->title('发现潜在问题')
                    ->warning()
                    ->body("该内容包含以下问题：{$issues}")
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('检测失败')
                ->danger()
                ->body('API 调用失败：' . $e->getMessage())
                ->send();

            // 清空结果，避免显示旧的结果
            $this->result = null;
        }
    }

    public function getViewData(): array
    {
        return [
            'result' => $this->result,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
