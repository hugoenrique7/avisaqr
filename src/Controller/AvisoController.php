<?php

namespace App\Controller;

use App\Entity\Aviso;
use App\Entity\Comunidad;
use App\Form\AvisoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvisoController extends AbstractController
{
    #[Route('/panel/comunidades/{id}/avisos/nuevo', name: 'app_aviso_new', methods: ['GET', 'POST'])]
    public function new(Comunidad $comunidad, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($comunidad->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $aviso = new Aviso();
        $aviso->setComunidad($comunidad);

        $form = $this->createForm(AvisoType::class, $aviso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aviso);
            $entityManager->flush();

            $this->addFlash('exito', 'Aviso publicado.');

            return $this->redirectToRoute('app_comunidad_show', ['id' => $comunidad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aviso/new.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }

    #[Route('/panel/avisos/{id}', name: 'app_aviso_delete', methods: ['POST'])]
    public function delete(Aviso $aviso, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comunidad = $aviso->getComunidad();

        if ($comunidad->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $aviso->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($aviso);
            $entityManager->flush();

            $this->addFlash('exito', 'Aviso borrado.');
        }

        return $this->redirectToRoute('app_comunidad_show', ['id' => $comunidad->getId()], Response::HTTP_SEE_OTHER);
    }
}