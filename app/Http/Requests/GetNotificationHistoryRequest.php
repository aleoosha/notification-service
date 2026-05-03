<?php

namespace App\Http\Requests;

use App\DTO\NotificationFilterDTO;
use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetNotificationHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'status' => ['nullable', new Enum(NotificationStatus::class)],
            'channel' => ['nullable', new Enum(NotificationChannel::class)],
        ];
    }

    public function toDto(): NotificationFilterDTO
    {
        return new NotificationFilterDTO(
            userId: (int) $this->validated('user_id'),
            status: $this->validated('status'),
            channel: $this->validated('channel')
        );
    }
}
