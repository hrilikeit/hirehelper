<?php

namespace App\Enums;

enum UserRole: string
{
    case Client = 'client';
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case ProjectManager = 'project_manager';
    case SalesManager = 'sales_manager';
    case Freelancer = 'freelancer';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::SuperAdmin => 'Superadmin',
            self::Admin => 'Admin',
            self::ProjectManager => 'Project Manager',
            self::SalesManager => 'Sales Manager',
            self::Freelancer => 'Freelancer',
        };
    }

    public function isInternal(): bool
    {
        return in_array($this, self::internalRoles(), true);
    }

    /**
     * @return array<int, self>
     */
    public static function internalRoles(): array
    {
        return [
            self::SuperAdmin,
            self::Admin,
            self::ProjectManager,
            self::SalesManager,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function internalValues(bool $includeSuperAdmin = true): array
    {
        return array_values(array_map(
            fn (self $role) => $role->value,
            array_filter(
                self::internalRoles(),
                fn (self $role) => $includeSuperAdmin || $role !== self::SuperAdmin,
            ),
        ));
    }

    /**
     * @return array<string, string>
     */
    public static function internalOptions(bool $includeSuperAdmin = true): array
    {
        $options = [];

        foreach (self::internalRoles() as $role) {
            if (! $includeSuperAdmin && $role === self::SuperAdmin) {
                continue;
            }

            $options[$role->value] = $role->label();
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function allOptions(): array
    {
        return [
            self::Client->value => self::Client->label(),
            self::SuperAdmin->value => self::SuperAdmin->label(),
            self::Admin->value => self::Admin->label(),
            self::ProjectManager->value => self::ProjectManager->label(),
            self::SalesManager->value => self::SalesManager->label(),
            self::Freelancer->value => self::Freelancer->label(),
        ];
    }
}
