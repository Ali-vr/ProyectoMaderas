<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de producto</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f5f5f5; color: #222; }
        .card { max-width: 640px; background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        .row { margin-bottom: 12px; }
        .label { display: inline-block; width: 120px; font-weight: bold; }
        .btn {
            display: inline-block; padding: 10px 14px; border-radius: 6px;
            text-decoration: none; color: #fff; background: #0d6efd; border: none; cursor: pointer;
            margin-right: 8px; margin-top: 12px;
        }
        .btn-secondary { background: #6c757d; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Detalle del producto</h1>

        <div class="row"><span class="label">ID:</span> <?= htmlspecialchars((string) ($producto['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="row"><span class="label">Nombre:</span> <?= htmlspecialchars((string) ($producto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="row"><span class="label">Precio:</span> <?= htmlspecialchars((string) ($producto['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="row"><span class="label">Stock:</span> <?= htmlspecialchars((string) ($producto['stock'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>

        <a class="btn btn-secondary" href="/productos">Volver a productos</a>
        <a class="btn" href="/productos/update/<?= urlencode((string) ($producto['id'] ?? '')) ?>">Editar</a>

        <form action="/productos/<?= urlencode((string) ($producto['id'] ?? '')) ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Desea eliminar este producto?');">
            <input type="hidden" name="_METHOD" value="DELETE">
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    </div>
</body>
</html>
