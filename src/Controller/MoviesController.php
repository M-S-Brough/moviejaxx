<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\MovieFormType;
use App\Form\ReviewFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $movieRepository;
    private $em;

    public function __construct(EntityManagerInterface $entityManager, MovieRepository $movieRepository)
    {
        $this->entityManager = $entityManager;
        $this->movieRepository = $movieRepository;
    }


    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {

        return $this->render('movies/homepage.html.twig', [
            'title' => 'MovieJaxx',
        ]);
    }

    #[Route('//browse/', name: 'app_movies', methods: ['GET'])]
    public function browseMovies(Request $request, PaginatorInterface $paginator): Response
    {
        // findAll() - SELECT * FROM movies;
        // find() - SELECT * FROM movies WHERE id = 5;
        // findBy() - SELECT * FROM movies ORDER BY runningTime DESC (findBy([],['runningTime' => 'DESC']))
        $queryBuilder = $this->entityManager->getRepository(Movie::class)
            ->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC'); // Adjust the query as needed

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Default to page 1
            6 // Number of items per page
        );

        return $this->render('movies/browse.html.twig', [
            'title' => 'Browse',
            'pagination' => $pagination
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

            // Handling multiple reviews
            foreach ($newMovie->getReviews() as $review) {
                $review->setMovie($newMovie); // Set the relationship
                $this->entityManager->persist($review);
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

            $movieId = $newMovie->getId();
            return $this->redirectToRoute('app_movies_show-movie', ['id' => $movieId]);
        }

        return $this->render('movies/create.html.twig', [
            'title' => 'Create New Movie',
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

    #[Route('/search', name: 'search_movies')]
    public function search(Request $request): Response
    {
        $searchTerm = $request->query->get('query');
        $results = $this->movieRepository->searchMovies($searchTerm);

        return $this->render('movies/search_results.html.twig', [
            'search' => $searchTerm, // Pass the search term to the template
            'movies' => $results,
        ]);
    }







}
