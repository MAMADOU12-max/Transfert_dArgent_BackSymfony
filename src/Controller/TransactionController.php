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
use App\Repository\SummarizeTransactionRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer, CompteRepository $compteRepository, 
                                EntityManagerInterface $manager, ValidatorInterface $validator, TransactionRepository $transactionRepository,
                                TarifsRepository $tarifsRepository, ClientRepository $clientRepository, CommissionsRepository $commissionsRepository,
                                SummarizeTransactionRepository $summarizeTransactionRepository) {

        $this->userRepository = $userRepository ;
        $this->compteRepository = $compteRepository;
        $this->manager = $manager ;
        $this->serialier = $serializer ;
        $this->validator = $validator ;
        $this->transactionRepository = $transactionRepository;
        $this->tarifsRepository = $tarifsRepository;
        $this->clientRepository = $clientRepository;
        $this->commissionsRepository = $commissionsRepository;
        $this->summarizeTransactionRepository = $summarizeTransactionRepository;
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

    /*  ************************************ Return Frais ******************************************* */

     /**
     * @Route(
     *      name="returnFrais" ,
     *      path="/api/fraisis" ,
     *       methods={"POST"} 
     *)
     */
    //,
   //  *       defaults={
   //  *         "__controller"="App\Controller\TransactionController::returnFrais",
    // *         "_api_resource_class"=Frais::class ,
    // *         "_api_collection_operation_name"="returnFrais"
   //  *     }
    public function retunFrais(Request $request) {
        $montantPostman =  json_decode($request->getContent());
        if($montantPostman->montant < 0) {
            return $this->json("le montant ne peut pas être négatif!", 400);  
        } else if(!is_numeric($montantPostman->montant)) {
            return $this->json("Vous devez founir un nombre valide, non une chaine de caractère!", 400); 
        } else if($montantPostman->montant > 2000000) {
            $frais = ((int)($montantPostman->montant)) * 0.02;
            return $this->json($frais, 200);
        }

        $frais  = $this->getTarifs((int)($montantPostman->montant));
        //$array = json_decode($frais, true);
        return $this->json($frais, 200);      
    }

    /*  *************************************** End Return Frais  ************************************* */
    

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
         $rand1 = rand(1, 99);  // choose number beetween 10-1000
         $rand2 = rand(1, 99);  // choose number beetween 1000-1000
         $date = new \DateTime('now');
         $genereCodeTransaction = $rand1.date_format($date, 'mdHis').$rand2;
       //  $genereCodeTransaction = str_shuffle($rand1.date_format($date, 'YmdHi').$rand2);
        return $genereCodeTransaction;
    } 
    
    /*  ******************************** End Genere Code  ******************************************** */



    /*  ********************************** Do Transaction  ******************************************* */

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
     *  )
     */
    public function doTransaction(Request $request, SerializerInterface $serializer)
    {
        $dataPostman =  json_decode($request->getContent());
        // get user depot
        $user_depot = $this->getUser();
        $utilisateur = $user_depot->getId();
         //get id agence of utilisateur
        $idAgence = $user_depot->getAgence()->getId();
        //dd($idAgence);
        $compteFocus = $this->compteRepository->findBy(['agence'=>$idAgence])[0]; //reper account
         //dd($compteFocus);
        // recup montant to send
         $montantToSended = $dataPostman->montant;
        if ($montantToSended < 0){
            return $this->json("le montant ne peut pas être négatif!", 400);  
        }
        
        //  if count is not enought 
         if($compteFocus->getSolde() < $montantToSended) {
            return $this->json("le solde de votre compte ne permet pas d'efféctuer cette action. Votre solde est de ".$compteFocus->getSolde(), 400);  
         }

        // transfer taxe
        if ($montantToSended < 2000000) {     
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

        // code and date
        $genereCodeTransaction = $this->_genereCode();
        $date = new \DateTime('now') ; 
         $dateFormatted = date_format($date,"d/m/Y H:i");
        //$dateFormatted = (new \DateTime('now'))->format("d/m/Y H:i") ;
        // dd($genereCodeTransaction);

        // refactor compte
        $compteFocus->setSolde(($compteFocus->getSolde() - $montantToSended) + $fraisEnvoie);
        $compteFocus->setMiseajour($dateFormatted);

        // client who send
        $clientSender = new Client() ;
        $clientSender->setNom($dataPostman->nomEmetteur); 
        $clientSender->setPrenom($dataPostman->prenomEmetteur);
        $clientSender->setPhone($dataPostman->phoneEmetteur);
        $clientSender->setIdentityNumber($dataPostman->identityNumberEmetteur);
        $clientSender->setCodeTransaction($genereCodeTransaction);  
        $clientSender->setMontant($realMontant);
        $clientSender->setAction('depot');
        $this->manager->persist($clientSender);
    
        // client who must receive
        $clientReceiver = new Client() ;
        $clientReceiver->setNom($dataPostman->nomBeneficaire);
        $clientReceiver->setPrenom($dataPostman->prenomBeneficaire);
        $clientReceiver->setPhone($dataPostman->phoneBeneficiaire);
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
        $summarizeTransaction->setUser( $utilisateur);
        $summarizeTransaction->setDatetransaction($date);
        $summarizeTransaction->setFrais($fraisEnvoieHT);
        $summarizeTransaction->setCodeTransaction($genereCodeTransaction);
        $this->manager->persist($summarizeTransaction);

 
        $this->manager->persist($transaction);
         $this->manager->flush();
        $json = json_encode('Vous venez d\'envoyer '.$realMontant.' à '.$dataPostman->nomBeneficaire.' '.$dataPostman->prenomBeneficaire.' sur le numèro '.$dataPostman->phoneBeneficiaire.'. Le code de transaction est '.$genereCodeTransaction.'');
        $array = json_decode($json, true);
        return $this->json($array, 201);
        
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

                $userConnected = $this->getUser();  //for recup token's user
                  //get id agence of utilisateur
                $idAgence = $this->userRepository->findOneBy(['id'=>$userConnected->getId()])->getAgence()->getId();
                // dd($idAgence);
                $focusCompte = $this->compteRepository->findBy(['agence'=>$idAgence])[0]; //reper account
                // dd($focusCompte);

                $time = new \DateTime();
                $dateFormatted = date_format($time,"d/m/Y H:i");
                $transactionDo->setDateRetrait($time);
                $transactionDo->setEtat("Reussie");
                $transactionDo->setCompteRetrait($focusCompte);
                $transactionDo->setRetraitUser($userConnected);
                $this->manager->persist($transactionDo);
                 //  dd($transactionDo);
                
                $compteFocus =  $this->compteRepository->findOneBy(['id'=>(int)$focusCompte->getId()]);
                $compteFocus->setSolde($compteFocus->getSolde() +$transactionDo->getMontant() + $transactionDo->getFraisRetrait());
                $compteFocus->setMiseajour($dateFormatted);
                $this->manager->persist($compteFocus);
               // dd($compteFocus->getMiseajour());

                // get identity from postman
                $identifiantBeneficiaire = json_decode($request->getContent())->identifiantBeneficiaire;
                //  dd($identifiantBeneficiaire);

                //update client received  
                $clientReceiver = $this->clientRepository->find($transactionDo->getRecuperer()->getId());
                $clientReceiver->setMontant($transactionDo->getMontant());
                $clientReceiver->setAction("retrait");
                $clientReceiver->setIdentityNumber((int)($identifiantBeneficiaire));
                $this->manager->persist($clientReceiver);
                //dd($clientReceiver);

                // summarize transaction
                $summarizeTransaction = new SummarizeTransaction();
                $summarizeTransaction->setMontant($transactionDo->getMontant());
                $summarizeTransaction->setCompte($focusCompte->getId());
                $summarizeTransaction->setType("retrait");
                $summarizeTransaction->setUser($userConnected->getId());
                $summarizeTransaction->setDatetransaction($time);
                $summarizeTransaction->setFrais(0);
                $summarizeTransaction->setCodeTransaction($transactionDo->getCodeTransaction());
                $this->manager->persist($summarizeTransaction);
                
                 $this->manager->flush();
                return $this->json("Vous avez retiré ".$transactionDo->getMontant()." par le distributeur N°".$focusCompte->getIdentifiantCompte()."."."\n"."Date de retrait: ".$focusCompte->getMiseajour()."", 200);

            }
           
        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }
      
    }

    /*  ******************************************* End Recup Transaction ****************************************************** */
 
 
 
    /*  ****************************************** Get Transaction By Code not verif ******************************************** */
   
     /**
     * @Route(
     *      name="recupTransaction" ,
     *      path="/api/getdirecttransaction/{code}" ,
     *     methods={"GET"}
     *)
     */
     public function getTransactionCodeDirectly($code) {

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
        $transactionDo =  $this->transactionRepository->findTransactionByCode($code) ;
        return $this->json($transactionDo , 200);
     }
    
    /*  *************************************** End Get Transaction By Code not verif ****************************************** */



    /*  ******************************************** Get Transaction By Code *************************************************** */

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

           if($transaction->getEtat() === "Encours") {

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

            } else if($transaction->getEtat() === "Reussie") {
                return $this->json("Cette transaction est achevée. L'argent est déjà retiré!", 400); 
            }  else {
                return $this->json("Cette transaction a été annulée", 400);
            }  
           
        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }

    }

    /*  ***************************************************** End Get Transaction By Code *********************************************************** */




    /*  ********************************************************** Annuler Transaction ************************************************************** */
   
        /**
     * @Route(
     *      name="annulerTransaction" ,
     *      path="/api/transaction/{code}/annuler" ,
     *     methods={"GET"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::annulerTransaction",
     *         "_api_resource_class"=Transaction::class ,
     *         "_api_collection_operation_name"="annulerTransaction"
     *     }
     *)
     */
    public function annulerTransaction(Request $request, SerializerInterface $serializer, $code)
    {

        $transaction =  $this->transactionRepository->findTransactionByCode($code) ;

        if($transaction) {
            if($transaction->getEtat() == "Reussie") {
                return $this->json("Cette transaction est déjà retirée ", 400);  
           } else if($transaction->getEtat() == "Annulée"){
                return $this->json("Cette transaction est déjà annulée ", 400);  
           } else {
               // find account
               $focusCompte = $this->compteRepository->findById($transaction->getCompteEnvoie()->getId())[0]; 
               
               $compteFocus =  $this->compteRepository->findOneBy(['id'=>(int)$focusCompte->getId()]);
               $montant = ($compteFocus->getSolde() +$transaction->getMontant() + $transaction->getTtc()) - $transaction->getFraisEnvoie();
               $compteFocus->setSolde($montant);
               $this->manager->persist($compteFocus);
               
               //update client received  
               $clientReceiver = $this->clientRepository->find($transaction->getRecuperer()->getId());
               $clientReceiver->setAction("annulée");
               $this->manager->persist($clientReceiver);
               $nomClient =$clientReceiver->getPrenom().' '.$clientReceiver->getNom();
               $numClient = $clientReceiver->getPhone();

               $time = new \DateTime();
               $transaction->setDateAnnulation($time);
               $transaction->setMontant($transaction->getMontant() + $transaction->getTtc());
               $transaction->setEtat("Annulée");
               $transaction->setFraisEtat(0);   
               $transaction->setFraisSystem(0); 
               $transaction->setFraisEnvoie(0);  
               $transaction->setFraisRetrait(0);
               $transaction->setTtc(0);
               $this->manager->persist($transaction);
               
               $this->manager->flush();
               $json = json_encode('Vous venez d\'annuler la transaction de '.$transaction->getMontant().' que vous aviez envoyé à '.$nomClient.' sur le numèro '.$numClient.'. \n Date d\'annulation: '.$time->format('Y-m-d H:i:s').' . Merci pour votre confiance!');
               $array = json_decode($json, true);
               return $this->json($array, 200);
           }

        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }

    }
   
    /*  ***************************** End Annuler Transaction ***************************************** */



    /* ******************************* Transaction par Compte ***************************************** */
  
    
     /**
     * @Route(
     *      name="transactionByCompte" ,
     *      path="/api/transactionByCompte" ,
     *     methods={"GET"}
     *)
     */
    public function transactionByCompte() {
              $compte = array();
              $idCompte = $this->getUser()->getAgence()->getCompte()->getId();
          //    $idCompte = $this->getUser()->getAgence()->getCompte()->getId();
             // dd($idCompte);
              $alldepotsComptes = $this->summarizeTransactionRepository->findAll();
              foreach($alldepotsComptes as $value) {
                    if($value->getCompte() == $idCompte) {
                        array_push($compte, $value );
                    }
              }
              return $this->json($compte , 200);
              
    }

    /* ***************************End Transaction par Compte* ******************************* */



    /* *************************** End Transaction par User ********************************* */

       /**
     * @Route(
     *      name="transactionByUser" ,
     *      path="/api/transactionByUser" ,
     *     methods={"GET"}
     *)
     */
    public function transactionByUser(Request $request) {
        $transaction = array();
        $idUser = $this->getUser()->getId();
        // dd($idUser);
        $alldepotsComptes = $this->summarizeTransactionRepository->findAll();
        foreach($alldepotsComptes as $value) {
              if($value->getUser() == $idUser) {
                  array_push($transaction, $value );
              }
        }
        return $this->json($transaction , 200);
        
    }


    /* *************************** End Transaction par User ********************************* */

}





   