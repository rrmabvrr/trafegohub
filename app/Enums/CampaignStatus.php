<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case ACTIVE = 'ACTIVE';
    case PAUSED = 'PAUSED';
    case ENDED = 'ENDED';
    case DRAFT = 'DRAFT';
    case ERROR = 'ERROR';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativa',
            self::PAUSED => 'Pausada',
            self::ENDED => 'Encerrada',
            self::DRAFT => 'Rascunho',
            self::ERROR => 'Erro',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40',
            self::PAUSED => 'bg-amber-500/20 text-amber-400 border-amber-500/40',
            self::ENDED => 'bg-slate-800 text-slate-400 border-slate-700',
            self::DRAFT => 'bg-blue-500/20 text-blue-400 border-blue-500/40',
            self::ERROR => 'bg-rose-500/20 text-rose-400 border-rose-500/40',
        };
    }

    public static function tryFromKey(?string $value): self
    {
        if (! $value) {
            return self::ACTIVE;
        }

        $normalized = strtoupper(trim($value));

        return match ($normalized) {
            'ATIVA', 'ACTIVE' => self::ACTIVE,
            'PAUSADA', 'PAUSED' => self::PAUSED,
            'ENCERRADA', 'ENDED', 'COMPLETED', 'ARCHIVED' => self::ENDED,
            'RASCUNHO', 'DRAFT' => self::DRAFT,
            'ERRO', 'ERROR' => self::ERROR,
            default => self::tryFrom($normalized) ?? self::ACTIVE,
        };
    }
}
