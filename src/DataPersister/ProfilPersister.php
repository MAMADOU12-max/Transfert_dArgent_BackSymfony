<?php
namespace App\DataPersister;

use App\Entity\Profil;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;



final class ProfilPersister implements ContextAwareDataPersisterInterface
{
 
    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, ProfilRepository $profilRepository){
        $this->manager = $manager ;
        $this->userRepository = $userRepository ;
        $this->profilRepository = $profilRepository;
    }

    public function supports($data, array $context = []): bool {
        return $data instanceof Profil;
    }

    public function persist($data, array $context = []) {
        // call your persistence layer to save $data

        $data->setLibelle($data->getLibelle()) ;
        $this->manager->persist($data) ;
        $this->manager->flush();
        return $data;
    }

    public function remove($data, array $context = []){
       
        $id = $data->getId() ;
       
        //archive profil  
        $data->setArchivage(1) ;
        $persist = $this->manager->persist($data);
        $this->manager->flush($persist);

        // id profil users 
        $users = $this->userRepository->findBy(['profils'=>$id]) ;
        // dd($users);  
        // parcourir users 
        foreach ($users as $value) {
            $value->setArchivage(1) ;
            $user = $this->manager->persist($value) ; 
            $this->manager->flush($user);
        }

    }
}
