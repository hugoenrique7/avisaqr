<?php

namespace App\Controller;

use App\Entity\Zona;
use App\Form\PinType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicoController extends AbstractController
{
    #[Route('/q/{id}', name: 'app_publico_qr', methods: ['GET', 'POST'])]
    public function qr(Zona $zona, Request $request): Response
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

        return $this->render('publico/qr.html.twig', [
            'zona' => $zona,
        ]);
    }
}