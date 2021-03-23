<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use App\Entity\Agence;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
      /**
     * @var SerializerInterface
     */
    private $serialize;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(SerializerInterface $serializer, UserRepository $userRepository,
    EntityManagerInterface $manager, ValidatorInterface $validator,
     UserPasswordEncoderInterface $encoder )
    {
        $this->serialize = $serializer ;
        $this->validator = $validator ;
        $this->encoder = $encoder ;
        $this->manager = $manager ;
        $this->userRepository = $userRepository ;
    }

    /**
     * @Route(
     *      name="addUser" ,
     *      path="/api/admin/users" ,
     *     methods={"POST"} ,
     *     defaults={
     *     "__controller"="App\Controller\UserController::addUser",
     *         "_api_resource_class"=User::class,
     *         "_api_collection_operation_name"="adding"
     *     }
     *
     *)
    */
    public function adUser( Request $request) {

        //all data
        $user = $request->request->all() ;
        // dd($user);

        //get profil and agence
        $profil = $user["profil"] ;
        $agence = $user["agence"];
        //Instance User
        $newUser = new User();

        // if agence exist
        if($user["agence"]) {
            $newUser->setAgence($this->manager->getRepository(Agence::class)->findOneBy(['id'=>$agence])) ;
            $newUser->setWorking(1);
         }
         if($profil == "") {
            return $this->json("Vous devez obligatoirement définir un profil", 400);
         }
        $newUser->setProfils($this->manager->getRepository(Profil::class)->findOneBy(['libelle'=>$profil])) ;
         
        // dd($user);

        //recupération de l'image
        $photo = $request->files->get("avatar");
        //is not obliged
        if($photo)
        {
            $photoBlob = fopen($photo->getRealPath(),"rb");

            $newUser->setAvatar($photoBlob);
        }


        $errors = $this->validator->validate($user);
        if (count($errors)){
            $errors = $this->serialize->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }

       
        $newUser->setPassword($this->encoder->encodePassword($newUser, $user['password'])) ; 
        $newUser->setUsername($user['username']);
        $newUser->setFirstname($user['firstname']);
        $newUser->setLastname($user['lastname']);
        $newUser->setPhone($user['phone']);
        $newUser->setIdentityNum($user['identityNum']); 
        $newUser->setAddress($user['address']);
        $newUser->setArchivage(false);
        $newUser->setType(strtolower($profil));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($newUser); 
       // dd($newUser);
        $em->flush();

        return $this->json("success",201);

    }

}
