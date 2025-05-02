<?php

namespace App\Services;

use App\Services\AmoCrmService;

class WebhookHandlerService
{
    private $amoCrmService;

    public function __construct(AmoCrmService $amoCrmService)
    {
        $this->amoCrmService = $amoCrmService;
    }

    /**
     * Обработка события добавления сущности.
     *
     * @param string $entityType
     * @param array $data
     */
    public function handleAddEvent(string $entityType, array $data): void
    {
        $name = $data['name'] ?? 'Название не указано';
        $responsibleUserId = $data['responsible_user_id'] ?? 'ID ответственного не указано';
        $createdAt = $data['created_at'] ?? 'Время создания не указано';

        if (is_numeric($createdAt)) {
            $createdAt = date('Y-m-d H:i:s', $createdAt);
        }

        $note = "Создана $entityType: $name\nОтветственный: $responsibleUserId\nВремя создания: $createdAt";

        $this->amoCrmService->addNote($entityType, (int)$data['id'], $note);
    }

    /**
     * Обработка события обновления сущности.
     *
     * @param string $entityType
     * @param array $data
     */
    public function handleUpdateEvent(string $entityType, array $data): void
    {
        $name = $data['name'] ?? 'Название не указано';

        $updatedAt = $data['updated_at'] ?? 'Время обновления не указано';
        if (is_numeric($updatedAt)) {
            $updatedAt = date('Y-m-d H:i:s', $updatedAt);
        }

        $note = "Обновлена $entityType: $name\n";
        $note .= "ID: {$data['id']}\n";
        $note .= "Текущие данные:\n";

        foreach ($data as $field => $value) {
            if (is_array($value)) {
                $note .= "- $field: " . json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
            } else {
                $note .= "- $field: $value\n";
            }
        }

        $note .= "Время изменения: $updatedAt";

        $this->amoCrmService->addNote($entityType, (int)$data['id'], $note);
    }
}
