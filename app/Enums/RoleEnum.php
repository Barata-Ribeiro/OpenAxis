<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'super-admin';
    case User = 'user';
    case Sales = 'sales';
    case Purchasing = 'purchasing';
    case Inventory = 'inventory';
    case CatalogManager = 'catalog-manager';
    case Finance = 'finance';
    case Auditor = 'auditor';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admins',
            self::User => 'Users',
            self::Sales => 'Sales',
            self::Purchasing => 'Purchasing',
            self::Inventory => 'Inventory',
            self::CatalogManager => 'Catalog Managers',
            self::Finance => 'Finance',
            self::Auditor => 'Auditors',
        };
    }
}
