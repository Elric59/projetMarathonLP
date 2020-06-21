<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\CommentsRepository;
use App\Repository\EpisodesRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/series")
 */
class SeriesController extends AbstractController
{
    /**
     * @Route("/", name="series_index", methods={"GET"})
     */
    public function index(SeriesRepository $seriesRepository): Response
    {
        return $this->render('series/index.html.twig', [
            //'series' => $seriesRepository->findAll(),
            'series' => $seriesRepository->getSerieByScoreDesc(),
        ]);
    }

    /**
     * @Route("/new", name="series_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        $file = $form['movie']->getData();

        if($file !== null && $file instanceof UploadedFile) {

            $fileInfo = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            try {
                $fileName = \uniqid() . \urldecode($fileInfo) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('user_video'),
                    $fileName
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Error on FileUpload : ' . $e->getMessage());

                return $this->redirectToRoute('series_index');
            }
            $series->setUrl($fileName);
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($series);
            $entityManager->flush();

            return $this->redirectToRoute('series_index');
        }

        return $this->render('series/new.html.twig', [
            'series' => $series,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="series_show", methods={"GET"})
     */
    public function show(Series $series, EpisodesRepository $episodesRepository, CommentsRepository $commentsRepository): Response
    {
        $saisons = $episodesRepository->getAllSaison($series);
        $comments = $commentsRepository->findBy(["Series" => $series],[]);
        $commentsNotesASC = $commentsRepository->getCommentByNoteASC();
        $commentsNotesDESC = $commentsRepository->getCommentByNoteDESC();
        $commentsValidated = $commentsRepository->getMoyenneCommentaireValide();
        $moyenne = 0;
        foreach($commentsValidated as $comment){
            $moyenne += $comment->getNote();
        }
        if(sizeof($commentsValidated) != 0)
            $moyenne = $moyenne / sizeof($commentsValidated);
        return $this->render('series/show.html.twig', [
            'series' => $series,
            'saisons' => $saisons,
            'comments' => $comments,
            'commentsNotesASC' => $commentsNotesASC,
            'commentsNotesDESC' => $commentsNotesDESC,
            'moyenne' => $moyenne,
        ]);
    }

    /**
     * @Route("/comments/{id}/validate", name="validate_comment", methods={"GET"})
     */
    public function validateComment(Comments $comments): Response{

        if($this->isGranted('ROLE_ADMIN') === true) {
            if ($comments->getValidated() == 1) {
                $comments->setValidated(0);
            } else {
                $comments->setValidated(1);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($comments);
            $em->flush();
        }
        return $this->redirectToRoute("series_show",["id" => $comments->getSeries()->getId()]);
    }

    /**
     * @Route("/{id}/edit", name="series_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Series $series): Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('series_show',['id'=>$series->getId()]);
        }

        return $this->render('series/edit.html.twig', [
            'series' => $series,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="series_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Series $series): Response
    {
        if ($this->isCsrfTokenValid('delete'.$series->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('series_index');
    }
}
