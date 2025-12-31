<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('email@email.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'Password'));
        $user->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $recipe = new Recipe();
        $recipe->setTitle('Recipe 1');
        $recipe->setContent("Recipe 1's content.");
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setPrivate(false);
        $recipe->setUser($user);

        $recipe = new Recipe();
        $recipe->setTitle('Recipe 2');
        $recipe->setContent("Recipe 2's content.");
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setPrivate(true);
        $recipe->setUser($user);

        $manager->persist($recipe);

        $manager->flush();
    }
}
