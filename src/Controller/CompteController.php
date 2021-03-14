<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Repository\CompteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompteController extends AbstractController
{
    /**
     * @Route(
     *      name="getCompteByAgence" ,
     *      path="/api/compte/{idAgence}/agence" ,
     *     methods={"GET"} 
     *)
    */
     // *     defaults={
     //   *     "__controller"="App\Controller\CompteController::getCompteByAgence",
     //   *         "_api_resource_class"=Compte::class,
     //    *         "_api_collection_operation_name"="getCompteByAgence"
       // *     }
    public function getCompteByAgence( Request $request, CompteRepository $compteRepository, $idAgence) {
         $compte = $compteRepository->findCompteByidAgence($idAgence);
         //dd($compte);
         return $this->json($compte, 200);
    }
}
