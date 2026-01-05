<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Service\RecipeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/recipe', 'recipe_')]
final class RecipeController extends AbstractController
{
    public function __construct(
        private RecipeService $recipeService,
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, #[CurrentUser] ?User $user, RecipeRepository $repository, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);

        $recipes = $this -> recipeService -> findRecipes(user: $user, page: $page, paginator: $paginator);

        [$paginatedPublicRecipes, $paginatedUserRecipes] = $recipes;

        return $this->render('recipe/index.html.twig', compact('paginatedPublicRecipes', 'paginatedUserRecipes'));
    }

    #[Route('/new', 'new')]
    public function new(Request $request, #[CurrentUser] ?User $user): Response
    {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this -> recipeService -> saveRecipe($recipe, $user);

            $this->addFlash('status', 'Recipe Posted Successfully');

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('recipe/new.html.twig', compact('form'));
    }

    #[Route("/{id<\d+>}", 'show')]
    #[IsGranted('RECIPE_MANAGE', 'recipe')]
    public function show(Recipe $recipe, Request $request): Response
    {
        return $this->render('recipe/show.html.twig', compact('recipe'));
    }

    #[Route('/{id<\d+>}/edit', 'edit')]
    #[IsGranted('RECIPE_MANAGE', 'recipe')]
    public function edit(Request $request, #[CurrentUser] ?User $user, Recipe $recipe): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this -> recipeService -> flush();

            $this->addFlash('status', 'Recipe Edited Successfully');

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/edit.html.twig', compact('form'));
    }

    #[Route("/{id<\d+>}/delete", 'delete')]
    #[IsGranted('RECIPE_MANAGE', 'recipe')]
    public function delete(Request $request, Recipe $recipe): Response
    {
        if ($request->isMethod('POST')) {
            $this -> recipeService -> remove($recipe);

            $this->addFlash('status', 'Recipe Deleted Successfully');

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('recipe/delete.html.twig', ['id' => $recipe->getId()]);
    }
}
