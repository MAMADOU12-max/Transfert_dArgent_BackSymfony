<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Repository\UserRepository;
use App\Controller\DepotController;
use App\Repository\DepotRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DepotController extends AbstractController {
    

    public function __construct(SerializerInterface $serializer, DepotRepository $depotRepository,
    EntityManagerInterface $manager, CompteRepository $compteRepository, UserRepository $userRepository )
    {
        $this->serialize = $serializer ;
        $this->manager = $manager ;
        $this->depotRepository = $depotRepository ;
        $this->userRepository = $userRepository;
        $this->compteRepository = $compteRepository;
    }

    // ----------------------------------------------------- ADD DEPOT -------------------------------------------------------------------//

    /**
     * @Route(
     *      name="depot" ,
     *      path="/api/depot" ,
     *      methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\DepotController::depot",
     *         "_api_resource_class"=Depot::class ,
     *         "_api_collection_operation_name"="depot"
     *     }
     *)
    */

    public function depot( Request $request) {
      
        //all data from postman
        $dataPostman =  json_decode($request->getContent());
        // dd($dataPostman);

         $montant = $dataPostman->montantDeDepot ; //get montant
         $utilisateur = $dataPostman->utilisateur ; //get utilisateur

         // Validate negatif number 
        if($montant < 0) {
           // return new JsonResponse("Can be negative number!" ,400) ; 
            return $this->json("le montant ne peut pas être négatif!",400);
        } 
        
        $newDepot = new Depot(); //Instancier Depot

        $newDepot->setMontantDeDepot($dataPostman->montantDeDepot);
        $newDepot->setCaissiers($this->userRepository->findOneBy(['id'=>(int)$utilisateur]));
        //get id agence of utilisateur
        $idAgence = $this->userRepository->findOneBy(['id'=>(int)$utilisateur])->getAgence()->getId();
        $focusCompte = $this->compteRepository->findBy(['agence'=>$idAgence]); //reper account
    
        $newDepot->setComptes($focusCompte[0]);
        $this->manager->persist($newDepot);
        
        $focusCompte[0]->setSolde($focusCompte[0]->getSolde() + $montant);
        // dd($focusCompte);

        $this->manager->persist($focusCompte[0]);
        $this->manager->flush();
        return $this->json("Votre dépôt a été fait avec success!",201);

    } 

    // ------------------------------------------------- FIN ADD DEPOT -------------------------------------------------------------------//



    // ------------------------------------------------- ANNULER DEPOT -------------------------------------------------------------------//

    /**
     * @Route(
     *    name="annulerDepot" ,
     *    path="/api/depot/annuler" ,
     *    methods={"PUT"} ,
     *    defaults={
     *         "__controller"="App\Controller\DepotController::annulerDepot",
     *         "_api_resource_class"=Depot::class ,
     *         "_api_collection_operation_name"="annulerDepot"
     *    }
     *)
    */

    public function annulerDepot( Request $request) {
    
        //all data from postman
        $dataPostman =  json_decode($request->getContent());
        $utilisateur = $dataPostman->utilisateur ; 

        // reper last depot user
        $lastDepotfromUser = $this->depotRepository->findOneBy(['caissiers'=>$utilisateur], array('id' => 'desc'));     
         // get last depot on db
        $lastDepotfromBd= $this->depotRepository->findOneBy(array(), array('id' => 'desc'));
       

        if($lastDepotfromUser == $lastDepotfromBd) {
            
            // verify action's date
            // $date = new \DateTime();         
            // if($date->format('Y-m-d') > $lastDepotfromUser->getDateDepot()->format('Y-m-d')) {
            //     return $this->json('Vous ne pouvez plus éffectuer annuler le depôt car le delaie d\'action est expiré!',400);
            // }
            

            // find account for retire money
            $focusCompte = $this->compteRepository->findById($lastDepotfromUser->getComptes()->getId())  ;
           
            $lastMontant = $lastDepotfromUser->getMontantDeDepot();
            if($lastMontant > $focusCompte[0]->getSolde()) {
                return $this->json('Desolé, le compte est insuffisant pour exercer cette action. Le solde est de '.$focusCompte[0]->getSolde().'.',400);
            }

            //refactor account
            $focusCompte[0]->setSolde($focusCompte[0]->getSolde() - $lastMontant);
            $this->manager->persist($focusCompte[0]);
            

            $findIt = $this->depotRepository->findById($lastDepotfromUser->getId()); // find depot
            // dd($findIt[0]);
             $this->manager->remove($findIt[0]);
             $this->manager->flush();
            return $this->json("Le dêpot est bien annulé!!",200);
        } else {
            return $this->json("Desolé, vous ne pouvez plus annuler votre dêpot car un autre dêpot entre temps!",400);
        }
        
    }

    // ---------------------------------------------FIN ANNULER DEPOT -------------------------------------------------------------------//

}
