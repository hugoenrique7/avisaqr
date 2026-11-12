<?php

namespace App\Controller;

use App\Entity\Incidencia;
use App\Entity\Zona;
use App\Enum\EstadoIncidencia;
use App\Form\IncidenciaVecinoType;
use App\Form\PinType;
use App\Repository\IncidenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicoController extends AbstractController
{
    #[Route('/q/{id}', name: 'app_publico_qr', methods: ['GET', 'POST'])]
    public function qr(Zona $zona, Request $request, IncidenciaRepository $incidenciaRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$zona->isTieneQr()) {
            throw $this->createNotFoundException();
        }

        $comunidad = $zona->getComunidad();
        $session = $request->getSession();
        $clave = 'pin_comunidad_' . $comunidad->getId();

        if (!$session->get($clave)) {
            $form = $this->createForm(PinType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('pin')->getData() === $comunidad->getPin()) {
                    $session->set($clave, true);

                    return $this->redirectToRoute('app_publico_qr', ['id' => $zona->getId()]);
                }

                $form->get('pin')->addError(new FormError('El PIN no es correcto.'));
            }

            return $this->render('publico/pin.html.twig', [
                'zona' => $zona,
                'form' => $form,
            ]);
        }

        $incidencia = new Incidencia();
        $form = $this->createForm(IncidenciaVecinoType::class, $incidencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $incidencia->setZona($zona);

            do {
                $codigo = strtoupper(bin2hex(random_bytes(3)));
            } while ($incidenciaRepository->findOneBy(['codigo' => $codigo]));

            $incidencia->setCodigo($codigo);

            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('codigo', $codigo);

            return $this->redirectToRoute('app_publico_qr', ['id' => $zona->getId()]);
        }

        $abiertas = $incidenciaRepository->findBy(
            [
                'zona' => $zona,
                'estado' => [EstadoIncidencia::Recibida->value, EstadoIncidencia::EnReparacion->value],
            ],
            ['fechaCreacion' => 'DESC']
        );

        return $this->render('publico/qr.html.twig', [
            'zona' => $zona,
            'abiertas' => $abiertas,
            'form' => $form,
        ]);
    }
}