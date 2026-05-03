<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            $table->string('idempotency_key')
                ->unique()
                ->comment('Уникальный ключ для предотвращения дублей сообщений');

            $table->unsignedBigInteger('user_id')
                ->index()
                ->comment('ID пользователя-получателя');

            $table->string('text', 500)
                ->comment('Текст уведомления (ограничение 500 символов)');

            $table->string('channel')
                ->comment('Канал отправки: email, telegram и т.д.');

            $table->string('event_name')
                ->index()
                ->comment('Название события или класса Job для фоновой обработки');

            $table->string('status')
                ->comment('Текущий статус: pending, processing, sent, error');

            $table->integer('attempts')
                ->default(0)
                ->comment('Количество попыток обработки уведомления');

            $table->timestamp('last_attempt_at')
                ->nullable()
                ->comment('Дата и время последней попытки обработки');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
