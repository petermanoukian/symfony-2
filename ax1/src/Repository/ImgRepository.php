<?php

namespace App\Repository;

use App\Entity\Img;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Tools\Pagination\Paginator;





/**
 * @extends ServiceEntityRepository<Img>
 *
 * @method Img|null find($id, $lockMode = null, $lockVersion = null)
 * @method Img|null findOneBy(array $criteria, array $orderBy = null)
 * @method Img[]    findAll()
 * @method Img[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImgRepository extends ServiceEntityRepository
{
  
	



	public function __construct(ManagerRegistry $registry )
    {
        parent::__construct($registry, Img::class);
		 
    }

	
	public function findAllPaginated($page, $limit = 12)
	{
		$query = $this->createQueryBuilder('e')
			->setMaxResults($limit)
			->setFirstResult(($page - 1) * $limit)
			->getQuery();

		$paginator = new Paginator($query, $fetchJoinCollection = true);

		return $paginator;
	}
	

	
	/*
	
		 public function findAllPaginated($query, $page, $limit = 10)
    {
        $paginatedData = $this->paginator->paginate(
            $query,
            $page,
            $limit
        );

        return $paginatedData;
    }
	
	
	
	public function findAllPaginated($page, $limit = 12)
	{
		$query = $this->createQueryBuilder('e')
			->getQuery();

		return $this->paginate($query, $page, $limit);
	}
	
	

	/*
	

//    /**
//     * @return Img[] Returns an array of Img objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Img
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
