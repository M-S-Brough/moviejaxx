<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    // Route for user registration
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $formAuthenticator): Response
    {
        // Creating a new User instance
        $user = new User();
        // Creating a registration form using RegistrationFormType
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoding the plain password using the provided UserPasswordHasherInterface
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Persisting the user entity to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Authenticating the user after registration
            $userAuthenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request
            );

            // Redirecting the user to the movies page after successful registration
            return $this->redirectToRoute('app_movies');
        }

        // Rendering the registration form
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => 'Register'
        ]);
    }
}
