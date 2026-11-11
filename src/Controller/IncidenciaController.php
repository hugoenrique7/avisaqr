<?php

namespace App\Controller;

use App\Entity\Incidencia;
use App\Enum\EstadoIncidencia;
use App\Form\IncidenciaType;
use App\Repository\IncidenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panel/averias')]
final class IncidenciaController extends AbstractController
{
    #[Route(name: 'app_incidencia_index', methods: ['GET'])]
    public function index(IncidenciaRepository $incidenciaRepository): Response
    {
        return $this->render('incidencia/index.html.twig', [
            'incidencias' => $incidenciaRepository->findDelAdministrador($this->getUser()),
        ]);
    }

    #[Route('/{id}', name: 'app_incidencia_show', methods: ['GET'])]
    public function show(Incidencia $incidencia): Response
    {
        if ($incidencia->getZona()->getComunidad()->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('incidencia/show.html.twig', [
            'incidencia' => $incidencia,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_incidencia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Incidencia $incidencia, EntityManagerInterface $entityManager): Response
    {
        if ($incidencia->getZona()->getComunidad()->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(IncidenciaType::class, $incidencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($incidencia->getEstado() === EstadoIncidencia::Resuelta) {
                if ($incidencia->getFechaResolucion() === null) {
                    $incidencia->setFechaResolucion(new \DateTimeImmutable());
                }
            } else {
                $incidencia->setFechaResolucion(null);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_incidencia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('incidencia/edit.html.twig', [
            'incidencia' => $incidencia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_incidencia_delete', methods: ['POST'])]
    public function delete(Request $request, Incidencia $incidencia, EntityManagerInterface $entityManager): Response
    {
        if ($incidencia->getZona()->getComunidad()->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $incidencia->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($incidencia);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_incidencia_index', [], Response::HTTP_SEE_OTHER);
    }
}