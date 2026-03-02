<?php

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=nom_de_ta_db;charset=utf8', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $price) {
        $stmt = $this->db->prepare("INSERT INTO products (name, price) VALUES (?, ?)");
        $stmt->execute([$name, $price]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }
}