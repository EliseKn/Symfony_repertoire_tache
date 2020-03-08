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
        $formTask = $this->createForm(TaskType::class, $task);

        $formTask->handleRequest($request);
        if ( $formTask->isSubmitted() && $formTask->isValid() ) {

            $pdo->persist($task);
            $pdo->flush();
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
            }

            return $this->render('task/task.html.twig', [
                'task' => $task,
                'form_task_edit' => $formTask->createView()
            ]);
        }
        else {
            //la tache n'existe pas 
            return $this->redirectToRoute('task');
        }
    }

    /**
     * PAGE SUPRESSION
     * @Route("/task/delete/{id}", name="delete_task")
     */

    public function deleteTask(Task $task=null) {
        if($task != null) {
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($task); 
            $pdo->flush();
        }

        return $this->redirectToRoute('task');
    }
}
