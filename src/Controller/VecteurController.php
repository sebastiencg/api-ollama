<?php

namespace App\Controller;

use App\Entity\Mot;
use App\Entity\Vecteur;
use App\Repository\MotRepository;
use App\Service\TakeVecteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VecteurController extends AbstractController
{


    #[Route('/vecteur', name: 'app_vecteur')]
    public function index(): Response
    {
        return $this->render('vecteur/index.html.twig', [
            'controller_name' => 'VecteurController',
        ]);
    }
    #[Route('/vecteur/{newMot}', name: 'app_home' , methods: ['GET'])]
    public function add($newMot, MotRepository $motRepository,TakeVecteur $takeVecteur,HttpClientInterface $httpClient, EntityManagerInterface $entityManager): Response
    {
        $motFind=$motRepository->findOneBy(["mot"=>"train"]);

        $mot= $motRepository->findOneBy(["mot"=>"$newMot"]);
        if ($mot){

            $similar=$takeVecteur->similar($motFind->getVecteurs()->toArray(),$mot->getVecteurs()->toArray());

            if ($similar==100){
                return $this->json(["message"=>"félicitation tu as gagné"],Response::HTTP_OK);
            }
            else{
                return $this->json(["message"=>"tu es a $similar % de similarité"],Response::HTTP_OK);
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


        return $this->json(["message"=>"tu es a $similar % de similarité"],Response::HTTP_OK);



    }

}
