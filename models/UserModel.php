<?php

<?php

class UserModel {
    private $db;

    public function __construct() {
        // 1. Connexion à la base de données
        // Remplace 'nom_de_ta_bdd', 'root' et '' par tes vrais identifiants
        try {
            $this->db = new PDO(
                'mysql:host=localhost;dbname=db_exam;charset=utf8', 
                'root', 
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] 
            );
        } catch (Exception $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }


    public function createUser($email, $password, $username) {

        if ($this->emailExists($email)) {
            throw new Exception("Cet email est déjà utilisé par un autre compte.");
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (email, password, username, created_at) 
                  VALUES (:email, :password, :username, NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'email'    => $email,
            'password' => $hashedPassword,
            'username' => $username
        ]);

        return $this->db->lastInsertId();
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    private function emailExists($email) {
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() !== false;
    }
}