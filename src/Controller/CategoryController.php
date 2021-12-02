<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {

        $categories = $this->getDoctrine()

            ->getRepository(Category::class)

            ->findAll();


        return $this->render(

            'category/index.html.twig',

            ['categories' => $categories]

        );
    }
    /**

     * Getting a category by id

     *

     * @Route("/show/{categoryName}", name="show")

     * @return Response

     */

    public function show(string $categoryName): Response

    {       
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(
                ['name' => $categoryName],
            );

        if (!$category) {
            throw $this->createNotFoundException(
                'No product found for id: ' . $categoryName
            );
        }
        $categoryPrograms = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
            );



        return $this->render('category/show.html.twig', [

            'categoryPrograms' => $categoryPrograms,
            'categoryName' => $category->getName(),

        ]);
    }
}
