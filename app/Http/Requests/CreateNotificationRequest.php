<?php

namespace App\Http\Requests;

use App\DTO\NotificationDTO;
use App\Enums\NotificationChannel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'integer'],
            'text' => ['required', 'string', 'max:500'],
            'channel' => ['required', new Enum(NotificationChannel::class)],
        ];
    }

    public function toDto(): NotificationDTO
    {
        return NotificationDTO::fromArray($this->validated());
    }
}
