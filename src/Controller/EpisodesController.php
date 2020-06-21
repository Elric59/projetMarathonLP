<?php

namespace App\Controller;

use App\Entity\Episodes;
use App\Form\EpisodesType;
use App\Repository\EpisodesRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/episodes")
 */
class EpisodesController extends AbstractController
{
    /**
     * @Route("/", name="episodes_index", methods={"GET"})
     */
    public function index(EpisodesRepository $episodesRepository): Response
    {
        return $this->render('episodes/index.html.twig', [
            'episodes' => $episodesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="episodes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $episode = new Episodes();
        $form = $this->createForm(EpisodesType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('episodes_index');
        }

        return $this->render('episodes/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="episodes_show", methods={"GET"})
     */
    public function show(Episodes $episode): Response
    {
        return $this->render('episodes/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="episodes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Episodes $episode): Response
    {
        $form = $this->createForm(EpisodesType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('episodes_index');
        }

        return $this->render('episodes/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/seen", name="episode_seen", methods={"GET"})
     */
    public function userSeenEpisode(Episodes $episodes){
        $em = $this->getDoctrine()->getManager();
        if($this->isGranted('ROLE_USER') === true){
            $user = $this->getUser();
            if($user->getEpisodes()->contains($episodes)){
                $user->removeEpisode($episodes);
            }
            else{
                $user->addEpisode($episodes);
            }
            $em->persist($user);
            $em->flush($user);
        }
        return $this->redirectToRoute('series_show',["id" => $episodes->getSeries()->getId()]);
    }

    /**
     * @Route("/{id}/seen/saison/{saison}", name="saison_seen", methods={"GET"})
     */
    public function userSeenSaison(Episodes $episodes, $saison, EpisodesRepository $episodesRepository){
        $em = $this->getDoctrine()->getManager();
        $episodesSaison = $episodesRepository->getAllEpisodesSaison($episodes->getSeries(), $saison);
        if($this->isGranted('ROLE_USER') === true){
            $user = $this->getUser();
            foreach ($episodesSaison as $episode){
                if(!$user->getEpisodes()->contains($episode)){
                    $user->addEpisode($episode);
                }
            }
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('series_show',["id" => $episodes->getSeries()->getId()]);
    }

    /**
     * @Route("/{id}/unseen/saison/{saison}", name="saison_unseen", methods={"GET"})
     */
    public function userUnseenSaison(Episodes $episodes, $saison, EpisodesRepository $episodesRepository){
        $em = $this->getDoctrine()->getManager();
        $episodesSaison = $episodesRepository->getAllEpisodesSaison($episodes->getSeries(), $saison);
        if($this->isGranted('ROLE_USER') === true){
            $user = $this->getUser();
            foreach ($episodesSaison as $episode){
                if($user->getEpisodes()->contains($episode)){
                    $user->removeEpisode($episode);
                }
            }
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('series_show',["id" => $episodes->getSeries()->getId()]);
    }

    /**
     * @Route("/{id}/seen/serie", name="serie_seen", methods={"GET"})
     */
    public function userSeenSerie(Episodes $episodes, EpisodesRepository $episodesRepository){
        $em = $this->getDoctrine()->getManager();
        $episodesSerie = $episodesRepository->getAllSaison($episodes->getSeries());
        if($this->isGranted('ROLE_USER') === true){
            $user = $this->getUser();
            foreach ($episodesSerie as $episode){
                if(!$user->getEpisodes()->contains($episode)){
                    $user->addEpisode($episode);
                }
            }
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('series_show',["id" => $episodes->getSeries()->getId()]);
    }

    /**
     * @Route("/{id}/unseen/serie", name="serie_unseen", methods={"GET"})
     */
    public function userUnseenSerie(Episodes $episodes, EpisodesRepository $episodesRepository){
        $em = $this->getDoctrine()->getManager();
        $episodesSerie = $episodesRepository->getAllSaison($episodes->getSeries());
        if($this->isGranted('ROLE_USER') === true){
            $user = $this->getUser();
            foreach ($episodesSerie as $episode){
                if($user->getEpisodes()->contains($episode)){
                    $user->removeEpisode($episode);
                }
            }
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('series_show',["id" => $episodes->getSeries()->getId()]);
    }


    /**
     * @Route("/{id}", name="episodes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Episodes $episode): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('episodes_index');
    }
}
