<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MovieApiController extends AbstractController
{
    // Route for listing all movies
    #[Route('/api/movies', name: 'api_movies_list', methods: ['GET'])]
    public function listMovies(EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Retrieve all movies from the database
        $movies = $entityManager->getRepository(Movie::class)->findAll();
        // Serialize the movies to JSON
        $jsonContent = $serializer->serialize($movies, 'json');
        // Return a JSON response with the list of movies
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    // Route for retrieving a movie by ID
    #[Route('/api/movies/{id}', name: 'api_movies_get_by_id', methods: ['GET'])]
    public function showMovie(int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Find the movie by ID
        $movie = $entityManager->getRepository(Movie::class)->find($id);
        // Check if the movie exists
        if (!$movie) {
            // Return a JSON response with a 404 status code if the movie is not found
            return $this->json(['message' => 'Movie not found'], 404);
        }
        // Serialize the movie to JSON
        $jsonContent = $serializer->serialize($movie, 'json');
        // Return a JSON response with the movie data
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    // Route for creating a new movie
    #[Route('/api/movies', name: 'api_movies_create', methods: ['POST'])]
    public function createMovie(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Decode the JSON request body into an associative array
        $movieData = json_decode($request->getContent(), true);
        // Create a new Movie object and set its properties based on the request data
        $movie = new Movie();
        $movie->setTitle($movieData['title']);
        $movie->setReleaseYear($movieData['releaseYear']);
        $movie->setDirector($movieData['director']);
        $movie->setCast($movieData['cast'] ?? null);
        $movie->setImage($movieData['image']);
        $movie->setRunningTime($movieData['runningTime']);

        // Persist the new movie entity to the database
        $entityManager->persist($movie);
        $entityManager->flush();

        // Serialize the created movie to JSON
        $jsonContent = $serializer->serialize($movie, 'json');
        // Return a JSON response with the created movie data and a 201 status code
        return new Response($jsonContent, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    // Route for updating an existing movie
    #[Route('/api/movies/{id}', name: 'api_movies_update', methods: ['PUT'])]
    public function updateMovie(int $id, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Find the movie by ID
        $movie = $entityManager->getRepository(Movie::class)->find($id);
        // Check if the movie exists
        if (!$movie) {
            // Return a JSON response with a 404 status code if the movie is not found
            return $this->json(['message' => 'Movie not found'], 404);
        }

        // Decode the JSON request body into an associative array
        $movieData = json_decode($request->getContent(), true);
        // Update the movie entity's properties based on the request data
        $movie->setTitle($movieData['title']);
        $movie->setReleaseYear($movieData['releaseYear']);
        $movie->setDirector($movieData['director']);
        $movie->setCast($movieData['cast'] ?? null);
        $movie->setImage($movieData['image']);
        $movie->setRunningTime($movieData['runningTime']);

        // Persist the updated movie entity to the database
        $entityManager->flush();

        // Serialize the updated movie to JSON
        $jsonContent = $serializer->serialize($movie, 'json');
        // Return a JSON response with the updated movie data
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    // Route for deleting a movie
    #[Route('/api/movies/{id}', name: 'api_movies_delete', methods: ['DELETE'])]
    public function deleteMovie(int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Find the movie by ID
        $movie = $entityManager->getRepository(Movie::class)->find($id);
        // Check if the movie exists
        if (!$movie) {
            // Return a JSON response with a 404 status code if the movie is not found
            return $this->json(['message' => 'Movie not found'], 404);
        }

        // Remove the movie entity from the database
        $entityManager->remove($movie);
        $entityManager->flush();

        // Return a JSON response indicating that the movie was deleted
        return $this->json(['message' => 'Movie deleted']);
    }
}
