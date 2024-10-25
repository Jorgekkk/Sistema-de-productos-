<?php
require_once 'config.php';

// Inicializar variables
$productos = [];
$busqueda_realizada = false;
$criterio = isset($_GET['criterio']) ? $_GET['criterio'] : '';
$termino = isset($_GET['termino']) ? $_GET['termino'] : '';
$precio_min = isset($_GET['precio_min']) ? $_GET['precio_min'] : '';
$precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : '';

// Procesar la búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($criterio)) {
    $busqueda_realizada = true;
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    switch ($criterio) {
        case 'nombre':
            if (!empty($termino)) {
                $sql .= " AND nombre LIKE ?";
                $params[] = "%{$termino}%";
            }
            break;
            
        case 'id':
            if (!empty($termino)) {
                $sql .= " AND id = ?";
                $params[] = $termino;
            }
            break;
            
        case 'precio':
            if (!empty($precio_min)) {
                $sql .= " AND precio_venta >= ?";
                $params[] = $precio_min;
            }
            if (!empty($precio_max)) {
                $sql .= " AND precio_venta <= ?";
                $params[] = $precio_max;
            }
            break;
            
        case 'fecha':
            if (!empty($termino)) {
                $sql .= " AND fecha = ?";
                $params[] = $termino;
            }
            break;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $productos = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error en la búsqueda: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUAP - Buscar Productos</title>
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

        .search-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .results-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
        <h1 class="text-2xl font-bold mb-6">Buscar Productos</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de búsqueda -->
        <div class="search-container">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="input-group">
                        <label class="input-label" for="criterio">Búsqueda por:</label>
                        <select id="criterio" name="criterio" class="input-field" onchange="toggleSearchFields()">
                            <option value="">Seleccione un criterio</option>
                            <option value="nombre" <?php echo $criterio === 'nombre' ? 'selected' : ''; ?>>Nombre</option>
                            <option value="id" <?php echo $criterio === 'id' ? 'selected' : ''; ?>>ID</option>
                            <option value="precio" <?php echo $criterio === 'precio' ? 'selected' : ''; ?>>Rango de Precio</option>
                            <option value="fecha" <?php echo $criterio === 'fecha' ? 'selected' : ''; ?>>Fecha</option>
                        </select>
                    </div>

                    <div id="termino-container" class="input-group">
                        <label class="input-label" for="termino">Término de búsqueda:</label>
                        <input type="text" id="termino" name="termino" value="<?php echo htmlspecialchars($termino); ?>" 
                               class="input-field">
                    </div>

                    <div id="precio-container" class="input-group hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label" for="precio_min">Precio mínimo:</label>
                                <input type="number" id="precio_min" name="precio_min" 
                                       value="<?php echo htmlspecialchars($precio_min); ?>" 
                                       step="0.01" class="input-field">
                            </div>
                            <div>
                                <label class="input-label" for="precio_max">Precio máximo:</label>
                                <input type="number" id="precio_max" name="precio_max" 
                                       value="<?php echo htmlspecialchars($precio_max); ?>" 
                                       step="0.01" class="input-field">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Resultados de la búsqueda -->
        <?php if ($busqueda_realizada): ?>
            <div class="results-container">
                <?php if (empty($productos)): ?>
                    <div class="p-4 text-center text-gray-500">
                        No se encontraron productos que coincidan con los criterios de búsqueda.
                    </div>
                <?php else: ?>
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
                                        <img src="<?php echo htmlspecialchars($producto['foto']); ?>" 
                                             alt="Foto del producto" class="h-10 w-10 object-cover rounded">
                                    <?php else: ?>
                                        <span class="text-gray-400">Sin foto</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script>
    function toggleSearchFields() {
        const criterio = document.getElementById('criterio').value;
        const terminoContainer = document.getElementById('termino-container');
        const precioContainer = document.getElementById('precio-container');
        
        // Ocultar todos los campos primero
        terminoContainer.classList.add('hidden');
        precioContainer.classList.add('hidden');
        
        // Mostrar los campos correspondientes según el criterio seleccionado
        if (criterio === 'precio') {
            precioContainer.classList.remove('hidden');
        } else if (criterio !== '') {
            terminoContainer.classList.remove('hidden');
            
            // Ajustar el tipo de input según el criterio
            const terminoInput = document.getElementById('termino');
            if (criterio === 'fecha') {
                terminoInput.type = 'date';
            } else if (criterio === 'id') {
                terminoInput.type = 'number';
            } else {
                terminoInput.type = 'text';
            }
        }
    }

    // Ejecutar al cargar la página para mantener el estado del formulario
    document.addEventListener('DOMContentLoaded', toggleSearchFields);
    </script>
</body>
</html>