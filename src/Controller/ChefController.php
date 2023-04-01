<?php

namespace App\Controller;

use App\Entity\Chef;
use App\Form\ChefType;
use App\Repository\ChefRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chef')]
class ChefController extends AbstractController
{
    #[Route('/', name: 'app_chef_index', methods: ['GET'])]
    public function index(ChefRepository $chefRepository): Response
    {
        return $this->render('chef/index.html.twig', [
            'chefs' => $chefRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chef_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ChefRepository $chefRepository): Response
    {
        $chef = new Chef();
        $form = $this->createForm(ChefType::class, $chef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chefRepository->save($chef, true);

            return $this->redirectToRoute('app_chef_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chef/new.html.twig', [
            'chef' => $chef,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chef_show', methods: ['GET'])]
    public function show(Chef $chef): Response
    {
        return $this->render('chef/show.html.twig', [
            'chef' => $chef,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chef_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chef $chef, ChefRepository $chefRepository): Response
    {
        $form = $this->createForm(ChefType::class, $chef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chefRepository->save($chef, true);

            return $this->redirectToRoute('app_chef_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chef/edit.html.twig', [
            'chef' => $chef,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chef_delete', methods: ['POST'])]
    public function delete(Request $request, Chef $chef, ChefRepository $chefRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chef->getId(), $request->request->get('_token'))) {
            $chefRepository->remove($chef, true);
        }

        return $this->redirectToRoute('app_chef_index', [], Response::HTTP_SEE_OTHER);
    }
}
