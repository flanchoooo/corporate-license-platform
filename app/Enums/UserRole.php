<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case CorporateAdmin = 'corporate_admin';
    case CorporateViewer = 'corporate_viewer';
}
