<?php

namespace App\ContentAudit;

class AuditResult
{
    public function __construct(
        private bool $isPassed,
        private array $risk,
        private float $overallRiskLevel,
    ) {}

    public function isPassed(): bool
    {
        return $this->isPassed;
    }

    public function getRisk(): array
    {
        return $this->risk;
    }

    public function getOverallRiskLevel(): int // 0-100
    {
        return $this->overallRiskLevel;
    }
}