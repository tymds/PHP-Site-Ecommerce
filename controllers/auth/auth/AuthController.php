<?php
require_once 'models/auth/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $username = $_POST['username'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->jsonResponse(['error' => 'Données manquantes'], 400);
        }

        try {
            $userId = $this->userModel->createUser($email, $password, $username);
            return $this->jsonResponse(['message' => 'User créé', 'id' => $userId], 201);
        } catch (Exception $e) {

            return $this->jsonResponse(['error' => $e->getMessage()], 409);
        }
    }

    private function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}