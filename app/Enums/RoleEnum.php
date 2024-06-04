<?php

namespace App\Enums;

enum RoleEnum: int
{
    case PRODUCT_OWNER = 1;
    case DEVELOPER = 2;
    case TESTER = 3;

    public function getLabel(): string
    {
        $label = [
            self::PRODUCT_OWNER => 'Product owner',
            self::DEVELOPER => 'Developer',
            self::TESTER => 'Tester'
        ];

        return $label[$this->value];
    }
}
