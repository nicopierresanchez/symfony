<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**

 * @Route("/program", name="program_")

 */

class ProgramController extends AbstractController

{
    /**
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response

    {

        $programs = $this->getDoctrine()

            ->getRepository(Program::class)

            ->findAll();


        return $this->render(

            'program/index.html.twig',

            ['programs' => $programs]

        );
    }

    /**
     * Getting a program by id
     *
     * @Route("/show/{id<^[0-9]+$>}", name="show")
     * @return Response
     */

    public function show(Program $program): Response

    {
        if (!$program) {

            throw $this->createNotFoundException(

                'No program with id : ' . $program . ' found in program\'s table.'

            );
        }
        $seasonNumbers = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(
                ['program' => $program],
            );


        return $this->render('program/show.html.twig', [

            'seasonNumbers' => $seasonNumbers,
            'program' => $program,

        ]);
    }

    /**
     * Getting a category by id
     *
     * @Route("/{program}/season/{season}", name="show_Season")
     * 
     * @return Response
     */

    public function showSeason(Program $program, Season $season): Response

    {
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(
                ['season' => $season],
            );

        if (!$episodes) {
            throw $this->createNotFoundException(
                'No product found for id: ' . $season
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program'=> $program,
            'season' => $season,
            'episode' => $episodes

        ]);
    }
    /**
     * Getting a episode 
     *
     * @Route("/{program}/season/{season}/episode/{episode}", name="show_Episode")
     * 
     * @return Response
     */

    public function showEpisode(Program $program, Season $season, Episode $episode): Response

    {

        return $this->render('program/episode_show.html.twig', [
            'program'=> $program,
            'season' => $season,
            'episode' => $episode

        ]);
    }
}