<?php
namespace App\OverloadToken;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class OverToken {

    public function tokenOver(JWTCreatedEvent $event) {
        //Recuperation de l'utilisateur

        $user = $event->getUser();
        // get exist data token
        $data =$event->getData();
        //Surchage des donnees du Token
         $data['id'] = $user->getId();
         $data['username'] = $user->getUsername();
       // $data['email'] = $user->getEmail();
         $data['Archivage'] = $user->getArchivage();

        // Revoie des donnees du Token
        $event->setData($data);

    }
}