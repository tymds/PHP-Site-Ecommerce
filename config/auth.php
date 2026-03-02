<?php
declare(strict_types=1);

// Démarre la session si elle n’est pas déjà active
function start_app_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Vérifie si l’utilisateur est connecté
function is_authenticated(): bool {
    start_app_session();
    return isset($_SESSION['user_id']); // Vrai si l’utilisateur a un ID en session
}