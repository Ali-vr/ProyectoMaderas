<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear producto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-5">

    <div class="card">

        <div class="card-header">
            <h1 class="h3 mb-0">Crear producto</h1>
        </div>

        <div class="card-body">

            <form action="/productos" method="POST">

                <div class="mb-3">

                    <label for="nombre" class="form-label">
                        Nombre
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nombre"
                        name="nombre"
                        maxlength="100"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label for="precio" class="form-label">
                        Precio
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="precio"
                        name="precio"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label for="stock" class="form-label">
                        Stock
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="stock"
                        name="stock"
                        min="0"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar producto
                </button>

                <a
                    href="/productos/"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>