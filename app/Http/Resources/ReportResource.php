<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Report $resource */
class ReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->uuid,
            'status' => $this->resource->status->value,
            'requested_at' => $this->resource->requested_at->toDateTimeString(),
            'completed_at' => $this->resource->completed_at?->toDateTimeString(),
            'download_url' => $this->resource->file_path
                ? route('reports.download', ['report' => $this->resource->uuid])
                : null,
        ];
    }
}
