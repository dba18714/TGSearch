<?php

namespace App\ContentAudit;

class AuditResult
{
    public function __construct(
        private bool $isPassed,
        private array $maxRisk,
        private array $risks,
    ) {}

    public function isPassed(): bool
    {
        return $this->isPassed;
    }

    /* 
    $risks[] = [
        'category' => 'Porn',
        'score' => 0.99,
    ];
    */
    public function getRisks(): array
    {
        return $this->risks;
    }

    /* 
    $maxRisk = [
        'category' => 'Porn',
        'score' => 0.99,
    ];
    */
    public function getMaxRisk(): array
    {
        return $this->maxRisk;
    }
}
