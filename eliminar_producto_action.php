<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = (int)$_POST['id'];
        
        // Primero obtenemos la información de la foto para eliminarla si existe
        $stmt = $pdo->prepare("SELECT foto FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        
        if ($producto && $producto['foto']) {
            // Eliminar el archivo de la foto si existe
            if (file_exists($producto['foto'])) {
                unlink($producto['foto']);
            }
        }
        
        // Eliminar el registro de la base de datos
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        echo json_encode(['success' => $result]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}