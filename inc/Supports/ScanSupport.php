<?php

namespace Chwnam\ThreadsToPosts\Supports;

use Bojaghi\Contract\Support;
use Chwnam\ThreadsToPosts\Interfaces\TaskRunner;

class ScanSupport implements Support
{
    public function __construct(private TaskRunner $taskRunner)
    {
    }

    public function doHeavyScan(): void
    {
    }

    public function getHeavyScanCompletedTime(string $userId): int
    {
        return get_transient("ttp_heavy_scan_completed_$userId");
    }

    public function isHeavyScanCompleted(string $userId): bool
    {
        return (bool)$this->getHeavyScanCompletedTime($userId);
    }

    public function setHeavyScanCompleted(string $userId): void
    {
        set_transient("ttp_heavy_scan_completed_$userId", time());
    }
}
