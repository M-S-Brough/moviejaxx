<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use App\Services\MovieApiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    // Dependency injection for services and repositories
    private EntityManagerInterface $entityManager;
    private MovieRepository $movieRepository;
    private MovieApiService $movieApiService;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, MovieRepository $movieRepository, MovieApiService $movieApiService)
    {
        // Assigning dependencies to class properties
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->movieRepository = $movieRepository;
        $this->movieApiService = $movieApiService;
    }

    // Homepage route
    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        return $this->render('movies/homepage.html.twig', [
            'title' => 'MovieJaxx',
        ]);
    }

    // Browse movies route
    #[Route('/browse/', name: 'app_movies', methods: ['GET'])]
    public function browseMovies(Request $request, PaginatorInterface $paginator): Response
    {
        // Building a query for movie browsing
        $queryBuilder = $this->entityManager->getRepository(Movie::class)
            ->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC'); // Adjust the query as needed

        // Paginating the results
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

    // Create movie route
    #[Route('/movies/create/', name: 'create_movie')]
    public function createMovie(Request $request): Response
    {
        // Creating a new movie form
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handling form submission
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

            // Persisting the new movie entity to the database
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

    // Show movie details route
    #[Route('//browse/{id}', name: 'app_movies_show-movie', methods: ['GET'])]
    public function showMovie($id): Response
    {
        $repository = $this->entityManager->getRepository(Movie::class);
        $movies = $repository->find($id);

        return $this->render('movies/show.html.twig', [
            'movie' => $movies,
        ]);
    }

    // Search movies route
    #[Route('/search', name: 'search_movies')]
    public function search(Request $request): Response
    {
        // Searching for movies based on the query term
        $searchTerm = $request->query->get('query');
        $results = $this->movieRepository->searchMovies($searchTerm);

        return $this->render('movies/search_results.html.twig', [
            'search' => $searchTerm,
            'movies' => $results,
        ]);
    }

    // Search movie via API route
    #[Route('/search/movie', name: 'search_movie', methods: ['GET'])]
    public function searchMovie(Request $request): JsonResponse
    {
        // Searching for movies via an external API
        $query = $request->query->get('term');
        $results = $this->movieApiService->searchMovies($query);

        return $this->json([
            'results' => $results['results'] ?? []
        ]);
    }

    // Get movie details from API route
    #[Route('/movie/details/{id}', name: 'movie_details', methods: ['GET'])]
    public function getMovieDetails(int $id): JsonResponse
    {
        // Logging the request for movie details
        $this->logger->info('Fetching details for movie ID: ' . $id);

        // Fetching movie details from an external API
        $movieDetails = $this->movieApiService->fetchMovieDetailsById($id);

        // Checking for errors in the fetched details
        if (array_key_exists('error', $movieDetails)) {
            $this->logger->error('Failed to fetch movie details', [
                'id' => $id,
                'error' => $movieDetails['error']
            ]);
            return $this->json(['error' => 'Failed to fetch movie details'], Response::HTTP_BAD_REQUEST);
        }

        // Logging successful fetch of movie details
        $this->logger->info('Successfully fetched movie details', [
            'id' => $id,
            'details' => $movieDetails
        ]);

        return $this->json($movieDetails);
    }
}
