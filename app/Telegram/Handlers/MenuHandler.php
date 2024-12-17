<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class MenuHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $user = $bot->get('user');
        $messageId = $bot->callbackQuery()?->message->message_id;

        // 获取机器人用户名
        $botUsername = $bot->getMe()->username;

        // 创建键盘按钮
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('👤 个人中心', callback_data: 'menu:profile'),
                InlineKeyboardButton::make('💰 邀请赚钱', callback_data: 'menu:invite'),
            )
            ->addRow(
                InlineKeyboardButton::make('➕ 添加到群组', url: "https://t.me/{$botUsername}?startgroup=start")
            );

        $text = <<<HTML
            👋 欢迎使用易搜机器人！
            
            直接向我发送关键词即可搜索:
            - 频道
            - 群组
            - 机器人
            - 资源内容
            
            查看帮助 👉 /help 

            👉 <a href='tg://setlanguage?lang=zhcncc'>点这里安装【简体中文】</a>👈
            HTML;

        if ($messageId) {
            $bot->editMessageText(
                text: $text,
                parse_mode: ParseMode::HTML,
                reply_markup: $keyboard,
            );
        } else {
            $bot->sendMessage(
                text: $text,
                parse_mode: ParseMode::HTML,
                disable_web_page_preview: true,
                reply_markup: $keyboard,
            );
        }
    }

    public function invite(Nutgram $bot): void
    {
        $user = $bot->get('user');
        $messageId = $bot->callbackQuery()?->message->message_id;

        // 创建键盘按钮
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('👤 个人中心', callback_data: 'menu:profile'),
            )
            ->addRow(
                InlineKeyboardButton::make('<< 返回主菜单', callback_data: 'menu:home')
            );

        $bot->editMessageText(
            text: <<<HTML
<b>🎯 邀请好友赚取USDT收益</b>

💎 <b>推广奖励</b>
• 直接邀请：每邀请1位新用户，您会获得 <b>0.08 USDT</b>
• 间接邀请：您的下级每邀请1人，您会获得 <b>0.02 USDT</b>

📱 <b>您的专属邀请链接</b>
<code>https://t.me/yisou123bot?start={$user->tg_id}</code>

✨ 推广文案(点击复制)：
<code>🔍 发现一个超好用的Telegram搜索机器人！
• 搜索群组/频道/机器人
• 支持资源内容搜索
• 完全免费使用
👉 立即体验：t.me/yisou123bot?start={$user->tg_id}</code>

💡 温馨提示：邀请的好友越多，收益越高！
HTML,
            message_id: $messageId,
            parse_mode: ParseMode::HTML,
            reply_markup: $keyboard,
        );
    }

    public function profile(Nutgram $bot): void
    {
        $user = $bot->get('user');
        $messageId = $bot->callbackQuery()?->message->message_id;

        $descendantsCounts = $user->getDescendantsCountByLevel();
        $directCount = $descendantsCounts[1] ?? 0; // 直推用户数量 (level 1)
        $indirectCount = $descendantsCounts[2] ?? 0; // 间推用户数量 (level 2)

        // 创建键盘按钮
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('💰 邀请赚钱', callback_data: 'menu:invite')
            )

            ->addRow(
                InlineKeyboardButton::make('<< 返回主菜单', callback_data: 'menu:home')
            );

        $bot->editMessageText(
            text: <<<HTML
<b>👤 个人中心</b>

📋 <b>基本信息</b>
• TGID：<code>{$user->tg_id}</code>
• 昵称：{$user->name}

💰 <b>资产信息</b>
• 账户余额：<b>{$user->balance}</b> USDT
• 佣金余额：<b>{$user->commission_balance}</b> USDT
• 已提现金额：<b>xxx</b> USDT

📊 <b>推广数据</b>
• 直推用户：<b>{$directCount}</b> 人
• 间推用户：<b>{$indirectCount}</b> 人
• 累计佣金：<b>{$user->total_commission}</b> USDT

💡 提示：点击⬇️邀请赚钱⬇️开始推广赚取佣金
HTML,
            message_id: $messageId,
            parse_mode: ParseMode::HTML,
            reply_markup: $keyboard,
        );
    }
}
