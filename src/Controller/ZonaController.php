<?php

namespace App\Controller;

use App\Entity\Comunidad;
use App\Entity\Zona;
use App\Form\ZonaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ZonaController extends AbstractController
{

    #[Route('/panel/comunidades/{id}/zonas/nueva', name: 'app_zona_new', methods: ['GET', 'POST'])]
    public function new(Comunidad $comunidad, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($comunidad->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Esta comunidad no es tuya.');
        }

        $zona = new Zona();
        $zona->setComunidad($comunidad);

        $form = $this->createForm(ZonaType::class, $zona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zona);
            $entityManager->flush();

            return $this->redirectToRoute('app_comunidad_show', ['id' => $comunidad->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('zona/new.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }


    #[Route('/panel/zonas/{id}/editar', name: 'app_zona_edit', methods: ['GET', 'POST'])]
    public function edit(Zona $zona, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->comprobarPropietario($zona);

        $form = $this->createForm(ZonaType::class, $zona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comunidad_show', ['id' => $zona->getComunidad()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('zona/edit.html.twig', [
            'zona' => $zona,
            'form' => $form,
        ]);
    }

    #[Route('/panel/zonas/{id}/qr', name: 'app_zona_qr', methods: ['GET'])]
    public function qr(Zona $zona): Response
    {
        $this->comprobarPropietario($zona);

        if (!$zona->isTieneQr()) {
            throw $this->createNotFoundException();
        }

        return $this->render('qr/hoja.html.twig', [
            'comunidad' => $zona->getComunidad(),
            'zonas' => [$zona],
        ]);
    }
    #[Route('/panel/zonas/{id}', name: 'app_zona_delete', methods: ['POST'])]
    public function delete(Zona $zona, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->comprobarPropietario($zona);

        $idComunidad = $zona->getComunidad()->getId();

        if ($this->isCsrfTokenValid('delete' . $zona->getId(), $request->getPayload()->getString('_token'))) {
            if (count($zona->getIncidencias()) > 0) {
                $this->addFlash('error', 'No se puede borrar la zona "' . $zona->getNombre() . '" porque tiene averías registradas.');
            } else {
                $entityManager->remove($zona);
                $entityManager->flush();
                $this->addFlash('exito', 'Zona borrada.');
            }
        }

        return $this->redirectToRoute('app_comunidad_show', ['id' => $idComunidad], Response::HTTP_SEE_OTHER);
    }
    private function comprobarPropietario(Zona $zona): void
    {
        if ($zona->getComunidad()->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Esta zona no es tuya.');
        }
    }
}
