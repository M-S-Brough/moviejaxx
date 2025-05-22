<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    // Route for the review index page
    #[Route('//show', name: 'app_review')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }

    // Route for editing a review
    #[Route('/review/edit/{id}', name: 'edit_review')]
    public function editReview($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the review entity by its ID
        $reviewRepository = $entityManager->getRepository(Review::class);
        $review = $reviewRepository->find($id);

        // Check if the review exists
        if (!$review) {
            throw $this->createNotFoundException('No review found for id ' . $id);
        }

        // Create a form for editing the review
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the updated review entity to the database
            $entityManager->persist($review);
            $entityManager->flush();

            // Redirect after successful edit, modify the route as needed
            return $this->redirectToRoute('app_movies');
        }

        // Render the edit review form
        return $this->render('review/edit.html.twig', [
            'form' => $form->createView(),
            'review' => $review,
            'title' => 'Edit Review'
        ]);
    }

    // Route for adding a new review
    #[Route('/review/add/{movieId}', name: 'add_review', methods: ['GET', 'POST'])]
    public function addReview(Request $request, EntityManagerInterface $entityManager, $movieId): Response
    {
        // Find the movie entity by its ID
        $movie = $entityManager->getRepository(Movie::class)->find($movieId);

        // Check if the movie exists
        if (!$movie) {
            throw $this->createNotFoundException('Movie not found.');
        }

        // Create a new Review object
        $review = new Review();

        // Create a form for adding a review
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Associate the review with the movie
            $review->setMovie($movie);

            // Set the author of the review (assuming you have a logged-in user)
            if ($this->getUser()) {
                $review->setAuthor($this->getUser());
            }

            // Persist the new review entity to the database
            $entityManager->persist($review);
            $entityManager->flush();

            // Redirect to the movie page or another appropriate page
            return $this->redirectToRoute('app_movies_show-movie', ['id' => $movieId]);
        }

        // Render the add review form
        return $this->render('review/add.html.twig', [
            'form' => $form->createView(),
            'movie' => $movie,
            'title' => 'Add Review'
        ]);
    }
}
