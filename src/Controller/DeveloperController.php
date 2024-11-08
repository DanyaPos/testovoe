<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Entity\Project;
use App\Form\DeveloperType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DeveloperController extends AbstractController
{
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $developer = new Developer();

        $projects = $entityManager->getRepository(Project::class)->findAll();

        $form = $this->createForm(DeveloperType::class, $developer, [
            'project' => $projects,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($developer);
            $entityManager->flush();

            return $this->redirectToRoute('developer_list');
        }

        return $this->render('developer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/developer/{id}/transfer', name: 'developer_transfer', methods: ['GET', 'POST'])]
    public function transfer(Developer $developer, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получаем все проекты для перевода
        $projects = $entityManager->getRepository(Project::class)->findAll();

        if ($request->isMethod('POST')) {
            // Находим новый проект из переданных данных
            $newProject = $entityManager->getRepository(Project::class)->find($request->request->get('project'));

            if ($newProject && $newProject !== $developer->getProject()) {
                $developer->setProject($newProject); // Переводим на новый проект
                $entityManager->flush(); // Сохраняем изменения в базе данных

                return $this->redirectToRoute('developer_list'); // Перенаправляем на список
            }
        }

        return $this->render('developer/transfer.html.twig', [
            'developer' => $developer,
            'projects' => $projects,
        ]);
    }

    #[Route('/developer/{id}/delete', name: 'developer_delete', methods: ['POST'])]
    public function delete(Developer $developer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($developer); // Удаляем разработчика
        $entityManager->flush(); // Сохраняем изменения в базе данных

        return $this->redirectToRoute('developer_list'); // Перенаправляем на список разработчиков
    }

}
