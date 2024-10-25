<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        $nombre = trim($_POST['nombre']);
        $precio_compra = floatval($_POST['precio_compra']);
        $precio_venta = floatval($_POST['precio_venta']);
        $fecha = $_POST['fecha'];
        $cantidad = intval($_POST['cantidad']);
        
        
        $foto_path = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            
            $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_extension;
            $foto_path = $upload_dir . $file_name;
            
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
                
            } else {
                throw new Exception('Error al subir la imagen');
            }
        }
        
        
        $stmt = $pdo->prepare("
            INSERT INTO products (nombre, precio_compra, precio_venta, fecha, cantidad, foto)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $nombre,
            $precio_compra,
            $precio_venta,
            $fecha,
            $cantidad,
            $foto_path
        ]);
        
        
        header('Location: index.php?mensaje=Producto creado exitosamente');
        exit;
        
    } catch (Exception $e) {
        
        header('Location: crear_producto.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}