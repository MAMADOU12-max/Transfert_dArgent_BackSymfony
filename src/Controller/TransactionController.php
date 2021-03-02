<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\Commissions;
use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Entity\SummarizeTransaction;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use App\Repository\TarifsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommissionsRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer, CompteRepository $compteRepository, 
                                EntityManagerInterface $manager, ValidatorInterface $validator, TransactionRepository $transactionRepository,
                                TarifsRepository $tarifsRepository, ClientRepository $clientRepository, CommissionsRepository $commissionsRepository) {

        $this->userRepository = $userRepository ;
        $this->compteRepository = $compteRepository;
        $this->manager = $manager ;
        $this->serialier = $serializer ;
        $this->validator = $validator ;
        $this->transactionRepository = $transactionRepository;
        $this->tarifsRepository = $tarifsRepository;
        $this->clientRepository = $clientRepository;
        $this->commissionsRepository = $commissionsRepository;
    }


    /*  ****************************************************************** Get Frais  *************************************************************** */
    
    public function getTarifs($montant) {
         $allTarifs = $this->tarifsRepository->findAll();
        
         foreach($allTarifs as $value) {      
            if($value->getBorneInferieur() < $montant && $value->getBorneSuperieur() >= $montant) {
                return $value->getFrais() ;
            }
         }
    }

    /*  ******************************************************************* End Get Frais  ********************************************************** */



    /*  ************************************************************* Get Commissions  ************************************************************** */
    
    public function _getCommissions() {
        $coms = $this->commissionsRepository->findAll();
        foreach($coms as $value) {
            if($value->getActive() == true && $value->getArchivage() == false) {
                return $value ;
            }
        } 
    }

    /*  ************************************************************** End Get Commissions  ********************************************************* */
   
  
   
    /*  **************************************************************** Genere Code  *************************************************************** */
    
    public function _genereCode() {
         // genere code transaction
         $rand1 = rand(1, 100);  // choose number beetween 10-1000
         $rand2 = rand(100, 1000);  // choose number beetween 1000-1000
         $date = new \DateTime('now');
         $genereCodeTransaction = str_shuffle($rand1.date_format($date, 'YmdHi').$rand2);
        return $genereCodeTransaction;
    } 
    
    /*  **************************************************************** End Genere Code  *********************************************************** */



    /*  ****************************************************************** Do Transaction  ********************************************************** */

    /**
     * @Route(
     *      name="doTransaction" ,
     *      path="/api/transactionClient" ,
     *     methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::doTransaction",
     *         "_api_resource_class"=Transaction::class ,
     *         "_api_collection_operation_name"="doTransaction"
     *     }
     *)
     */
    public function doTransaction(Request $request, SerializerInterface $serializer)
    {
        $dataPostman =  json_decode($request->getContent());
        $utilisateur = $dataPostman->user;
        // get user depot
        $user_depot = $this->userRepository->findOneBy(['id'=>(int)$utilisateur]);

         //get id agence of utilisateur
        $idAgence = $this->userRepository->findOneBy(['id'=>(int)$utilisateur])->getAgence()->getId();
        
        $compteFocus = $this->compteRepository->findBy(['agence'=>$idAgence])[0]; //reper account
        // dd($compteFocus);
        // recup montant to send
         $montantToSended = $dataPostman->montant;
        if($montantToSended < 0) {
            return $this->json("le montant ne peut pas être négatif!", 400);  
        }
        
        //  if count is not enought 
         if($compteFocus->getSolde() < $montantToSended) {
            return $this->json("le solde de votre compte ne permet pas d'efféctuer cette action. Votre solde est de ".$compteFocus->getSolde(), 400);  
         }

        // transfer taxe
        if($montantToSended < 2000000) {     
            $fraisEnvoieHT  = $this->getTarifs($montantToSended);
            $realMontant = $montantToSended - $fraisEnvoieHT;
        } else if($montantToSended >= 2000000) {
             $fraisEnvoieHT = $montantToSended * 0.02;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }    
       
        // Commissions;     
        $commissionEtat = ($this->_getCommissions()->getFraisEtat()) / 100; 
        $commissionSystem = ($this->_getCommissions()->getFraisSystem()) / 100; 
        $commissionEnvoie = ($this->_getCommissions()->getFraisEnvoie()) / 100; 
        $commissionRetrait = ($this->_getCommissions()->getFraisRetrait()) / 100;  

        $fraisEtat = $fraisEnvoieHT * $commissionEtat ;
        $fraisSystem = $fraisEnvoieHT * $commissionSystem ;
        $fraisEnvoie = $fraisEnvoieHT * $commissionEnvoie ;
        $fraisRetrait = $fraisEnvoieHT * $commissionRetrait ;
        //dd('frais'. $fraisEnvoieHT ,'frais etat'.$fraisEtat,'frais envoie'. $fraisEnvoie ,'frais system'. $fraisSystem,'frais retrait'. $fraisRetrait,);

        // refactor compte
        $compteFocus->setSolde(($compteFocus->getSolde() - $montantToSended) + $fraisEnvoie);

        // code and date
        $genereCodeTransaction = $this->_genereCode();
        $date = new \DateTime('now');

        // client who send
        $clientSender = new Client() ;
        $clientSender->setNomComplet($dataPostman->nomCompletEmetteur); 
        $clientSender->setPhone($dataPostman->phoneEmetteur);
        $clientSender->setIdentityNumber($dataPostman->identityNumberEmetteur);
        $clientSender->setCodeTransaction($genereCodeTransaction);  
        $clientSender->setMontant($realMontant);
        $clientSender->setAction('depot');
        $this->manager->persist($clientSender);

        // client who must receive
        $clientReceiver = new Client() ;
        $clientReceiver->setNomComplet($dataPostman->nomCompletBeneficiaire);
        $clientReceiver->setPhone($dataPostman->phoneBeneficiaire);
        // $clientReceiver->setIdentityNumber($receiver->identityNumber);
        $clientReceiver->setCodeTransaction($genereCodeTransaction);
        $this->manager->persist($clientReceiver);
       
        // transaction
        $transaction = new Transaction;
        $transaction->setMontant($realMontant);
        $transaction->setDateDepot($date);
        $transaction->setEtat("Encours");
        $transaction->setTTC($fraisEnvoieHT);
        $transaction->setFraisEtat($fraisEtat);
        $transaction->setFraisSystem($fraisSystem);
        $transaction->setFraisEnvoie($fraisEnvoie);
        $transaction->setFraisRetrait($fraisRetrait);
        $transaction->setRecuperer($clientReceiver);
        $transaction->setEnvoyer($clientSender);
        $transaction->setDeposerUser($user_depot);
        $transaction->setCodeTransaction($genereCodeTransaction);
        $transaction->setCompteEnvoie($compteFocus);
        //dd($transaction);

        // summarize transaction
        $summarizeTransaction = new SummarizeTransaction();
        $summarizeTransaction->setMontant($montantToSended);
        $summarizeTransaction->setCompte($compteFocus->getId());
        $summarizeTransaction->setType("dépôt");
        $this->manager->persist($summarizeTransaction);

 
        $this->manager->persist($transaction);
        $this->manager->flush();
       $json = json_encode('Vous venez d\'envoyer '.$realMontant.' à '.$dataPostman->nomCompletBeneficiaire.' sur le numèro '.$dataPostman->phoneBeneficiaire.'. Le code de transaction est '.$genereCodeTransaction.'');
       $array = json_decode($json, true);
        return $this->json("le depot est bien effectué", 201);
        
    }

    /*  **************************************************** End Do Transaction ******************************************************************** */



    /*  **************************************************** Recup Transaction ********************************************************************* */

     /**
     * @Route(
     *      name="recupTransaction" ,
     *      path="/api/recupTransaction/{code}" ,
     *     methods={"PUT"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::recupTransaction",
     *         "_api_resource_class"=Transaction::class ,
     *         "_api_collection_operation_name"="recupTransaction"
     *     }
     *)
     */
    public function recupTransaction(Request $request, SerializerInterface $serializer, $code)
    {
        $transactionDo =  $this->transactionRepository->findTransactionByCode($code) ;
         
        if($transactionDo) {
            
            if($transactionDo->getEtat() == "Reussie") {
                 return $this->json("Cette transaction est déjà retirée ", 400);  
            } else if($transactionDo->getEtat() == "Annulée"){
                 return $this->json("Cette transaction a étè annulée ", 400);  
            } else {
                // data given on postman
                $dataPostman =  json_decode($request->getContent());
                // $idCompteCaissierGiven = $dataPostman->comptes;


                $user = $dataPostman->user ; //get utilisateur
                $utilisateur = $this->userRepository->findOneBy(['id'=>$user]);
                // dd($utilisateur);
                  //get id agence of utilisateur
                $idAgence = $this->userRepository->findOneBy(['id'=>$utilisateur])->getAgence()->getId();
                // dd($idAgence);
                $focusCompte = $this->compteRepository->findBy(['agence'=>$idAgence])[0]; //reper account
                // dd($focusCompte);


                $time = new \DateTime();
                $transactionDo->setDateRetrait($time);
                $transactionDo->setEtat("Reussie");
                $transactionDo->setCompteRetrait($focusCompte);
                $transactionDo->setRetraitUser($utilisateur);
                $this->manager->persist($transactionDo);
                 //  dd($transactionDo);
                
                $compteFocus =  $this->compteRepository->findOneBy(['id'=>(int)$focusCompte->getId()]);
                $compteFocus->setSolde($compteFocus->getSolde() +$transactionDo->getMontant() + $transactionDo->getFraisRetrait());
                $this->manager->persist($compteFocus);
                //  dd($compteFocus);
                
                //update client received  
                $clientReceiver = $this->clientRepository->find($transactionDo->getRecuperer()->getId());
                $clientReceiver->setMontant($transactionDo->getMontant());
                $clientReceiver->setAction("retrait");
                $this->manager->persist($clientReceiver);
             
                // summarize transaction
                $summarizeTransaction = new SummarizeTransaction();
                $summarizeTransaction->setMontant($transactionDo->getMontant());
                $summarizeTransaction->setCompte($focusCompte->getId());
                $summarizeTransaction->setType("retrait");
                $this->manager->persist($summarizeTransaction);

                $this->manager->flush();
                 return $this->json("retrait reussit", 201);
            }
           
        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }
      
    }

    /*  ********************************************************* End Recup Transaction ************************************************************ */



    /*  ******************************************************** Get Transaction By Code *********************************************************** */

     /**
     * @Route(
     *      name="getTransactionByCode" ,
     *      path="/api/transaction/{code}" ,
     *     methods={"GET"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::getTransactionByCode",
     *         "_api_resource_class"=Transaction::class ,
     *         "_api_collection_operation_name"="getTransactionByCode"
     *     }
     *)
     */
    public function getTransactionByCode(Request $request, SerializerInterface $serializer, $code)
    {
        $data = array();
        
        $transaction =  $this->transactionRepository->findTransactionByCode($code) ;

        if($transaction) {
           
            $recuperator = $this->clientRepository->findById($transaction->getRecuperer()->getId());
            // transaction client
            if($recuperator) {
                $envoyer = $this->clientRepository->findById($transaction->getEnvoyer()->getId());
                // browser data   
                foreach($envoyer as $env ) {
                    foreach($recuperator as $recup) {
                        array_push($data, $transaction, $env, $recup );
                    }
                }
                return $this->json($data , 200);
            } else {
                $deposer = $this->clientRepository->findById($transaction->getDeposer()->getId());
                $retrait = $this->clientRepository->findById($transaction->getRetrait()->getId());
                
                foreach($deposer as $dep) {
                    foreach($retrait as $ret) {
                        array_push($data, $transaction, $dep, $ret);
                    }
                }
                return $this->json($data , 200);
            }

        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }

    }

    /*  ***************************************************** End Get Transaction By Code *********************************************************** */

}





   