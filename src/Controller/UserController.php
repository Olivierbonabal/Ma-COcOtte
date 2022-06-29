<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        // dd($user);
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() === $user) {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les Informations de votre Compte ont bien été mises à jour.'
                );

                return $this->redirectToRoute('recipe.index');
            } else {

                $this->addFlash(
                    'warning',
                    'Le Mot de Passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(User $user, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserPasswordType::class);

        // je lui passe la requete
        $form->handleRequest($request);
        //si le formulaire a été soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword()['plainPassword'])) {
                $user->setPlainPassword(
                    $form->getData()->getNewPassword());

                    // autre méthode Hash

            // if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
            //     $user->setPlainPassword(
            //         $hasher->setPassword(
            //             $user,
            //             $form->getData()['newPassword']
            //         )
            //     );

                $this->addFlash(
                    'success',
                    'Le Mot de Passe a été modifié .'
                );

                return $this->redirectToRoute('recipe.index');
            } else {

                $this->addFlash(
                    'warning',
                    'Le Mot de Passe renseigné est incorrect.'
                );
            }

            return $this->render('pages/user/edit_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}
