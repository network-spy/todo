<?php

namespace App\Transformers;

use App\DTO\TaskDTO;

/**
 * Class TaskCollectionToDtoTransformer
 * @package App\Transformers
 */
class TaskCollectionToDtoTransformer
{
    /**
     * @param iterable $tasks
     * @return iterable
     */
    public function transform(iterable $tasks): iterable
    {
        $tasksDTO = [];
        foreach ($tasks as $task) {
            $tasksDTO[] = new TaskDTO($task);
        }

        return $tasksDTO;
    }
}
