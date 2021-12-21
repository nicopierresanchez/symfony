<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Assert\NotBlank(message="ne me laisse pas tout vide")
     * @Assert\Length(max="255", maxMessage="La catégorie saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify): Response

    {

        // Create a new Category Object
        $program = new Program();

        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);

        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $this->flash->createFlash('create');

            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);

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
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Program $program): Response
    {

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
     * @Route("/{slug}/season/{number}", name="show_Season")
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"number": "number"}})
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
     * @Route("/{program}/season/{number}/episode/{slug}", name="show_Episode")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}}) 
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"number": "number"}}) 
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slug": "slug"}})
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
