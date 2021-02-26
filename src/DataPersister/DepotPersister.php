<?php
namespace App\DataPersister;

use App\Entity\Depot;
use App\Repository\UserRepository;
use App\Repository\DepotRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommissionsRepository;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class DepotPersister implements ContextAwareDataPersisterInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, DepotRepository $depotRepository, CompteRepository $compteRepository, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->depotRepository = $depotRepository;
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository ;
    }
    
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Depot;
    }

    public function persist($data, array $context = [])
    {
        // cumul sum compte after depot
      $data->getComptes()->setSolde($data->getComptes()->getSolde() + $data->getMontantDeDepot());
       
      $this->entityManager->persist($data);
      $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        $id = $data->getId() ;
        $depotDeleted = $this->depotRepository->findById($id) ;
        // dd($depotDeleted); 
        $data->setArchivage(1) ;
        $this->entityManager->persist($data);
        $this->entityManager->flush();
       // call your persistence layer to delete $data
    }
}