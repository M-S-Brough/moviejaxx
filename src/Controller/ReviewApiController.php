<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;

class ReviewApiController extends AbstractController
{
    // Route for listing all reviews
    #[Route('/api/reviews', name: 'api_reviews_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Fetch all reviews from the database
        $reviews = $entityManager->getRepository(Review::class)->findAll();
        // Serialize the reviews to JSON
        $jsonContent = $serializer->serialize($reviews, 'json');
        // Return a JSON response with the list of reviews
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    // Route for retrieving a review by ID
    #[Route('/api/reviews/{id}', name: 'api_reviews_get_by_id', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Find the review by ID
        $review = $entityManager->getRepository(Review::class)->find($id);
        // Check if the review exists
        if (!$review) {
            // Return a JSON response with a 404 status code if the review is not found
            return $this->json(['message' => 'Review not found'], 404);
        }
        // Serialize the review to JSON
        $jsonContent = $serializer->serialize($review, 'json');
        // Return a JSON response with the review data
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    // Route for creating a new review
    #[Route('/api/reviews', name: 'api_reviews_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Decode the JSON request body into an associative array
        $reviewData = json_decode($request->getContent(), true);
        // Create a new Review object
        $review = new Review();
        // Set the review properties based on the request data
        $review->setTitle($reviewData['title'] ?? null);
        $review->setReviewText($reviewData['reviewText'] ?? null);

        // Fetch and set the associated movie
        if (isset($reviewData['movieId'])) {
            $movie = $entityManager->getRepository(Movie::class)->find($reviewData['movieId']);
            if (!$movie) {
                return $this->json(['message' => 'Movie not found'], 404);
            }
            $review->setMovie($movie);
        } else {
            return $this->json(['message' => 'Movie ID is required'], 400);
        }

        // Set the author to the current authenticated user
        $user = $this->getUser();  // Retrieve the currently authenticated user
        if ($user) {
            $review->setAuthor($user);
        } else {
            return $this->json(['message' => 'User must be authenticated to post a review'], 401);
        }

        // Persist the new review entity to the database
        $entityManager->persist($review);
        $entityManager->flush();

        // Serialize the created review to JSON
        $jsonContent = $serializer->serialize($review, 'json');
        // Return a JSON response with the created review data and a 201 status code
        return new Response($jsonContent, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    // Route for updating an existing review
    #[Route('/api/reviews/{id}', name: 'api_reviews_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response {
        // Find the review by ID
        $review = $entityManager->getRepository(Review::class)->find($id);
        // Check if the review exists
        if (!$review) {
            // Return a JSON response with a 404 status code if the review is not found
            return $this->json(['message' => 'Review not found'], 404);
        }

        // Decode the JSON request body into an associative array
        $reviewData = json_decode($request->getContent(), true);

        // Check if the movieId is set and valid
        if (isset($reviewData['movieId'])) {
            $movie = $entityManager->getRepository(Movie::class)->find($reviewData['movieId']);
            if (!$movie) {
                return $this->json(['message' => 'Movie not found'], 404);
            }
            $review->setMovie($movie); // Reassociate the review with a new movie if given
        }

        // Update the review properties based on the request data
        $review->setTitle($reviewData['title'] ?? $review->getTitle());
        $review->setReviewText($reviewData['reviewText'] ?? $review->getReviewText());

        // Persist the updated review entity to the database
        $entityManager->flush();

        // Serialize the updated review to JSON
        $jsonContent = $serializer->serialize($review, 'json');
        // Return a JSON response with the updated review data
        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    // Route for deleting a review
    #[Route('/api/reviews/{id}', name: 'api_reviews_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response {
        // Find the review by ID
        $review = $entityManager->getRepository(Review::class)->find($id);

        // Check if the review exists
        if (!$review) {
            // Return a JSON response with a 404 status code if the review is not found
            return $this->json(['message' => 'Review not found'], 404);
        }

        // Optionally, check if the logged-in user is the author of the review
        // or if the user has admin rights to delete any review
        $user = $this->getUser();
        if ($user !== $review->getAuthor() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            // Return a JSON response with a 403 status code if the user is not authorized to delete the review
            return $this->json(['message' => 'Unauthorized to delete this review'], 403);
        }

        // Remove the review entity from the database
        $entityManager->remove($review);
        $entityManager->flush();

        // Return a JSON response with a success message
        return $this->json(['message' => 'Review deleted successfully']);
    }
}
