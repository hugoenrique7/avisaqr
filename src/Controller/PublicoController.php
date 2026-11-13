<?php

namespace App\Controller;

use App\Entity\Afectado;
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
use App\Entity\Comunidad;

use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;



final class PublicoController extends AbstractController
{
    public function __construct(
        private RateLimiterFactoryInterface $pinLimiter,
        private RateLimiterFactoryInterface $averiasLimiter,
    ) {}

    #[Route('/q/{id}', name: 'app_publico_qr', methods: ['GET', 'POST'])]
    public function qr(Zona $zona, Request $request, IncidenciaRepository $incidenciaRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$zona->isTieneQr()) {
            throw $this->createNotFoundException();
        }

        $respuesta = $this->pedirPin($zona->getComunidad(), $request);

        if ($respuesta) {
            return $respuesta;
        }

        $incidencia = new Incidencia();
        $form = $this->createForm(IncidenciaVecinoType::class, $incidencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->puedeEnviar($form, $request)) {


            $incidencia->setZona($zona);

            $codigo = $this->nuevoCodigo($incidenciaRepository);
            $incidencia->setCodigo($codigo);
            $this->guardarFoto($form, $incidencia);
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
    #[Route('/c/{id}', name: 'app_publico_comunidad', methods: ['GET', 'POST'])]
    public function comunidad(Comunidad $comunidad, Request $request, IncidenciaRepository $incidenciaRepository, EntityManagerInterface $entityManager): Response
    {
        $respuesta = $this->pedirPin($comunidad, $request);

        if ($respuesta) {
            return $respuesta;
        }

        $incidencia = new Incidencia();
        $form = $this->createForm(IncidenciaVecinoType::class, $incidencia, ['comunidad' => $comunidad]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->puedeEnviar($form, $request)) {
            $codigo = $this->nuevoCodigo($incidenciaRepository);
            $incidencia->setCodigo($codigo);
            $this->guardarFoto($form, $incidencia);

            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('codigo', $codigo);

            return $this->redirectToRoute('app_publico_comunidad', ['id' => $comunidad->getId()]);
        }

        $abiertas = [];

        foreach ($incidenciaRepository->findDeComunidad($comunidad) as $averia) {
            if (in_array($averia->getEstado(), [EstadoIncidencia::Recibida, EstadoIncidencia::EnReparacion])) {
                $abiertas[] = $averia;
            }
        }

        return $this->render('publico/comunidad.html.twig', [
            'comunidad' => $comunidad,
            'abiertas' => $abiertas,
            'form' => $form,
        ]);
    }


    #[Route('/afecta/{id}', name: 'app_publico_afecta', methods: ['POST'])]
    public function meAfecta(Incidencia $incidencia, Request $request, EntityManagerInterface $entityManager): Response
    {
        $zona = $incidencia->getZona();
        $session = $request->getSession();

        if (!$session->get('pin_comunidad_' . $zona->getComunidad()->getId())) {
            throw $this->createAccessDeniedException();
        }

        $clave = 'afecta_' . $incidencia->getId();
        $abierta = in_array($incidencia->getEstado(), [EstadoIncidencia::Recibida, EstadoIncidencia::EnReparacion]);

        if ($abierta && !$session->get($clave) && $this->isCsrfTokenValid('afecta' . $incidencia->getId(), $request->getPayload()->getString('_token'))) {
            $afectado = new Afectado();
            $afectado->setIncidencia($incidencia);

            $entityManager->persist($afectado);
            $entityManager->flush();

            $session->set($clave, true);
        }

        if ($request->getPayload()->getString('volver') === 'comunidad') {
            return $this->redirectToRoute('app_publico_comunidad', ['id' => $zona->getComunidad()->getId()]);
        }

        return $this->redirectToRoute('app_publico_qr', ['id' => $zona->getId()]);
    }
    #[Route('/q/{id}/historial', name: 'app_publico_historial', methods: ['GET'])]
    #[Route('/c/{id}/historial', name: 'app_publico_historial', methods: ['GET', 'POST'])]
    public function historial(Comunidad $comunidad, Request $request, IncidenciaRepository $incidenciaRepository): Response
    {
        $respuesta = $this->pedirPin($comunidad, $request);

        if ($respuesta) {
            return $respuesta;
        }

        $codigo = strtoupper(trim($request->query->get('codigo', '')));

        if ($codigo !== '') {
            $incidencias = [];
            $incidencia = $incidenciaRepository->findOneBy(['codigo' => $codigo]);

            if ($incidencia && $incidencia->getZona()->getComunidad() === $comunidad) {
                $incidencias = [$incidencia];
            }
        } else {
            $incidencias = $incidenciaRepository->findDeComunidad($comunidad);
        }

        return $this->render('publico/historial.html.twig', [
            'comunidad' => $comunidad,
            'incidencias' => $incidencias,
            'codigo' => $codigo,
        ]);
    }
    #[Route('/reabrir/{id}', name: 'app_publico_reabrir', methods: ['POST'])]
    public function reabrir(Incidencia $incidencia, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();

        if (!$session->get('pin_comunidad_' . $incidencia->getZona()->getComunidad()->getId())) {
            throw $this->createAccessDeniedException();
        }

        $clave = 'reabrir_' . $incidencia->getId();

        if ($incidencia->getEstado() === EstadoIncidencia::Resuelta && !$session->get($clave) && $this->isCsrfTokenValid('reabrir' . $incidencia->getId(), $request->getPayload()->getString('_token'))) {
            $incidencia->setEstado(EstadoIncidencia::Recibida);
            $incidencia->setFechaResolucion(null);
            $incidencia->setCuantidadReabierta($incidencia->getCuantidadReabierta() + 1);

            $entityManager->flush();

            $session->set($clave, true);
        }

        return $this->redirectToRoute('app_publico_historial', ['id' => $incidencia->getZona()->getComunidad()->getId()]);
    }

    private function pedirPin(Comunidad $comunidad, Request $request): ?Response
    {
        $session = $request->getSession();
        $clave = 'pin_comunidad_' . $comunidad->getId();

        if ($session->get($clave)) {
            return null;
        }

        $form = $this->createForm(PinType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $limite = $this->pinLimiter->create($request->getClientIp());

            if (!$limite->consume()->isAccepted()) {
                $form->get('pin')->addError(new FormError('Demasiados intentos. Esperar 10 min.'));
            } elseif ($form->get('pin')->getData() === $comunidad->getPin()) {
                $session->set($clave, true);

                return $this->redirect($request->getUri());
            } else {
                $form->get('pin')->addError(new FormError('El PIN no es correcto.'));
            }
        }

        return $this->render('publico/pin.html.twig', [
            'comunidad' => $comunidad,
            'form' => $form,
        ]);
    }
    private function nuevoCodigo(IncidenciaRepository $incidenciaRepository): string
    {
        do {
            $codigo = strtoupper(bin2hex(random_bytes(3)));
        } while ($incidenciaRepository->findOneBy(['codigo' => $codigo]));

        return $codigo;
    }

    private function guardarFoto($form, Incidencia $incidencia): void
    {
        $archivo = $form->get('foto')->getData();

        if ($archivo) {
            $nombre = bin2hex(random_bytes(8)) . '.' . $archivo->guessExtension();
            $archivo->move($this->getParameter('kernel.project_dir') . '/public/uploads', $nombre);
            $incidencia->setFoto($nombre);
        }
    }

    private function puedeEnviar($form, Request $request): bool
    {
        $limite = $this->averiasLimiter->create($request->getClientIp());

        if (!$limite->consume()->isAccepted()) {
            $form->addError(new FormError('Has enviado demasiados avisos. Inténtalo más tarde.'));

            return false;
        }

        return true;
    }
}
