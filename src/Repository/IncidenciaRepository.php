<?php

namespace App\Repository;

use App\Entity\Incidencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Incidencia>
 */
class IncidenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incidencia::class);
    }




    //    /**
    //     * @return Incidencia[] Returns an array of Incidencia objects
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

    //    public function findOneBySomeField($value): ?Incidencia
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findDelAdministrador($administrador): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.zona', 'z')
            ->join('z.comunidad', 'c')
            ->andWhere('c.administrador = :administrador')
            ->setParameter('administrador', $administrador)
            ->orderBy('i.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findDeComunidad($comunidad): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.zona', 'z')
            ->andWhere('z.comunidad = :comunidad')
            ->setParameter('comunidad', $comunidad)
            ->orderBy('i.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
