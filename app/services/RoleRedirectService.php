<?php

namespace App\Services;

use Phalcon\Di\Injectable;

class RoleRedirectService extends Injectable {
    public function getRedirectPathByRole(int $role): string {
        return match ($role) {
            0 => '/dashboard/admin',
            1 => '/dashboard/manager',
            2 => '/dashboard/caregiver',
            default => '/error/unauthorized',
        };
    }

    public function redirectToDashboardByRole(int $role) {
        $path = $this->getRedirectPathByRole($role);
        return $this->response->redirect($path);
    }
}