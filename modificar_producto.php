<?php
require_once 'config.php';

// Obtener el producto específico si se proporciona un ID
$producto = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $producto = $stmt->fetch();
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    try {
        $stmt = $pdo->prepare("UPDATE products SET 
            nombre = ?,
            precio_compra = ?,
            precio_venta = ?,
            fecha = ?,
            cantidad = ?
            WHERE id = ?");
            
        $stmt->execute([
            $_POST['nombre'],
            $_POST['precio_compra'],
            $_POST['precio_venta'],
            $_POST['fecha'],
            $_POST['cantidad'],
            $_POST['id']
        ]);

        // Procesar la foto si se subió una nueva
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_temp = $_FILES['foto']['tmp_name'];
            $foto_nombre = time() . '_' . $_FILES['foto']['name'];
            $foto_destino = 'uploads/' . $foto_nombre;
            
            if (move_uploaded_file($foto_temp, $foto_destino)) {
                $stmt = $pdo->prepare("UPDATE products SET foto = ? WHERE id = ?");
                $stmt->execute([$foto_destino, $_POST['id']]);
            }
        }

        header("Location: index.php?mensaje=Producto actualizado correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "Error al actualizar el producto: " . $e->getMessage();
    }
}

// Obtener todos los productos para la lista
$productos = $pdo->query("SELECT * FROM products ORDER BY id")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUAP - Modificar Producto</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <style>
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 1rem;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            color: #1a1a1a;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }
        
        .menu-item:hover {
            background-color: #e9ecef;
        }
        
        .menu-item svg {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 2rem;
            padding: 1rem;
        }

        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .input-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .input-field {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">BUAP</div>
        <nav>
            <a href="crear_producto.php" class="menu-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear Producto
            </a>
            <a href="modificar_producto.php" class="menu-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modificar Producto
            </a>
            <a href="buscar_producto.php" class="menu-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Buscar
            </a>
            <a href="logout.php" class="menu-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Salir
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-2xl font-bold mb-6">Modificar Producto</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!$producto): ?>
            <!-- Lista de productos para seleccionar -->
            <div class="table-container overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Compra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Venta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prod['id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prod['nombre']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo number_format($prod['precio_compra'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo number_format($prod['precio_venta'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="?id=<?php echo $prod['id']; ?>" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Modificar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Formulario de modificación -->
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">
                    
                    <div class="input-group">
                        <label class="input-label" for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                               class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="precio_compra">Precio de Compra:</label>
                        <input type="number" id="precio_compra" name="precio_compra" 
                               value="<?php echo htmlspecialchars($producto['precio_compra']); ?>" 
                               step="0.01" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="precio_venta">Precio de Venta:</label>
                        <input type="number" id="precio_venta" name="precio_venta" 
                               value="<?php echo htmlspecialchars($producto['precio_venta']); ?>" 
                               step="0.01" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="fecha">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" 
                               value="<?php echo htmlspecialchars($producto['fecha']); ?>" 
                               class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" 
                               value="<?php echo htmlspecialchars($producto['cantidad']); ?>" 
                               class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="foto">Foto:</label>
                        <?php if ($producto['foto']): ?>
                            <img src="<?php echo htmlspecialchars($producto['foto']); ?>" 
                                 alt="Foto actual" class="mb-2 h-32 w-32 object-cover rounded">
                        <?php endif; ?>
                        <input type="file" id="foto" name="foto" class="input-field">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="modificar_producto.php" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancelar
                        </a>
                        <button type="submit" name="actualizar" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>