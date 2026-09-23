<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case GERENTE = 'GERENTE';
    case ANALISTA = 'ANALISTA';
    case CLIENTE = 'CLIENTE';

    public static function normalize(string $role): self
    {
        return self::tryFrom(strtoupper($role)) ?? self::CLIENTE;
    }
}
