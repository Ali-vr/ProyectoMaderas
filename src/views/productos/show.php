<?php

declare(strict_types=1);

$producto = $producto ?? null;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalle del producto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-5">

    <div class="card">

        <div class="card-header">

            <h1 class="h3 mb-0">
                Detalle del producto
            </h1>

        </div>

        <div class="card-body">

            <p>
                <strong>ID:</strong>
                <?= htmlspecialchars((string) $producto["id"]) ?>
            </p>

            <p>
                <strong>Nombre:</strong>
                <?= htmlspecialchars($producto["nombre"]) ?>
            </p>

            <p>
                <strong>Precio:</strong>
                $<?= htmlspecialchars((string) $producto["precio"]) ?>
            </p>

            <p>
                <strong>Stock:</strong>
                <?= htmlspecialchars((string) $producto["stock"]) ?>
            </p>

            <div class="mt-4">

                <a
                    href="/productos/update/<?= $producto["id"] ?>"
                    class="btn btn-warning"
                >
                    Editar
                </a>

                <a
                    href="/productos/"
                    class="btn btn-secondary"
                >
                    Volver
                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>