<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

    #[Route('/pins/{id<[0-9]+>}/edit', name: 'pins_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {


        $form = $this->createForm(PinType::class, $pin);
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

    #[Route('/pins/{id<[0-9]+>}/delete', name: 'pins_delete', methods: ["DELETE"])]
    public function delete(Pin $pin, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('pins_deletion' . $pin->getId(), $request->request->get('csrf_token'))) {
            $em->remove($pin);
            $em->flush();
        }
        $this->addFlash('info', 'Pin successfully deleted!');
        return  $this->redirectToRoute('app_home');
    }

    #[Route('/pins/{id<[0-9]+>}', name: 'pins_show')]
    public function show(Pin $pin): Response
    {


        return $this->render(
            'pins/show.html.twig',
            compact('pin')

        );
    }
}