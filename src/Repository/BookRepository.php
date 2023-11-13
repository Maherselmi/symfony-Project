<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    //Query Builder: Question 2
    public function showAllBooksByRef($ref)
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->where('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }

    //Query Builder: Question 3
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //Query Builder: Question 4
    public function showBooksByDateAndNbBooks($nbooks, $year)
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->where('a.nb_books > :nbooks')
            ->andWhere("b.publicationDate < :year")
            ->setParameter('nbooks', $nbooks)
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    //Query Builder: Question 5
    public function updateBooksCategoryByAuthor($c)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->update('App\Entity\Book', 'b')

            ->set('b.category', '?1')
            ->setParameter(1, 'Romance')
            ->where('b.category LIKE ?2')
            ->setParameter(2, $c)
            ->getQuery()
            ->getResult();
    }

    // DQL
    //Question 1
    function NbBookCategory()
    {
        $em = $this->getEntityManager();
        return $em->createQuery('select count(b) from App\Entity\Book b WHERE b.category=:category')
            ->setParameter('category', 'Romance')->getSingleScalarResult();
    }
    //Question 2
    function findBookByPublicationDate()
    {
        $em = $this->getEntityManager();
        return $em->createQuery('select b from App\Entity\Book b WHERE 
    b.publicationDate BETWEEN ?1 AND ?2')
            ->setParameter(1, '2014-01-01')
            ->setParameter(2, '2018-01-01')->getResult();
    }
    function UpdateQB()
    {
        return $this->createQueryBuilder('b')
            ->update()->set('b.category', '?1')->setParameter(1, 'Romance')
            ->where('b.category=?2')->setParameter(2, 'Science Fiction ')
            ->getQuery()->getResult();
    }
}
