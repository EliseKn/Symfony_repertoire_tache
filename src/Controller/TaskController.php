<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task")
     */
    public function index(Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();

        $task = new Task();
        $task->setDeadline(new \DateTime('now'));

        $formTask = $this->createForm(TaskType::class, $task);

        $formTask->handleRequest($request);
        if ( $formTask->isSubmitted() && $formTask->isValid() ) {
            $task->setState(false);
            $pdo->persist($task);
            $pdo->flush();

            $this->addFlash("success", "Tâche ajoutée !");
        }

        $tasks = $pdo->getRepository(Task::class)->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'form_task_add' => $formTask->createView()
        ]);
    }

    /**
     * PAGE TACHE
     * @Route("/task/{id}", name="my_task")
     */

     public function task(Request $request, Task $task=null){

        if ($task !=null) {
            //si la tache existe 

            $formTask = $this->createForm(TaskType::class, $task);
            $formTask->handleRequest($request);

            if ( $formTask->isSubmitted() && $formTask->isValid() ) {
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($task);
                $pdo->flush();

                $this->addFlash("success", "Tâche modifiée !");
            }

            return $this->render('task/task.html.twig', [
                'task' => $task,
                'form_task_edit' => $formTask->createView()
            ]);
        }
        else {
            //la tache n'existe pas 
            $this->addFlash("danger", "Tâche introuvable !");
            return $this->redirectToRoute('task');
        }
    }

    /**
     * PAGE SUPRESSION
     * @Route("/task/delete/{id}", name="delete_task")
     */

    public function deleteTask(Request $request, Task $task=null) {
        if($task != null) {
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($task); 
            $pdo->flush();

            $this->addFlash("success", "Tâche supprimée !");
        }

        return $this->redirectToRoute('task');
    }

    /**
     * @Route("/task/ok/{id}", name="ok_task")
     */
    public function ok_task(Request $request, Task $task = null)
    {
        if ($task != null) {
            $task->setState(true);
            $pdo = $this->getDoctrine()->getManager();
            $pdo->persist($task);
            $pdo->flush();

            $this->addFlash("success", "Tâche validée !");
        } 
        else {
            $this->addFlash("danger", "Tâche introuvable ");
        }

        return $this->redirectToRoute('task');
    }
}
