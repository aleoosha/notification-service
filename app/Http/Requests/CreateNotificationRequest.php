<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\NotificationDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateNotificationRequest extends FormRequest
{
    /**
     * Определить, авторизован ли пользователь для выполнения этого запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idempotency_key' => $this->header('X-Idempotency-Key'),
        ]);
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:500'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'channel' => ['required', 'string'],
            'idempotency_key' => ['required', 'string'],
        ];
    }

    public function toDto(): NotificationDTO
    {
        return NotificationDTO::fromArray($this->validated());
    }
}
