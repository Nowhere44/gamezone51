<?php

namespace App\Repository;

use App\Entity\GameLauncher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameLauncher>
 *
 * @method GameLauncher|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameLauncher|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameLauncher[]    findAll()
 * @method GameLauncher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameLauncherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameLauncher::class);
    }

    public function save(GameLauncher $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameLauncher $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function findAllOrderById(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'desc');

        $query = $qb->getQuery();

        return $query->execute();
    }


    public function findAllOrderByPrice(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.price', 'desc');

        $query = $qb->getQuery();

        return $query->execute();
    }




    public function findAllIdentiqueThing($id, $type): array
    {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\GameLauncher as p
            WHERE
                    (p.id != :id)
                and (p.TypeOfGame = :TypeOfGame)
            ORDER BY p.price ASC'
        )->setParameter('id', $id)
            ->setParameter('TypeOfGame', $type);

        return $query->execute();
    }



    public function Search($title): array
    {
        $title = '%' . $title . '%';
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\GameLauncher as p
            WHERE (UPPER(p.title) like UPPER(:title))'
        )->setParameter('title', $title);

        return $query->execute();
    }




    //    /**
    //     * @return GameLauncher[] Returns an array of GameLauncher objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GameLauncher
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
