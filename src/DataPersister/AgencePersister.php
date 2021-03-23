<?php
namespace App\DataPersister;


use App\Entity\Agence;
use App\Repository\UserRepository;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
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
             //dd($data);
            if($data->getCompte()) {
                
                if($data->getCompte()->getSolde() > 700000) {
                       // get Id last agence from db
                    $Idlastagence= $this->agenceRepository->findOneBy(array(), array('id' => 'desc'))->getId();
                    $data->getCompte()->setAgence($data);
                    // $id = 1;
                    //dd($id);
                    //dd($data);
                    // dd($data->getComptes()[0]->getSolde());
                     $this->entityManager->persist($data);
                     $this->entityManager->flush();
                     return new JsonResponse("success",201) ;
                }
                return new JsonResponse("Le compte doit avoir au minimum 700.000",400) ;
            }
            return new JsonResponse("Une agence doit obligatoirement avoir un compte",400) ;
         
            dd($data->getId());  
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return new JsonResponse("success",201) ;      
    
    }

    public function remove($data, array $context = [])
    {
        $id = $data->getId() ;
        // dd($id);
        $agenceBlocked = $this->agenceRepository->find($id);
        $agenceBlocked->setDisabled(1);
        //  dd($agenceBlocked);
        $this->entityManager->persist($data);

        // block ccounts 
        $comptesAgences = $this->compteRepository->findBy(['agence'=>$id]);

        foreach ($comptesAgences as $value) {
            $value->setDisabled(1);
            $this->entityManager->persist($value);
        }

        //block users
        $users = $this->userRepository->findBy(['agence'=>$id]);
        // dd($users);
        foreach ($users as $value) {
            $value->setArchivage(1);
            $this->entityManager->persist($value);     
        }

        $this->entityManager->flush();
        return new JsonResponse("This agence has been blocked with success",200) ;  
        
    }
}