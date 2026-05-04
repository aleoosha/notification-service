<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id()->comment('Системный инкрементальный ID');

            $table->uuid('uuid')
                ->unique()
                ->index()
                ->comment('Публичный идентификатор для внешних API запросов');

            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->index()
                ->comment('Владелец отчета. Nullable для сохранения истории после удаления юзера');

            $table->string('status')
                ->index()
                ->comment('Статус генерации: pending, completed, failed');

            $table->string('file_path')
                ->nullable()
                ->comment('Относительный путь к файлу в хранилище storage/app');

            $table->string('event_name')
                ->comment('Полное имя класса события для Relay-процесса');

            $table->integer('attempts')
                ->default(0)
                ->comment('Счетчик попыток обработки записи фоновым релеем');

            $table->timestamp('last_attempt_at')
                ->nullable()
                ->comment('Метка времени последней активности Relay-процесса');

            $table->timestamp('requested_at')
                ->comment('Время получения запроса от пользователя');

            $table->timestamp('completed_at')
                ->nullable()
                ->comment('Время фактического создания файла на диске');

            $table->timestamps();

            $table->timestamp('start_date')->nullable()->comment('Начало периода отчета');
            $table->timestamp('end_date')->nullable()->comment('Конец периода отчета');

            $table->softDeletes()
                ->comment('Метка мягкого удаления записи');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
