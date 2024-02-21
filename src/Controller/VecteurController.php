<?php

namespace App\Controller;

use App\Entity\Mot;
use App\Entity\MotDuJour;
use App\Entity\Vecteur;
use App\Repository\MotDuJourRepository;
use App\Repository\MotRepository;
use App\Service\TakeVecteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VecteurController extends AbstractController
{


    #[Route('/vecteur/cheat/motDuJour', name: 'app_vecteur')]
    public function index(MotDuJourRepository $motDuJourRepository): Response
    {
        $dernierMot = $motDuJourRepository->findOneBy([], ['id' => 'DESC']);
        $mot=$dernierMot->getMot()->getMot();

        return $this->json($mot,Response::HTTP_OK);

    }
    #[Route('/vecteur/{newMot}', name: 'app_vecteur_mot' , methods: ['GET'])]
    public function add($newMot, MotRepository $motRepository,TakeVecteur $takeVecteur,HttpClientInterface $httpClient, EntityManagerInterface $entityManager,MotDuJourRepository $motDuJourRepository): Response
    {
        //$motFind=$motRepository->findOneBy(["mot"=>"train"]);

        $motFind=$this->motDuJour($motDuJourRepository,$motRepository,$entityManager,$takeVecteur,$httpClient);

        $mot= $motRepository->findOneBy(["mot"=>"$newMot"]);
        if ($mot){

            $similar=$takeVecteur->similar($motFind->getVecteurs()->toArray(),$mot->getVecteurs()->toArray());

            if ($similar==100){
                return $this->json(["message"=>100],Response::HTTP_OK);
            }
            else{
                return $this->json(["message"=>$similar],Response::HTTP_OK);
            }
        }

        $addMot=new Mot();
        $addMot->setMot($newMot);
        $entityManager->persist($addMot);

        $tab=$takeVecteur->httpClient($newMot,$httpClient);
        foreach ($tab as $vecteurs){

            foreach ($vecteurs as $vecteur){

                $newVecteur= new Vecteur();
                $newVecteur->setValue($vecteur);
                $newVecteur->setMot($addMot);
                $entityManager->persist($newVecteur);
            }
        }
        $entityManager->flush();

        $similar=$takeVecteur->similar2($motFind->getVecteurs()->toArray(), $tab["embedding"]);


        return $this->json(["message"=>$similar],Response::HTTP_OK);



    }

    public function motDuJour($motDuJourRepository, $motRepository, $entityManager, $takeVecteur, $httpClient)
    {
        $dernierMot = $motDuJourRepository->findOneBy([], ['id' => 'DESC']);

        if ($dernierMot){

            $createdAt = $dernierMot->getCreatedAt();

            $now = new \DateTime();

            $unJourPlusTard = (clone $now)->modify('+1 day');


            if ($createdAt < $unJourPlusTard) {

                return $dernierMot->getMot();
            }
        }

        $all=$motRepository->findAll();

        if (!empty($all)) {
            $randomIndex = array_rand($all);

            $motAleatoire = $all[$randomIndex];

            $newMotDuJour= new MotDuJour();

            $newMotDuJour->setMot($motAleatoire);

            $newMotDuJour->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($newMotDuJour);

            $entityManager->flush();

            return $motAleatoire;
        } else {
            $addMot = new Mot();
            $addMot->setMot("train");
            $entityManager->persist($addMot);

            $tab=$takeVecteur->httpClient($addMot->getMot(),$httpClient);

            foreach ($tab as $vecteurs){

                foreach ($vecteurs as $vecteur){

                    $newVecteur= new Vecteur();
                    $newVecteur->setValue($vecteur);
                    $newVecteur->setMot($addMot);
                    $entityManager->persist($newVecteur);
                }
            }
            $entityManager->flush();

            return $addMot;
        }
    }
}
