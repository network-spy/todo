<?php

namespace App\DTO;

use App\Entity\Task;

/**
 * Class TaskDTO
 * @package App\DTO
 */
class TaskDTO
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var null|string
     */
    private $content;

    /**
     * @var \DateTimeInterface|null
     */
    private $createdAt;

    /**
     * @var bool|null
     */
    private $completed;

    /**
     * @var int|null
     */
    private $userId;

    /**
     * TaskDTO constructor.
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->id = $task->getId();
        $this->content = $task->getContent();
        $this->createdAt = $task->getCreatedAt();
        $this->completed = $task->getCompleted();
        $this->userId = $task->getUser()->getId();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
