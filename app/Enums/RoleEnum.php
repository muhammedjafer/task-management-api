<?php

namespace App\Enums;

enum RoleEnum: int
{
    case PRODUCT_OWNER = 1;
    case DEVELOPER = 2;
    case TESTER = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getValue($name): int
    {
        try {

            foreach (self::cases() as $case) {
                if ($case->name == $name) {
                    return $case->value;
                }
            }
            return null;

        } catch (\ValueError $e) {
            return null; 
        }
    }

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
