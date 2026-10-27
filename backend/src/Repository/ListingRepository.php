<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Listing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Listing> */
class ListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    /** Only admin-approved ads are ever visible to the public. @return Listing[] */
    public function findPublic(?string $q = null, ?Category $category = null, ?City $city = null): array
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.status = :s')->setParameter('s', Listing::STATUS_APPROVED)
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults(100);
        if ($q) {
            $qb->andWhere('l.title LIKE :q OR l.description LIKE :q')
               ->setParameter('q', '%'.addcslashes($q, '%_\\').'%');
        }
        if ($category) {
            $qb->andWhere('l.category = :c')->setParameter('c', $category);
        }
        if ($city) {
            $qb->andWhere('l.city = :ci')->setParameter('ci', $city);
        }

        return $qb->getQuery()->getResult();
    }
}
