<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/database/Database.php';

/*
|--------------------------------------------------------------------------
| Variables de entorno
|--------------------------------------------------------------------------
*/

Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$env = $_ENV["APP_ENV"] ?? "prod";

$allowedEnvs = ["dev", "prod"];

if (!in_array($env, $allowedEnvs, true)) {
    throw new RuntimeException("APP_ENV inválido: $env");
}

$debug = $env === "dev";


/*
|--------------------------------------------------------------------------
| Base de datos
|--------------------------------------------------------------------------
*/

$database = new Database();


/*
|--------------------------------------------------------------------------
| Crear aplicación Slim
|--------------------------------------------------------------------------
*/

$app = AppFactory::create();


/*
|--------------------------------------------------------------------------
| Motor de plantillas
|--------------------------------------------------------------------------
*/

$renderer = new PhpRenderer(
    templatePath: __DIR__ . "/views",
    attributes: [
        "title" => "PDI | Slim Template 2026"
    ],
);


/*
|--------------------------------------------------------------------------
| CRUD PRODUCTOS
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| GET /productos/
|--------------------------------------------------------------------------
| Lista todos los productos.
*/

$app->get("/productos/", function ($request, $response, $args) use ($renderer, $database) {

    // Obtenemos la conexión mediante PDO.
    $conn = $database->getConnection();

    // Consultamos todos los productos.
    $stmt = $conn->query(
        "SELECT id, nombre, precio, stock
         FROM productos
         ORDER BY id"
    );

    // Obtenemos los resultados.
    $productos = $stmt->fetchAll();

    // Enviamos los productos a la vista.
    return view(
        $renderer,
        $response,
        "/productos/index.php",
        [
            "productos" => $productos
        ]
    );
});


/*
|--------------------------------------------------------------------------
| GET /productos/create
|--------------------------------------------------------------------------
| Muestra el formulario para crear un producto.
*/

$app->get("/productos/create", function ($request, $response, $args) use ($renderer) {

    return view(
        $renderer,
        $response,
        "/productos/create.php"
    );
});


/*
|--------------------------------------------------------------------------
| POST /productos
|--------------------------------------------------------------------------
| Crea un nuevo producto.
|
| La operación se realiza dentro de una transacción.
| Si algo falla, runTransaction() hace rollback.
*/

$app->post("/productos", function ($request, $response, $args) use ($database) {

    // Obtenemos los datos enviados por el formulario.
    $data = $request->getParsedBody();

    $nombre = trim($data["nombre"] ?? "");
    $precio = $data["precio"] ?? null;
    $stock = $data["stock"] ?? null;

    /*
     * Ejecutamos el INSERT dentro de una transacción.
     */
    $database->runTransaction(
        function (PDO $conn) use ($nombre, $precio, $stock) {

            /*
             * Verificamos que todos los campos
             * obligatorios estén completos.
             */
            if ($nombre === "" || $precio === null || $stock === null) {
                throw new RuntimeException(
                    "Todos los campos son obligatorios."
                );
            }

            /*
             * Verificamos que precio y stock sean números.
             */
            if (!is_numeric($precio) || !is_numeric($stock)) {
                throw new RuntimeException(
                    "El precio y el stock deben ser numéricos."
                );
            }

            /*
             * Verificamos que el precio no sea negativo.
             */
            if ((float) $precio < 0) {
                throw new RuntimeException(
                    "El precio no puede ser negativo."
                );
            }

            /*
             * Verificamos que el stock no sea negativo.
             */
            if ((int) $stock < 0) {
                throw new RuntimeException(
                    "El stock no puede ser negativo."
                );
            }

            /*
             * Insertamos el producto.
             */
            $stmt = $conn->prepare(
                "INSERT INTO productos (nombre, precio, stock)
                 VALUES (:nombre, :precio, :stock)"
            );

            $stmt->execute([
                ":nombre" => $nombre,
                ":precio" => $precio,
                ":stock" => $stock
            ]);
        }
    );

    /*
     * Volvemos al listado.
     */
    return $response
        ->withHeader("Location", "/productos/")
        ->withStatus(302);
});


/*
|--------------------------------------------------------------------------
| GET /productos/{id}
|--------------------------------------------------------------------------
| Muestra el detalle de un producto.
|
| Si el producto no existe, muestra not_found.php.
*/

$app->get("/productos/{id}", function ($request, $response, $args) use ($renderer, $database) {

    $id = (int) $args["id"];

    // Obtenemos la conexión.
    $conn = $database->getConnection();

    // Buscamos el producto.
    $stmt = $conn->prepare(
        "SELECT id, nombre, precio, stock
         FROM productos
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $producto = $stmt->fetch();

    /*
     * Si no encontramos el producto,
     * mostramos la vista correspondiente.
     */
    if (!$producto) {
        return view(
            $renderer,
            $response,
            "/productos/not_found.php"
        );
    }

    // Mostramos el producto.
    return view(
        $renderer,
        $response,
        "/productos/show.php",
        [
            "producto" => $producto
        ]
    );
});


/*
|--------------------------------------------------------------------------
| GET /productos/update/{id}
|--------------------------------------------------------------------------
| Muestra el formulario para editar un producto.
*/

$app->get("/productos/update/{id}", function ($request, $response, $args) use ($renderer, $database) {

    $id = (int) $args["id"];

    // Obtenemos la conexión.
    $conn = $database->getConnection();

    // Buscamos el producto.
    $stmt = $conn->prepare(
        "SELECT id, nombre, precio, stock
         FROM productos
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $producto = $stmt->fetch();

    /*
     * Si no existe, mostramos not_found.php.
     */
    if (!$producto) {
        return view(
            $renderer,
            $response,
            "/productos/not_found.php"
        );
    }

    // Mostramos el formulario de edicion.
    return view(
        $renderer,
        $response,
        "/productos/update.php",
        [
            "producto" => $producto
        ]
    );
});


/*
|--------------------------------------------------------------------------
| PUT /productos/{id}
|--------------------------------------------------------------------------
| Actualiza un producto existente.
|
| La operación se realiza dentro de una transacción.
*/

$app->put("/productos/{id}", function ($request, $response, $args) use ($database) {

    $id = (int) $args["id"];

    // Obtenemos los datos enviados.
    $data = $request->getParsedBody();

    $nombre = trim($data["nombre"] ?? "");
    $precio = $data["precio"] ?? null;
    $stock = $data["stock"] ?? null;

    /*
     * Ejecutamos el UPDATE dentro de una transaccion.
     */
    $database->runTransaction(
        function (PDO $conn) use ($id, $nombre, $precio, $stock) {

            /*
             * Verificamos que todos los campos
             * estén completos.
             */
            if ($nombre === "" || $precio === null || $stock === null) {
                throw new RuntimeException(
                    "Todos los campos son obligatorios."
                );
            }

            /*
             * Verificamos que precio y stock sean numeros.
             */
            if (!is_numeric($precio) || !is_numeric($stock)) {
                throw new RuntimeException(
                    "El precio y el stock deben ser numéricos."
                );
            }

            /*
             * Verificamos que el precio no sea negativo.
             */
            if ((float) $precio < 0) {
                throw new RuntimeException(
                    "El precio no puede ser negativo."
                );
            }

            /*
             * Verificamos que el stock no sea negativo.
             */
            if ((int) $stock < 0) {
                throw new RuntimeException(
                    "El stock no puede ser negativo."
                );
            }

            /*
             * Verificamos que el producto exista
             * antes de actualizarlo.
             */
            $check = $conn->prepare(
                "SELECT id
                 FROM productos
                 WHERE id = :id"
            );

            $check->execute([
                ":id" => $id
            ]);

            if (!$check->fetch()) {
                throw new RuntimeException(
                    "El producto no existe."
                );
            }

            /*
             * Actualizamos el producto.
             */
            $stmt = $conn->prepare(
                "UPDATE productos
                 SET nombre = :nombre,
                     precio = :precio,
                     stock = :stock
                 WHERE id = :id"
            );

            $stmt->execute([
                ":nombre" => $nombre,
                ":precio" => $precio,
                ":stock" => $stock,
                ":id" => $id
            ]);
        }
    );

    /*
     * Volvemos al listado.
     */
    return $response
        ->withHeader("Location", "/productos/")
        ->withStatus(302);
});


/*
|--------------------------------------------------------------------------
| DELETE /productos/{id}
|--------------------------------------------------------------------------
| Elimina un producto.
|
| La operación se realiza dentro de una transacción.
*/

$app->delete("/productos/{id}", function ($request, $response, $args) use ($database) {

    $id = (int) $args["id"];

    /*
     * Ejecutamos el DELETE dentro de una transacción.
     */
    $database->runTransaction(
        function (PDO $conn) use ($id) {

            /*
             * Eliminamos directamente el producto.
             */
            $stmt = $conn->prepare(
                "DELETE FROM productos
                 WHERE id = :id"
            );

            $stmt->execute([
                ":id" => $id
            ]);
        }
    );


    return $response
        ->withHeader("Location", "/productos/")
        ->withStatus(302);
});


$app->addErrorMiddleware($debug, true, true);

return $app;