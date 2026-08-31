<?php

declare(strict_types=1);

$productos = $productos ?? [];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Productos</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>Productos</h1>

        <a href="/productos/create" class="btn btn-primary">
            Nuevo producto
        </a>

    </div>

    <?php if (empty($productos)): ?>

        <div class="alert alert-info">
            No hay productos registrados.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-striped table-bordered align-middle">

                <thead class="table-dark">

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

                        <td>
                            <?= htmlspecialchars((string) $producto["id"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($producto["nombre"]) ?>
                        </td>

                        <td>
                            $<?= htmlspecialchars((string) $producto["precio"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars((string) $producto["stock"]) ?>
                        </td>

                        <td>

                            <a
                                href="/productos/<?= $producto["id"] ?>"
                                class="btn btn-sm btn-info"
                            >
                                Ver
                            </a>

                            <a
                                href="/productos/update/<?= $producto["id"] ?>"
                                class="btn btn-sm btn-warning"
                            >
                                Editar
                            </a>

                            <form
                                action="/productos/<?= $producto["id"] ?>"
                                method="POST"
                                class="d-inline"
                            >

                                <input
                                    type="hidden"
                                    name="_METHOD"
                                    value="DELETE"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Seguro que querés eliminar este producto?')"
                                >
                                    Eliminar
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

</body>

</html>