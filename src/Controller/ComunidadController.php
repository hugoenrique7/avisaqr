<?php

namespace App\Controller;

use App\Entity\Comunidad;
use App\Form\ComunidadType;
use App\Repository\ComunidadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/panel/comunidades')]
final class ComunidadController extends AbstractController
{
    #[Route(name: 'app_comunidad_index', methods: ['GET'])]
    public function index(ComunidadRepository $comunidadRepository): Response
    {
        $comunidades = $comunidadRepository->findBy(
            ['administrador' => $this->getUser()],
            ['nombre' => 'ASC']
        );

        return $this->render('comunidad/index.html.twig', [
            'comunidades' => $comunidades,
        ]);
    }

    #[Route('/nueva', name: 'app_comunidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comunidad = new Comunidad();
        $form = $this->createForm(ComunidadType::class, $comunidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comunidad->setAdministrador($this->getUser());

            $entityManager->persist($comunidad);
            $entityManager->flush();

            return $this->redirectToRoute('app_comunidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comunidad/new.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comunidad_show', methods: ['GET'])]
    public function show(Comunidad $comunidad): Response
    {
        $this->comprobarPropietario($comunidad);

        return $this->render('comunidad/show.html.twig', [
            'comunidad' => $comunidad,
        ]);
    }

    #[Route('/{id}/qr', name: 'app_comunidad_qr', methods: ['GET'])]
    public function qr(Comunidad $comunidad): Response
    {
        $this->comprobarPropietario($comunidad);

      
        $zonas = [];
        foreach ($comunidad->getZonas() as $zona) {
            if ($zona->isTieneQr()) {
                $zonas[] = $zona;
            }
        }

        return $this->render('qr/hoja.html.twig', [
            'comunidad' => $comunidad,
            'zonas' => $zonas,
        ]);
    }
    #[Route('/{id}/editar', name: 'app_comunidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comunidad $comunidad, EntityManagerInterface $entityManager): Response
    {
        $this->comprobarPropietario($comunidad);

        $form = $this->createForm(ComunidadType::class, $comunidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comunidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comunidad/edit.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comunidad_delete', methods: ['POST'])]
    public function delete(Request $request, Comunidad $comunidad, EntityManagerInterface $entityManager): Response
    {
        $this->comprobarPropietario($comunidad);

        if ($this->isCsrfTokenValid('delete' . $comunidad->getId(), $request->getPayload()->getString('_token'))) {
            if (count($comunidad->getZonas()) > 0) {
                $this->addFlash('error', 'No se puede borrar "' . $comunidad->getNombre() . '" porque tiene zonas. Borra antes sus zonas.');

                return $this->redirectToRoute('app_comunidad_show', ['id' => $comunidad->getId()], Response::HTTP_SEE_OTHER);
            }

            $entityManager->remove($comunidad);
            $entityManager->flush();
            $this->addFlash('exito', 'Comunidad borrada.');
        }

        return $this->redirectToRoute('app_comunidad_index', [], Response::HTTP_SEE_OTHER);
    }


    private function comprobarPropietario(Comunidad $comunidad): void
    {
        if ($comunidad->getAdministrador() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Esta comunidad no es tuya.');
        }
    }
}
