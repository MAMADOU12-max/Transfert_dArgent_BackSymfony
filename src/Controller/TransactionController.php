<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Compte;
use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{


     /**
     * @var transactionRepository
     */
    private $transactionRepository;
    /**
     * @var compteRepository
     */
    private $compteRepository;
    /**
     * @var userRepository
     */
    private $userRepository;
    /**
     * @var SerializerInterface
     */
    private $serialier;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * UserController constructor.
     */
    public function __construct(UserRepository $userRepository, SerializerInterface $serializer, CompteRepository $compteRepository,
                                EntityManagerInterface $manager, ValidatorInterface $validator, TransactionRepository $transactionRepository)
    {

        $this->userRepository = $userRepository ;
        $this->compteRepository = $compteRepository;
        $this->manager = $manager ;
        $this->serialier = $serializer ;
        $this->validator = $validator ;
        $this->transactionRepository = $transactionRepository;
    }


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
        // dd($dataPostman) ;

        // recup montant to send
         $montantToSended = $dataPostman->montant;

         $userDoingTransaction = $dataPostman->comptes; 
         $compteFocus = $this->compteRepository->findOneBy(['id'=>(int)$userDoingTransaction]);

    
        //  if count is not enought 
         if($compteFocus->getSolde() < $montantToSended) {
            return $this->json("Votre solde ne permet pas d'efféctuer cette action", 400);  
         }
        //  dd($compteFocus->getSolde());  


        // transfer taxe
        if($montantToSended <= 5000 ) {
            $fraisEnvoieHT = 425;
            $realMontant = $montantToSended - 425 ;
            // dd($realMontant);
        } else if ($montantToSended > 5000 && $montantToSended <= 10000) {
            $fraisEnvoieHT = 850;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 10000 && $montantToSended <= 15000) {
            $fraisEnvoieHT = 1270;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 15000 && $montantToSended <= 20000) {
            $fraisEnvoieHT = 1695;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 20000 && $montantToSended <= 50000) {
            $fraisEnvoieHT = 2500;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 50000 && $montantToSended <= 60000) {
            $fraisEnvoieHT = 3000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 60000 && $montantToSended <= 75000) {
            $fraisEnvoieHT = 4000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 75000 && $montantToSended <= 120000) {
            $fraisEnvoieHT = 5000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 120000 && $montantToSended <= 150000) {
            $fraisEnvoieHT = 6000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 150000 && $montantToSended <= 200000) {
            $fraisEnvoieHT = 7000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 200000 && $montantToSended <= 250000) {
            $fraisEnvoieHT = 8000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 250000 && $montantToSended <= 300000) {
            $fraisEnvoieHT = 9000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 300000 && $montantToSended <= 400000) {
            $fraisEnvoieHT = 12000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 400000 && $montantToSended <= 750000) {
            $fraisEnvoieHT = 15000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 750000 && $montantToSended <= 900000) {
            $fraisEnvoieHT = 22000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 900000 && $montantToSended <= 1000000) {
            $fraisEnvoieHT = 25000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }else if ($montantToSended > 1000000 && $montantToSended <= 1125000) {
            $fraisEnvoieHT = 27000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }
        else if ($montantToSended > 1125000 && $montantToSended <= 1400000) {
            $fraisEnvoieHT = 30000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }
        else if ($montantToSended > 1400000 && $montantToSended <= 2000000) {
            $fraisEnvoieHT = 30000;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }  else if ( $montantToSended > 2000000) {
            $fraisEnvoieHT = $montantToSended * 0.02;
            $realMontant = $montantToSended - $fraisEnvoieHT  ;
        }
        // dd($fraisEnvoieHT);
        // Commissions
        $fraisEtat = $fraisEnvoieHT * 0.4 ;
        $fraisSystem = $fraisEnvoieHT * 0.3 ;
        $fraisEnvoie = ($fraisSystem) * (1/3) ;
        $fraisRetrait = ($fraisSystem) * (2/3) ;

        // refactor compte
        $compteFocus->setSolde(($compteFocus->getSolde() - $montantToSended) + $fraisEnvoie);

        // client who send
        $sender = $dataPostman->envoyer;
        $clientSender = new Client() ;
        $clientSender->setNomComplet($sender->nomComplet);
        $clientSender->setPhone($sender->phone);
        $clientSender->setIdentityNumber($sender->identityNumber);
        $this->manager->persist($clientSender);

        // client who must receive
        $receiver = $dataPostman->recuperer;
        $clientReceiver = new Client() ;
        $clientReceiver->setNomComplet($receiver->nomComplet);
        $clientReceiver->setPhone($receiver->phone);
        $clientReceiver->setIdentityNumber($receiver->identityNumber);
        $this->manager->persist($clientReceiver);

        //transaction
        $transaction = new Transaction;
        $transaction->setMontant($realMontant);
        $time = new \DateTime();
        $transaction->setDateDepot($time);
        $transaction->setEtat("Encours");
        $transaction->setTTC(123);
        $transaction->setFraisEtat($fraisEtat);
        $transaction->setFraisSystem($fraisSystem);
        $transaction->setFraisEnvoie($fraisEnvoie);
        $transaction->setFraisRetrait($fraisRetrait);
        $transaction->setRecuperer($clientReceiver);
        $transaction->setEnvoyer($clientSender);
        $transaction->setCodeTransaction(rand());
        $transaction->setComptes($this->compteRepository->findOneBy(['id'=>(int)$userDoingTransaction]));

        // dd($transaction);
 

        $this->manager->persist($transaction);
        $this->manager->flush();
        return $this->json("success", 201);
        
    }

    /*  *********************************************************Recup Transaction********************************************************************** */

     // Recup transaction

     /**
     * @Route(
     *      name="recupTransaction" ,
     *      path="/api/recupTransaction/{id}" ,
     *     methods={"PUT"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::recupTransaction",
     *         "_api_resource_class"=Transaction::class ,
     *         "_api_collection_operation_name"="recupTransaction"
     *     }
     *)
     */
    public function recupTransaction(Request $request, SerializerInterface $serializer, $id)
    {
        $transactionDo =  $this->transactionRepository->findTransactionByCode($id) ;
        //   dd($transactionDo->getDateRetrait());

        if($transactionDo) {

            if($transactionDo->getDateRetrait() !== null) {
                return $this->json("Cette transaction est déjà retirée ", 400);  
            } else if($transactionDo->getDateAnnulation() !== null){
                return $this->json("Cette transaction a étè annulée ", 400);  
            } else {
                $time = new \DateTime();
                $transactionDo->setDateRetrait($time);
                $transactionDo->setEtat("Reussie");
                $this->manager->persist($transactionDo);
                // dd($transactionDo->getFraisRetrait());

                $dataPostman =  json_decode($request->getContent());

                $idCompteCaissierGiven = $dataPostman->comptes;
                $compteFocus =  $this->compteRepository->findOneBy(['id'=>(int)$idCompteCaissierGiven]);
                $compteFocus->setSolde($compteFocus->getSolde() +$transactionDo->getMontant() + $transactionDo->getFraisRetrait());
                $this->manager->persist($compteFocus);
                // dd($compteFocus);
                $this->manager->flush();
                return $this->json("success", 201);
            }
           

        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }

        
    }
}
