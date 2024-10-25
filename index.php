<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $eliminar_id = $_POST['eliminar_id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $eliminar_id]);

    // Redirigir con un mensaje de éxito
    header("Location: index.php?mensaje=Producto eliminado correctamente");
    exit();
}

$productos = $pdo->query("SELECT * FROM products ORDER BY id")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUAP - Sistema de Productos</title>
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

        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .action-button {
            padding: 4px 8px;
            background-color: #dc2626;
            color: white;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .action-button:hover {
            background-color: #b91c1c;
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
        <h1 class="text-2xl font-bold mb-6">Eliminar Producto</h1>
        
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
        <?php endif; ?>

        <div class="table-container overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Compra</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Venta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($producto['id']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo number_format($producto['precio_compra'], 2); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo number_format($producto['precio_venta'], 2); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($producto['fecha']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($producto['foto']): ?>
                                <img src="<?php echo htmlspecialchars($producto['foto']); ?>" alt="Foto del producto" class="h-10 w-10 object-cover rounded">
                            <?php else: ?>
                                <span class="text-gray-400">Sin foto</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="confirmarEliminar(<?php echo $producto['id']; ?>)" 
                                    class="action-button">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script>
    function confirmarEliminar(id) {
        if (confirm('¿Está seguro de que desea eliminar este producto?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'eliminar_id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>