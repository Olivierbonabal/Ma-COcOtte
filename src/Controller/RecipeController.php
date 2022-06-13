<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        PaginatorInterface $paginator,
        RecipeRepository $repository,
        Request $request
    ): Response {

        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return $this->render('pages/recipe/new.html.twig');
    }
}
