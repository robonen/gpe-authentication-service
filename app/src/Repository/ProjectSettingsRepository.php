<?php

namespace App\Repository;

use App\Casts\ProjectSettingsMapper;
use App\Entity\ProjectSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectSettings>
 *
 * @method ProjectSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectSettings[]    findAll()
 * @method ProjectSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProjectSettingsRepository extends ServiceEntityRepository
{
    private ProjectSettingsMapper $mapper;

    public function __construct(ManagerRegistry $registry, ProjectSettingsMapper $mapper)
    {
        parent::__construct($registry, ProjectSettings::class);
        $this->mapper = $mapper;
    }

    public function save(ProjectSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProjectSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ProjectSettings[] Returns an array of ProjectSettings objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProjectSettings
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
