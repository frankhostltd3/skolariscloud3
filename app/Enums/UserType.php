<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case GENERAL_STAFF = 'general_staff';
    case TEACHING_STAFF = 'teaching_staff';
    case STUDENT = 'student';
    case PARENT = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::GENERAL_STAFF => 'General Staff',
            self::TEACHING_STAFF => 'Teaching Staff',
            self::STUDENT => 'Student',
            self::PARENT => 'Parent',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }

    public function viewPath(): string
    {
        return 'dashboards.' . $this->value;
    }
}
