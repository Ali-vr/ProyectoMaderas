<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f5f5f5; color: #222; }
        h1 { margin-bottom: 20px; }
        .actions { margin-bottom: 20px; }
        .btn {
            display: inline-block; padding: 10px 14px; border-radius: 6px;
            text-decoration: none; color: #fff; background: #0d6efd; border: none; cursor: pointer;
            margin-right: 8px; margin-bottom: 8px;
        }
        .btn-secondary { background: #6c757d; }
        .btn-danger { background: #dc3545; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f1f1f1; }
        .empty { background: #fff; border: 1px solid #ddd; padding: 18px; }
        form { display: inline; }
        .link-row { display: inline-block; margin-right: 8px; }
    </style>
</head>
<body>
    <h1>Productos</h1>

    <div class="actions">
        <a class="btn" href="/productos/create">Crear producto</a>
        <?php if (!empty($_SESSION['user_id'] ?? null)): ?>
            <a class="btn btn-secondary" href="/auth/login">Ir al login</a>
        <?php else: ?>
            <a class="btn btn-secondary" href="/auth/login">Iniciar sesión</a>
        <?php endif; ?>
    </div>

    <?php if (empty($productos)): ?>
        <div class="empty">No hay productos registrados.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($producto['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($producto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($producto['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($producto['stock'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="link-row"><a class="btn btn-secondary" href="/productos/<?= urlencode((string) ($producto['id'] ?? '')) ?>">Ver</a></span>
                            <span class="link-row"><a class="btn btn-secondary" href="/productos/update/<?= urlencode((string) ($producto['id'] ?? '')) ?>">Editar</a></span>
                            <span class="link-row">
                                <form action="/productos/<?= urlencode((string) ($producto['id'] ?? '')) ?>" method="POST" onsubmit="return confirm('¿Desea eliminar este producto?');">
                                    <input type="hidden" name="_METHOD" value="DELETE">
                                    <button class="btn btn-danger" type="submit">Eliminar</button>
                                </form>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
