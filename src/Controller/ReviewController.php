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
    #[Route('//show', name: 'app_review')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }

    #[Route('/review/edit/{id}', name: 'edit_review')]
    public function editReview($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Use the entity manager to get the Review repository
        $reviewRepository = $entityManager->getRepository(Review::class);
        $review = $reviewRepository->find($id);

        if (!$review) {
            throw $this->createNotFoundException('No review found for id ' . $id);
        }

        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            // Redirect after successful edit, modify the route as needed
            return $this->redirectToRoute('app_movies');
        }

        return $this->render('review/edit.html.twig', [
            'form' => $form->createView(),
            'review' => $review,
            'title' => 'Edit Review'
        ]);
    }

    #[Route('/review/add/{movieId}', name: 'add_review', methods: ['GET', 'POST'])]
    public function addReview(Request $request, EntityManagerInterface $entityManager, $movieId): Response
    {
        $movie = $entityManager->getRepository(Movie::class)->find($movieId);
        if (!$movie) {
            throw $this->createNotFoundException('Movie not found.');
        }

        $review = new Review();
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setMovie($movie); // Associate the review with the movie

            // Set the author of the review (assuming you have a logged-in user)
            if ($this->getUser()) {
                $review->setAuthor($this->getUser());
            }

            $entityManager->persist($review);
            $entityManager->flush();

            // Redirect to the movie page or another appropriate page
            return $this->redirectToRoute('app_movies_show-movie', ['id' => $movieId]);
        }

        return $this->render('review/add.html.twig', [
            'form' => $form->createView(),
            'movie' => $movie,
            'title' => 'Add Review'
        ]);
    }
}
