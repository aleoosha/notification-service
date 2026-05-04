<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\ReportOrderDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class OrderReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function toDto(): ReportOrderDTO
    {
        return new ReportOrderDTO(
            startDate: Carbon::parse($this->validated('start_date')),
            endDate: Carbon::parse($this->validated('end_date')),
        );
    }
}
