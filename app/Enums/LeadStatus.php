<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NOVO = 'novo';
    case CONTATO = 'contato';
    case NEGOCIACAO = 'negociacao';
    case CONVERTIDO = 'convertido';
    case PERDIDO = 'perdido';

    public static function normalize(mixed $status): self
    {
        $normalized = strtolower((string) $status);

        return match ($normalized) {
            'novo', 'new' => self::NOVO,
            'contato', 'contacted' => self::CONTATO,
            'negociacao', 'qualified' => self::NEGOCIACAO,
            'convertido', 'converted' => self::CONVERTIDO,
            'perdido', 'lost' => self::PERDIDO,
            default => self::NOVO,
        };
    }
}
