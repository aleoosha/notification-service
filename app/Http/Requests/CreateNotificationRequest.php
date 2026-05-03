<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\NotificationDTO;
use App\Enums\NotificationChannel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateNotificationRequest extends FormRequest
{
    /**
     * Определить, авторизован ли пользователь для выполнения этого запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получить правила валидации, применяемые к запросу.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'integer'],
            'text' => ['required', 'string', 'max:500'],
            'channel' => ['required', new Enum(NotificationChannel::class)],
        ];
    }

    /**
     * Преобразовать валидированные данные в DTO.
     */
    public function toDto(): NotificationDTO
    {
        return NotificationDTO::fromArray($this->validated());
    }
}
