<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user -> setEmail("email@email.com");
        $user -> setPassword($this -> passwordHasher -> hashPassword($user, "Password"));
        $user -> setRoles(["ROLE_USER"]);
        
        $manager -> persist($user);

        $manager -> flush();
    }
}
