<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private  $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {

        return $this->render('movies/homepage.html.twig', [
            'title' => 'MovieJaxx',
        ]);
    }

    #[Route('//browse/', name: 'app_movies', methods: ['GET'])]
    public function index(): Response
    {
        // findAll() - SELECT * FROM movies;
        // find() - SELECT * FROM movies WHERE id = 5;
        // findBy() - SELECT * FROM movies ORDER BY runningTime DESC (findBy([],['runningTime' => 'DESC']))
        $repository =$this->entityManager->getRepository(Movie::class);

        $movies = $repository->findAll();


        return $this->render('movies/browse.html.twig', [

            'movies' => $movies,

        ]);

    }

    #[Route('//browse/{id}', name: 'app_movies_show-movie', methods: ['GET'])]
    public function showMovie($id): Response
    {
        $repository =$this->entityManager->getRepository(Movie::class);

        $movies = $repository->find($id);


        return $this->render('movies/show.html.twig', [

            'movie' => $movies,

        ]);

    }






}
