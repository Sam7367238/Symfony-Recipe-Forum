<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly final class RecipeService extends AbstractService {
    public function __construct(
        EntityManagerInterface $entityManager,
        private RecipeRepository $repository
    ) {
        parent::__construct($entityManager);
    }

    public function findRecipes(User $user, int $page, $paginator): array {
        $publicRecipes = $this-> repository ->findPublicRecipes();
        $userRecipes = $this -> repository->findUserRecipes($user);

        $paginatedPublicRecipes = $paginator->paginate($publicRecipes, $page, 5);
        $paginatedUserRecipes = $paginator->paginate($userRecipes, $page, 5);

        return [$paginatedPublicRecipes, $paginatedUserRecipes];
    }

    public function saveRecipe(Recipe $recipe, User $user): void {
        $recipe->setUser($user);

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();
    }
}
