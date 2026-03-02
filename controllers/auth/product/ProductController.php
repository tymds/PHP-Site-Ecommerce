<?php
require_once 'models/ProductModel.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }


    public function index() {
        $products = $this->productModel->getAll();
        $this->jsonResponse($products);
    }

    public function show($id) {
        $product = $this->productModel->getById($id);
        if (!$product) {
            $this->jsonResponse(['error' => 'Produit non trouvé'], 404);
        }
        $this->jsonResponse($product);
    }


    public function store() {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;

        if (empty($name) || $price <= 0) {
            $this->jsonResponse(['error' => 'Données invalides'], 400);
        }

        $id = $this->productModel->create($name, $price);
        $this->jsonResponse(['message' => 'Produit créé', 'id' => $id], 201);
    }


    public function delete($id) {
        $this->productModel->delete($id);
        $this->jsonResponse(['message' => 'Produit supprimé']);
    }

    private function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}