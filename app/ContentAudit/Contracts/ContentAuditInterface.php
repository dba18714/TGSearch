<?php

namespace App\ContentAudit\Contracts;

use App\ContentAudit\AuditResult;

interface ContentAuditInterface
{
    /**
     * 检查内容并返回详细结果
     */
    // public function checkContent(string $content): array;

    /**
     * 快速检查内容是否安全
     */
    // public function isSafe(string $content): bool;

    /**
     * 获取详细的审核分析结果
     */
    public function audit(string $content): AuditResult;
}