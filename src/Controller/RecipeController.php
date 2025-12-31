<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(): Response
    {
        return $this -> render("recipe/index.html.twig");
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
}
