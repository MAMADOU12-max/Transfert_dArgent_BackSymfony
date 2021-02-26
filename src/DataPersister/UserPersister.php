<?php
namespace App\DataPersister;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\DepotRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommissionsRepository;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class UserPersister implements ContextAwareDataPersisterInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }
    
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
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
        $userDeleted = $this->userRepository->findById($id) ;
        // dd($userDeleted); 
        $data->setArchivage(1) ;
        $this->entityManager->persist($data);
        $this->entityManager->flush();
       // call your persistence layer to delete $data
    }
}