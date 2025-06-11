<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SessiondebugController extends BaseController {

    /**
     * Debug session state
     */
    public function indexAction() {
        // Disable view rendering
        $this->view->disable();

        header('Content-Type: text/plain');

        echo "=== SESSION DEBUG ===\n\n";

        echo "Session exists: " . ($this->session->exists() ? 'YES' : 'NO') . "\n";
        echo "Session ID: " . session_id() . "\n";
        echo "Session status: " . session_status() . "\n\n";

        echo "Session contents:\n";
        if ($this->session->exists()) {
            $keys = $this->session->getKeys();
            foreach ($keys as $key) {
                $value = $this->session->get($key);
                if (is_string($value) && strlen($value) > 100) {
                    $value = substr($value, 0, 100) . '...';
                }
                echo "  $key: " . print_r($value, true) . "\n";
            }
        } else {
            echo "  (no session)\n";
        }

        echo "\nAuthentication status:\n";
        echo "  Is authenticated: " . ($this->isAuthenticated() ? 'YES' : 'NO') . "\n";
        echo "  User ID: " . $this->getUserId() . "\n";
        echo "  Username: " . $this->getUsername() . "\n";
        echo "  User role: " . $this->getUserRole() . "\n";

        echo "\n===================\n";

        echo "\nTo clear session, visit: /sessiondebug/clear\n";
    }

    /**
     * Clear session
     */
    public function clearAction() {
        // Disable view rendering
        $this->view->disable();

        header('Content-Type: text/plain');

        echo "=== CLEARING SESSION ===\n\n";

        // Force session destruction
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Clear Phalcon session
        if ($this->session->exists()) {
            $keys = $this->session->getKeys();
            foreach ($keys as $key) {
                $this->session->remove($key);
            }
            $this->session->destroy();
        }

        echo "Session cleared!\n";
        echo "You can now visit /signin\n";
    }
}