<?php

namespace App\Enum;

enum EstadoIncidencia: string
{
    case Recibida = 'recibida';
    case EnReparacion = 'en_reparacion';
    case Resuelta = 'resuelta';
    case Descartada = 'descartada';
    case Duplicada = 'duplicada';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Recibida => 'Recibida',
            self::EnReparacion => 'En reparación',
            self::Resuelta => 'Resuelta',
            self::Descartada => 'Descartada',
            self::Duplicada => 'Duplicada',
        };
    }
}
