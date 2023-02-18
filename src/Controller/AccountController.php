<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



#[Route("/account")]
#[IsGranted("ROLE_USER")]
class AccountController extends AbstractController
{
    #[Route('', name: 'app_account', methods: "GET")]
    public function show(): Response
    {
        return $this->render('account/show.html.twig', []);
    }

    #[Route("/edit", name: "app_account_edit", methods: ["GET", "POST"])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Account updated successfully');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig', ['form' => $form->createView()]);
    }


    #[Route("/change-password", name: "app_account_change_password", methods: ["GET", "POST"])]
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, null, ['current_password_required' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form['plainPassword']->getData()));
            $em->flush();
            $this->addFlash('success', 'Password updated successfully');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/change-password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
