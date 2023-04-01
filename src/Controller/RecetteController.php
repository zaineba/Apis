<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/recette')]
class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository): JsonResponse
    {
        $recettes = $recetteRepository->findAll();

        return $this->json(['recettes' => $recettes]);
    }

    #[Route('/new', name: 'app_recette_new', methods: ['POST'])]
    public function new(Request $request, RecetteRepository $recetteRepository, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('add_recette', $submittedToken))) {
            return $this->json(['error' => 'Invalid CSRF token'], Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $recetteRepository->save($recette, true);

            return $this->json(['recette' => $recette]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }



    #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): JsonResponse
    {
        return $this->json(['recette' => $recette]);
    }

    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['POST'])]
    public function edit(Request $request, Recette $recette, RecetteRepository $recetteRepository): JsonResponse
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recetteRepository->save($recette, true);

            return $this->json(['recette' => $recette]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_recette_delete', methods: ['DELETE'])]
    public function delete(Request $request, Recette $recette, RecetteRepository $recetteRepository): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {
            $recetteRepository->remove($recette, true);

            return $this->json([], Response::HTTP_NO_CONTENT);
        }

        return $this->json(['error' => 'Invalid CSRF token'], Response::HTTP_BAD_REQUEST);
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $errors;
    }
}
