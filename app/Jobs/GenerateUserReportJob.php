<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateUserReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Report $report) {}

    public function handle(): void
    {
        try {
            $stats = Notification::query()
                ->where('user_id', $this->report->user_id)
                ->selectRaw('channel, status, count(*) as count')
                ->groupBy('channel', 'status')
                ->get();

            $fileName = "reports/user_{$this->report->user_id}_".now()->timestamp.'.csv';
            $handle = fopen('php://temp', 'r+');

            fputcsv($handle, ['Channel', 'Status', 'Count']);
            /** @var Notification $row */
            foreach ($stats as $row) {
                /** @var int $count */
                $count = $row->getAttribute('count');

                fputcsv($handle, [
                    $row->channel->value,
                    $row->status->value,
                    (string) $count,
                ]);
            }

            rewind($handle);
            Storage::disk('local')->put($fileName, stream_get_contents($handle));
            fclose($handle);

            $this->report->update([
                'status' => ReportStatus::COMPLETED,
                'file_path' => $fileName,
                'completed_at' => now(),
            ]);
        } catch (Throwable $e) {
            $this->report->update(['status' => ReportStatus::FAILED]);
            throw $e;
        }
    }
}
