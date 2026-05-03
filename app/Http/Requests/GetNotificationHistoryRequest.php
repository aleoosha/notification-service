<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\NotificationFilterDTO;
use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetNotificationHistoryRequest extends FormRequest
{
    /**
     * Определить, авторизован ли пользователь для этого запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получить правила валидации.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'status' => ['nullable', new Enum(NotificationStatus::class)],
            'channel' => ['nullable', new Enum(NotificationChannel::class)],
        ];
    }

    /**
     * Преобразовать данные запроса в типизированный DTO.
     */
    public function toDto(): NotificationFilterDTO
    {
        return new NotificationFilterDTO(
            userId: (int) $this->validated('user_id'),
            status: $this->validated('status'),
            channel: $this->validated('channel')
        );
    }
}
