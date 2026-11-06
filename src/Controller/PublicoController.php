<?php

namespace App\Controller;

use App\Entity\Zona;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicoController extends AbstractController
{
    // Página que abre el QR de cada zona. De momento es provisional:

    #[Route('/q/{id}', name: 'app_publico_qr', methods: ['GET'])]
    public function qr(Zona $zona): Response
    {
        // Las zonas sin QR, como el tejado, no tienen esta página
        if (!$zona->isTieneQr()) {
            throw $this->createNotFoundException();
        }

        return $this->render('publico/qr.html.twig', [
            'zona' => $zona,
        ]);
    }
}
