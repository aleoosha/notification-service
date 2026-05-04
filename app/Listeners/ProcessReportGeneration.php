<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ReportRequested;
use App\Jobs\GenerateUserReportJob;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProcessReportGeneration implements ShouldHandleEventsAfterCommit
{
    public function handle(ReportRequested $event): void
    {
        GenerateUserReportJob::dispatch($event->report);
    }
}
