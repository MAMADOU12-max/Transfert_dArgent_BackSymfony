<?php
namespace App\DataPersister;


use App\Entity\Agence;
use App\Repository\UserRepository;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\DataPersister\AgencePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class AgencePersister implements ContextAwareDataPersisterInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager, AgenceRepository $agenceRepository, UserRepository $userRepository, CompteRepository $compteRepository)
    {
        $this->entityManager = $entityManager;
        $this->agenceRepository = $agenceRepository;
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository ;
    }
    
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Agence;
    }

    public function persist($data, array $context = [])
    {
        // if($context["collection_operation_name"]==="POST"){}
         
            if($data->getComptes()[0]) {
               
                if($data->getComptes()[0]->getSolde() > 700000) {
                    // dd($data->getComptes()[0]->getSolde());
                     $this->entityManager->persist($data);
                     $this->entityManager->flush();
                     return new JsonResponse("success",201) ;
                }
                return new JsonResponse("the balance must be greater than 700,000",400) ;
            }
            return new JsonResponse("You must add at least one compte",400) ;         
    
    }

    public function remove($data, array $context = [])
    {
        $id = $data->getId() ;
        // dd($id);
        $agenceBlocked = $this->agenceRepository->find($id);
        $agenceBlocked->setDisabled(1);
        //  dd($agenceBlocked);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // block ccounts 
        $comptesAgences = $this->compteRepository->findBy(['agence'=>$id]);

        foreach ($comptesAgences as $value) {
            $value->setDisabled(1);
            $this->entityManager->persist($value);
            $this->entityManager->flush();
        }

        //block users
        $users = $this->userRepository->findBy(['agence'=>$id]);
        // dd($users);
        foreach ($users as $value) {
            $value->setArchivage(1);
            $this->entityManager->persist($value);
            $this->entityManager->flush();
        }

        return new JsonResponse("This agence has been blocked with success",200) ;  
        
    }
}