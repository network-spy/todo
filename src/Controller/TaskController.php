<?php

namespace App\Controller;

use App\DTO\TaskDTO;
use App\Form\TaskType;
use App\Transformers\TaskCollectionToDtoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TaskRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TaskController
 * @package App\Controller
 * @RouteResource("Task", pluralize=false)
 */
class TaskController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * TaskController constructor.
     * @param EntityManagerInterface $entityManager
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function cgetAction()
    {
        $tasks = $this->taskRepository->findAll();
        $transformer = new TaskCollectionToDtoTransformer();

        return $this->view(
            $transformer->transform($tasks)
        );
    }

    /**
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(int $id)
    {
        return $this->view(
            new TaskDTO(
                $this->findTaskById($id)
            )
        );
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(TaskType::class, new Task);
        $form->submit(
            $request->request->all()
        );
        if (false === $form->isValid()) {
            return $this->view($form);
        }
        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function putAction(Request $request, int $id)
    {
        $statusCode = Response::HTTP_OK;
        try {
            $task = $this->findTaskById($id);
        } catch (NotFoundHttpException $e) {
            $task = new Task();
            $statusCode = Response::HTTP_CREATED;
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->submit(
            $request->request->all()
        );
        if (false === $form->isValid()) {
            return $this->view($form);
        }
        $this->entityManager->flush();

        return $this->view(null, $statusCode);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function patchAction(Request $request, int $id)
    {
        $task = $this->findTaskById($id);
        $form = $this->createForm(TaskType::class, $task);
        $form->submit(
            $request->request->all(),
            false
        );
        if (false === $form->isValid()) {
            return $this->view($form);
        }
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(int $id)
    {
        $task = $this->findTaskById($id);
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $id
     * @return Task|null
     */
    private function findTaskById($id): ?Task
    {
        $task = $this->taskRepository->find($id);
        if (null === $task) {
            throw new NotFoundHttpException();
        }

        return $task;
    }
}
