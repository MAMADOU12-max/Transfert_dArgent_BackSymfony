<?php
namespace App\DataPersister;

use App\Entity\Commissions;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommissionsRepository;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class CommissionsPersister implements ContextAwareDataPersisterInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, CommissionsRepository $commissionsRepository)
    {
        $this->entityManager = $entityManager;
        $this->commissionsRepository = $commissionsRepository;
    }
    
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Commissions;
    }

    public function persist($data, array $context = [])
    {
         // call your persistence layer to save $data
        //  $data->setLibelle($data->getLibelle()) ;
        //  $user = $this->manager->persist($data) ;
        //  $this->manager->flush($user);
        //  return $data;
         $this->entityManager->persist($data);
         $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        $id = $data->getId() ;
        // id profil users 
        $profilDeleted = $this->commissionsRepository->findById($id) ;
        // dd($profilDeleted); 
        $data->setArchivage(1) ;
        $this->entityManager->persist($data);
        $this->entityManager->flush();
       // call your persistence layer to delete $data
    }
}