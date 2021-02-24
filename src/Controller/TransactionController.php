<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Transaction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{


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
    public function __construct(UserRepository $userRepository, SerializerInterface $serializer,
                                EntityManagerInterface $manager, ValidatorInterface $validator)
    {

        $this->userRepository = $userRepository ;
        $this->manager = $manager ;
        $this->serialier = $serializer ;
        $this->validator = $validator ;
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
        // $dataPostman =  $request->getContent();

        // recup montant to send
        $montantToSended = $dataPostman->montant;

        if($montantToSended <= 5000 ) {
            $realMontant = $montantToSended - 425 ;
            dd($realMontant);
        } else if ($montantToSended > 5000 && $montantToSended <= 10000) {
            dd("sup 5000");
        }

        dd($montantToSended);
        dd($dataPostman) ;
        //instance groupe
        $grpCompetence = new GroupeCompetence() ;
        //recup groupe libelle
        $libelleGroupe = $dataPostman->libelle ;
        //verify if name groupe exist or not
        if($this->groupeCompetenceRepository->findOneBy(["libelle"=>$libelleGroupe])) {
            return new JsonResponse("this name'crew exists already, please select others!",Response::HTTP_BAD_REQUEST,[],true) ;
        }

          $grpCompetence->setLibelle($dataPostman->libelle) ;

        $competences = $dataPostman->competence ;

            $this->manager->flush();
    
        return $this->json("Added");
    }
}
