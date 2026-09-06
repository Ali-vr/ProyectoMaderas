<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f5f5f5; color: #222; }
        .card { max-width: 600px; margin: 0 auto; background: #fff; border: 1px solid #ddd; padding: 24px; border-radius: 8px; }
        .field { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input { width: 100%; padding: 10px; box-sizing: border-box; }
        .actions { display: flex; gap: 10px; margin-top: 18px; }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 6px; text-decoration: none; color: #fff; background: #0d6efd; border: none; cursor: pointer; }
        .btn-secondary { background: #6c757d; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Crear nuevo producto</h1>

        <form action="/productos" method="POST">
            <div class="field">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" required>
            </div>

            <div class="field">
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required>
            </div>

            <div class="field">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" min="0" required>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Guardar producto</button>
                <a class="btn btn-secondary" href="/productos">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
