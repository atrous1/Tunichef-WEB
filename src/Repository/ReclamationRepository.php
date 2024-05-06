<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;


/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Reclamation $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Reclamation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function findByAvis( $Avis)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.Avis LIKE :Avis')
        ->setParameter('Avis','%' .$Avis. '%')
        ->getQuery()
        ->execute();
}


public function countByStatut($statut)
{
    return $this->createQueryBuilder('e')
        ->select('COUNT(e.id)')
        ->andWhere('e.statut = :statut')
        ->setParameter('statut', $statut)
        ->getQuery()
        ->getSingleScalarResult();
}

public function SortByAvis(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.Avis','ASC')
        ->getQuery()
        ->getResult()
        ;
}
public function updateReclamationStatut(): void
{
    $reclamations = $this->createQueryBuilder('r')
        ->leftJoin('r.idRep', 'Reponse')
        ->getQuery()
        ->getResult();

    foreach ($reclamations as $reclamation) {
        if ($reclamation->getIdRep()->isEmpty()) {
            $reclamation->setStatut('En attente');
        } else {
            $reclamation->setStatut('traitée');
        }
        $this->_em->persist($reclamation);
    }

    $this->_em->flush();
}
public function updateStats(): void
{
    $nbReclamationsTraitees = $this->countByStatut('traitée');
    $nbReclamationsEnAttente = $this->countByStatut('En attente');
    $this->_em->flush();
}

    public function sms()
    {
       
                $sid = 'AC5f30c61a472e288900d2e1fb14d3b5b3';
                $auth_token = '348eab68dd79dd13eb813691337783d5';
                $twilio_number = "+17573472962";
        
                $client = new Client($sid, $auth_token);
                $client->messages->create(
                    '+21620427036',
                    [
                      'from' => '+17573472962',
                       'body' => 'Une reclamation a été ajoutée '
                    ]
                );
                
           
            }


        }