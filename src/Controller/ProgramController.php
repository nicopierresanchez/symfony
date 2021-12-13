<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Annotation\Route;

/**

 * @Route("/program", name="program_")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")

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
     * The controller for the category add form
     *@ORM\Column(type="string", length=255)

     * @Assert\NotBlank(message="ne me laisse pas tout vide")

     * @Assert\Length(max="255", maxMessage="La catégorie saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     * @Route("/new", name="new")
     */

    public function new(Request $request): Response

    {

        // Create a new Category Object

        $program = new Program();

        // Create the associated Form

        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request

        $form->handleRequest($request);

        // Was the form submitted ?

        if ($form->isSubmitted() && $form->isValid())  {

            // Deal with the submitted data

            // Get the Entity Manager

            $entityManager = $this->getDoctrine()->getManager();

            // Persist Category Object

            $entityManager->persist($program);

            // Flush the persisted object

            $entityManager->flush();

            // Finally redirect to categories list

            return $this->redirectToRoute('program_index');
        }

        // Render the form

        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
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
            'program' => $program,
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
            'program' => $program,
            'season' => $season,
            'episode' => $episode

        ]);
    }
}
