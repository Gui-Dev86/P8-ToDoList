<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{

    /**
     * @Route("/tasks", name="task_list_todo")
     * 
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     * 
     */
    public function listActionToDo(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => false])]);
    }

    /**
     * @Route("/tasks/done", name="task_list_done")
     * 
     * @param TaskRepository $taskRepository
     * 
     * @return Response
     * 
     */
    public function listActionDone(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => true])]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     * 
     */
    public function createAction(Request $request, EntityManagerInterface $manager)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $createdAt = new \DateTime();

            $task->setCreatedAt($createdAt)
                ->setIsDone(false)
                ->setUser($this->getUser());

            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * 
     * @param Task $task
     * @param Request $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function editAction(Task $task, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list_todo');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     * 
     * @param Task $task
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function toggleTaskAction(Task $task, EntityManagerInterface $manager)
    {
        $task->toggle(!$task->isDone());
        $manager->flush();

        if($task->isDone() === true) {
            $this->addFlash('success', sprintf('La tâche %s a bien été envoyée dans les tâches terminées.', $task->getTitle()));

            return $this->redirectToRoute('task_list_todo');
        }
        $this->addFlash('success', sprintf('La tâche %s a bien été envoyée dans les tâches à faire.', $task->getTitle()));

        return $this->redirectToRoute('task_list_done');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * 
     * @param Task $task
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function deleteTaskAction(Task $task, EntityManagerInterface $manager)
    {

        $user = $this->getUser();
        $userRole = $user->getRoles();
        //verify if the user is admin and task anonymous or if the user is the task owner
        if ($task->getUser() == $this->getUser() || $task->getUser() === null && $userRole[0] == "ROLE_ADMIN") {
                
            $manager->remove($task);
            $manager->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été supprimée.', $task->getTitle()));

            if($task->isDone() === true) {
                    return $this->redirectToRoute('task_list_done');
                }
            return $this->redirectToRoute('task_list_todo');
        }

        throw $this->createAccessDeniedException(); 
    }
}
