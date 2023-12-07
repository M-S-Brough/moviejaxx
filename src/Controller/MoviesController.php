<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private  $entityManager;
    private $em;

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
    #[Route('/movies/create/', name: 'create_movie')]
    public function createMovie(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData(); // Get the Movie entity populated with form data

            // Handle newReview data
            $newReviewData = $form->get('newReview')->getData();
            if ($newReviewData) {
                $newMovie->setReview($newReviewData); // Associate the review with the movie
                $this->entityManager->persist($newReviewData); // Persist the new review
            }

            // Handle image upload
            $imagePath = $form->get('image')->getData();
            if ($imagePath) {
                $newFilmName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/build/images',
                        $newFilmName
                    );
                } catch (FileException $e) {
                    // Handle exceptions if something goes wrong during file upload
                    return new Response($e->getMessage());
                }

                $newMovie->setImage('/build/images/' . $newFilmName);
            }

            $this->entityManager->persist($newMovie);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_movies');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
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
