<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\EpisodesRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SeriesRepository $seriesRepository,EpisodesRepository $episodesRepository,Request $request)
    {

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $Resultat=null;
            if($data['Choix']=='Episode') {

                $Resultat = $episodesRepository->getEpisodesYouWant($data['Recherche']);
            }
            else if($data['Choix']=='Serie') {

            }
            else if($data['Choix']=='Categorie') {

            }

            return $this->redirectToRoute('home',[
                'resultat' => $Resultat
                ]);
        }

        return $this->render('home/index.html.twig',[
            'score_ordres' => $seriesRepository->getSerieByScoreDesc(),
            'last_series' => $seriesRepository->getThreeLastSeries(),
            'form' => $form->CreateView(),


        ]);

    }
}
