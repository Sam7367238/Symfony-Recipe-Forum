<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route("/recipe", "recipe_")]
final class RecipeController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/', name: 'index')]
    public function index(Request $request, RecipeRepository $repository, PaginatorInterface $paginator): Response
    {

        $query = $this -> entityManager -> createQuery("SELECT r, u FROM App\Entity\Recipe r INNER JOIN r.user u");

        $recipes = $paginator -> paginate($query, $request -> query -> getInt("page", 1), 2);

        return $this -> render("recipe/index.html.twig", compact("recipes"));
    }

    #[Route('/new', "new")]
    public function new(Request $request, #[CurrentUser] ?User $user): Response {
        $recipe = new Recipe();

        $form = $this -> createForm(RecipeType::class, $recipe);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $recipe -> setUser($user);

            $this -> entityManager -> persist($recipe);
            $this -> entityManager -> flush();

            $this -> addFlash("status", "Recipe Posted Successfully");

            return $this -> redirectToRoute("recipe_index");
        }

        return $this -> render("recipe/new.html.twig", compact("form"));
    }

    #[Route("/{id<\d+>}", "show")]
    public function show(Recipe $recipe, Request $request) {

    }
}
