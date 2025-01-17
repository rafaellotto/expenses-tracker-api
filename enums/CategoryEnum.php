<?php

namespace app\enums;

enum CategoryEnum: string
{
    case FOOD = 'Alimentação';
    case TRANSPORTATION = 'Transporte';
    case LEISURE = 'Lazer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}