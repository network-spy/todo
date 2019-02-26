<?php

namespace App\Transformers\Task;

use App\DTO\TaskDTO;

/**
 * Class TaskCollectionToDtoTransformer
 * @package App\Transformers
 */
class TaskCollectionToDtoTransformer
{
    /**
     * @param array $tasks
     * @return array
     */
    public function transform(array $tasks): array
    {
        $tasksDTO = [];
        foreach ($tasks as $task) {
            $tasksDTO[] = new TaskDTO($task);
        }

        return $tasksDTO;
    }
}
