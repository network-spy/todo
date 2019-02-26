<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\TaskDTO;
use App\Form\TaskType;
use App\Transformers\Task\TaskCollectionToDtoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TaskRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class TaskController
 * @package App\Controller
 * @RouteResource("Task", pluralize=false)
 */
class TaskController extends AbstractController implements ClassResourceInterface
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
     * List of user's tasks
     * @SWG\Response(
     *     response=200,
     *     description="Returns list of user's tasks",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskDTO::class))
     *     )
     * )
     * @return \FOS\RestBundle\View\View
     */
    public function cgetAction()
    {
        $tasks = $this->taskRepository->findAll();
        $transformer = new TaskCollectionToDtoTransformer();

        return $this->json(
            $transformer->transform($tasks),
            Response::HTTP_OK
        );
    }

    /**
     * Single user's task
     * @SWG\Response(
     *     response=200,
     *     description="Returns user's task",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskDTO::class))
     *     )
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="User's task id"
     * )
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function getAction(int $id)
    {
        return $this->json(
            new TaskDTO(
                $this->findTaskById($id)
            ),
            Response::HTTP_OK
        );
    }

    /**
     * Create user's task
     * @SWG\Response(
     *     response=201,
     *     description="Returns created user's task",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskDTO::class))
     *     )
     * )
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
        $task = $form->getData();
        $task->setUser($this->getUser());
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->json(
            new TaskDTO($task),
            Response::HTTP_CREATED
        );
    }

    /**
     * Fully update existing user's task or create new one (if task with provided id not found)
     * @SWG\Response(
     *     response=201,
     *     description="Returns created user's task",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskDTO::class))
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns updated user's task",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskDTO::class))
     *     )
     * )
     * @param Request $request
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function putAction(Request $request, int $id)
    {
        $statusCode = Response::HTTP_OK;
        try {
            $task = $this->findTaskById($id);
            $this->compareWithCurrentUserId($task->getUser()->getId());
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
        $task = $form->getData();
        if (!$task->getId()) {
            $task->setUser($this->getUser());
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();

        return $this->json(
            new TaskDTO($task),
            $statusCode
        );
    }

    /**
     * Partially update existing user's task by id
     * @SWG\Response(
     *     response=201,
     *     description="Returns updated user's task",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TaskDTO::class))
     *     )
     * )
     * @param Request $request
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function patchAction(Request $request, int $id)
    {
        $task = $this->findTaskById($id);
        $this->compareWithCurrentUserId($task->getUser()->getId());
        $form = $this->createForm(TaskType::class, $task);
        $form->submit(
            $request->request->all(),
            false
        );
        if (false === $form->isValid()) {
            return $this->view($form);
        }
        $task = $form->getData();
        $this->entityManager->flush();

        return $this->json(
            new TaskDTO($task),
            Response::HTTP_OK
        );
    }

    /**
     * Delete user's task by id
     * @SWG\Response(
     *     response=204,
     *     description="Delete user's task",
     * )
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(int $id)
    {
        $task = $this->findTaskById($id);
        $this->compareWithCurrentUserId($task->getUser()->getId());
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @return Task|null
     */
    private function findTaskById(int $id): ?Task
    {
        $task = $this->taskRepository->find($id);
        if (null === $task) {
            throw new NotFoundHttpException();
        }

        return $task;
    }

    /**
     * @param int $id
     */
    private function compareWithCurrentUserId(int $id)
    {
        if  (((int) $this->getUser()->getId()) === $id) {
            return;
        }

        throw new AccessDeniedHttpException();
    }
}
