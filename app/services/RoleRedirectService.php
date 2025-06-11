<?php

namespace App\Services;

use Phalcon\Di\Injectable;

class RoleRedirectService extends Injectable {
    public function getRedirectPathByRole(int $role): string {
        return match ($role) {
            0 => '/dashboard/admin',      // Administrator
            1 => '/dashboard/manager',    // Manager
            2 => '/dashboard/caregiver',  // Caregiver
            default => '/signin',         // Unknown role, redirect to login
        };
    }

    public function redirectToDashboardByRole(int $role) {
        $path = $this->getRedirectPathByRole($role);
        return $this->response->redirect($path);
    }
}