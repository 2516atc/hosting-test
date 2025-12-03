<?php

namespace App\Repository;

use App\Entity\DeployConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeployConfiguration>
 */
class DeployConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeployConfiguration::class);
    }

    public function findOneByGitRepository(string $owner, string $name): ?DeployConfiguration
    {
        return $this->createQueryBuilder('config')
            ->andWhere('config.repositoryOwner = :owner')
            ->andWhere('config.repositoryName = :name')
            ->setParameter('owner', $owner)
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
