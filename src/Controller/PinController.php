<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PinController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render(
            'pins/index.html.twig',
            compact('pins')

        );
    }

    #[Route('/pins/create', name: 'pins_create', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') && user.isVerified() ")]

    public function create(Request $request, EntityManagerInterface $em, UserRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $janeDoe = $repo->findOneBy(['email' => 'janedoe@example.com']);
            $pin->setUser($janeDoe);
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render(
            'pins/create.html.twig',
            ['form' => $form->createView()]


        );
    }

    #[Route('/pins/{id<[0-9]+>}/edit', name: 'pins_edit', methods: ['GET', 'PUT'])]
    #[Security("is_granted('PINS_MANAGE', pin)")]

    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {


        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->flush();
            $this->addFlash('error', 'Pin successfully updated!');
            return  $this->redirectToRoute('app_home');
        }

        return $this->render(
            'pins/edit.html.twig',
            [
                'pin' => $pin,
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/pins/{id<[0-9]+>}', name: 'pins_show')]
    public function show(Pin $pin): Response
    {


        return $this->render(
            'pins/show.html.twig',
            compact('pin')

        );
    }
    #[Route('/pins/{id<[0-9]+>}/delete', name: 'pins_delete', methods: ['DELETE'])]
    #[Security("is_granted('PINS_MANAGE', pin)")]

    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {

        if ($this->isCsrfTokenValid('pin' . $pin->getId(), $request->request->get('csrf_token'))) {
            $em->remove($pin);
            $em->flush();
            $this->addFlash('info', 'Pin successfully deleted!');
        }


        return $this->redirectToRoute('app_home');
    }
}
