<?php

namespace App\Tests\Controller;

use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecipeControllerTest extends WebTestCase
{
    public function testAuthorization(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $recipeRepository = static::getContainer()->get(RecipeRepository::class);

        $testUser = $userRepository->findOneBy(['email' => 'email@email.com']);

        $client->loginUser($testUser);

        $client->request('GET', '/');

        $client->clickLink('Recipes');

        $client->clickLink('New Recipe');

        $client->submitForm('recipe[save]', [
            'recipe[title]' => 'Test Recipe',
            'recipe[content]' => 'Test recipe content.',
            'recipe[private]' => true,
        ]);

        $recipe = $recipeRepository->findOneBy(['title' => 'Test Recipe']);

        $maliciousUser = $userRepository->findOneBy(['email' => 'email2@email.com']);

        $client->loginUser($maliciousUser);

        $client->request('GET', "/recipe/{$recipe->getId()}");

        $this->assertResponseStatusCodeSame(403);
    }
}
