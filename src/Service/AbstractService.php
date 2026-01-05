<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

abstract readonly class AbstractService
{
    public function __construct(protected EntityManagerInterface $entityManager) {}

    public function flush(): void {
        $this -> entityManager -> flush();
    }

    public function remove(object $entity): void {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
