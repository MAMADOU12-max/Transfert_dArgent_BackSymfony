<?php
namespace App\DataPersister;

use App\Entity\User;
use App\Entity\Compte;
use App\Repository\UserRepository;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommissionsRepository;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class ComptePersister implements ContextAwareDataPersisterInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, CompteRepository $compteRepository, UserRepository $userRepository, AgenceRepository $agenceRepository)
    {
        $this->entityManager = $entityManager;
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->agenceRepository = $agenceRepository;
    }
    
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
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

        // get id compte
        $id = $data->getId() ;
        $compteBlock = $this->compteRepository->findById($id) ;
        // dd($compteBlock[0]->getDisabled());
        // if($compteBlock[0]->getDisabled() == true) {
        //     return "Compte déjà bloqué!!";
        // }
        $compteBlock[0]->setDisabled(1);
        $block =  $this->entityManager->persist($compteBlock[0]);
       $this->entityManager->flush($block);
     
        // id agence of compte
        $idAgenceOfCompte =  $compteBlock[0]->getAgence()->getId();
        $usersofThisCompte = $this->userRepository->findBy(['agence'=>$idAgenceOfCompte]);
        // dd($usersofThisCompte);

        // browser and archive user
        foreach($usersofThisCompte as $value) {
             // dd($value->setArchivage(1));
            //  dd($value->setArchivage(1));
            // dd($value->getId(), $value->getArchivage());
            // if($value->getArchivage()){
            //     dd('cool');
            // }
            //     dd($value);
          
             $value->setArchivage(1);
            //  dd($user);
             $this->entityManager->persist($value);
              $this->entityManager->flush();
        }
       
        // dd($depotDeleted); 
       // $data->setArchivage(1) ;
        // $this->entityManager->persist($data);
       
       // call your persistence layer to delete $data
    }
} 