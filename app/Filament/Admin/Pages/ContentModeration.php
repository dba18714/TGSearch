<?php

namespace App\Filament\Admin\Pages;

use App\ContentAudit\AuditResult;
use App\ContentAudit\Facades\ContentAudit;
use Filament\Pages\Page;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\MaxWidth;

class ContentModeration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = '内容审核';
    protected static ?string $title = '内容审核';
    protected static ?string $slug = 'content-moderation';
    protected static ?string $navigationGroup = '系统管理';
    protected static string $view = 'filament.admin.pages.content-moderation';

    public array $data = [
        'content' => '',
        'service' => null,
    ];

    public ?bool $isPassed = null;
    public ?array $risks = null;
    public ?array $maxRisk = null;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::SevenExtraLarge;
    }

    public function mount(): void
    {
        $this->form->fill([
            'service' => config('content-audit.default'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('service')
                            ->label('服务提供商')
                            ->options([
                                'tencent' => '腾讯云内容审核',
                                'openai' => 'OpenAI 内容审核',
                            ])
                            ->required(),
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

            $result = ContentAudit::driver($data['service'])
                ->audit($data['content']);
            $this->isPassed = $result->isPassed();
            $this->risks = $result->getRisks();
            $this->maxRisk = $result->getMaxRisk();

            if ($this->isPassed) {
                Notification::make()
                    ->title('内容检测完成')
                    ->success()
                    ->body('该内容未发现问题')
                    ->send();
            } else {
                $issues = collect($this->risks)
                    ->pluck('category')
                    ->map(function ($category) {
                        $categoryMap = [
                            'Porn' => '色情',
                            'Abuse' => '辱骂',
                            'Ad' => '广告',
                            'Illegal' => '违法',
                            'Spam' => '垃圾信息'
                        ];
                        return $categoryMap[$category] ?? $category;
                    })
                    ->join('、');

                Notification::make()
                    ->title('发现潜在问题')
                    ->warning()
                    ->body("该内容包含以下问题：{$issues}")
                    ->send();
            }
            $this->dispatch('content-checked');
        } catch (\Exception $e) {
            Notification::make()
                ->title('检测失败')
                ->danger()
                ->body('API 调用失败：' . $e->getMessage())
                ->send();


            $this->reset([
                'isPassed',
                'risks',
                'maxRisk',
            ]);
        }
    }

}
