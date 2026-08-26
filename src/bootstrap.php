<?php

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno desde el .env
Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$env = $_ENV["APP_ENV"] ?? "prod";
$allowedEnvs = ["dev", "prod"];

if (!in_array($env, $allowedEnvs, true)) {
  throw new RuntimeException("APP_ENV inválido: $env");
}

$debug = $env === "dev";

// Crear la aplicacion de Slim
$app = AppFactory::create();

// Crear el motor de plantillas
$renderer = new PhpRenderer(
  templatePath: __DIR__ . "/views",
  attributes: ["title" => "PDI | Slim Template 2026"],
);
//conexion
use PDO;
use RuntimeException;

$app->get("/productos/", function ($request, $response, $args) use ($renderer, $database) {

    // Obtenemos la conexión mediante PDO.
    $conn = $database->getConnection();

    // Consultamos todos los productos de la base de datos.
    $stmt = $conn->query(
        "SELECT id, nombre, precio, stock
         FROM productos
         ORDER BY id"
    );

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
})

// Ruta/Vista Listado
$app->get("/productos", function ($request, $response, $args) use ($renderer) {

 $queryParams = $request->getQueryParams();

    $productos = [
        ["id" => 1, "name" => "Camiseta de futbol", "price" => 15000],
        ["id" => 2, "name" => "Botines", "price" => 45000],
        ["id" => 3, "name" => "Pelota", "price" => 2000],
        ["id" => 4, "name" => "Canilleras", "price" => 5000]
    ];

if (isset($queryParams['limit'])) {
        $limit = $queryParams['limit'];
        $productos = array_slice($productos, 0, $limit);
    }


    return view($renderer, $response, "/productos/index.php", [
        "productos" => $productos
    ]);
});

// Ruta/Vista Detalle 
$app->get("/productos/{id}", function ($request, $response, $args) use ($renderer) {
    $id = $args["id"];
    

    return view($renderer, $response, "/productos/show.php", [
        "id" => $id
    ]);
});

// Ruta/Vista Creacion
$app->get("/create/productos", function ($request, $response) use ($renderer) {
  return view($renderer, $response, "/productos/store.php");
});

// Ruta/formulario 

$app->get("/formulario/register", function ($request, $response) use ($renderer) {
  return view($renderer, $response, "/formulario/register.php");
});  


$app->post("/formulario/register", function ($request, $response) use ($renderer) {
    

    $data = $request->getParsedBody();
  
    $nombre = $data['nombre'] ?? null;
    $precio = $data['precio'] ?? null;
    $descripcion = $data['descripcion'] ?? null;
    $img = $data['img'] ?? null;

    return $renderer->render($response, "/formulario/logeado.php", [
        "nombre" => $nombre,
        "precio" => $precio,
        "descripcion" => $descripcion,
        "img" => $img
    ]);
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
| IMPORTANTE:
| La operación se realiza dentro de una transacción.
| Si algo falla, runTransaction() hace rollback automáticamente.
*/

$app->post("/productos", function ($request, $response, $args) use ($database) {

    // Obtenemos los datos enviados por el formulario.
    $data = $request->getParsedBody();

    $nombre = trim($data["nombre"] ?? "");
    $precio = $data["precio"] ?? null;
    $stock = $data["stock"] ?? null;

    return $database->runTransaction(
        function (PDO $conn) use ($nombre, $precio, $stock) {

            /*
             * Validamos que todos los campos obligatorios
             * hayan sido enviados.
             */
            if ($nombre === "" || $precio === null || $stock === null) {
                throw new RuntimeException(
                    "Todos los campos son obligatorios."
                );
            }

            /*
             * Validamos que precio y stock sean números.
             */
            if (!is_numeric($precio) || !is_numeric($stock)) {
                throw new RuntimeException(
                    "El precio y el stock deben ser numéricos."
                );
            }

            /*
             * Insertamos el producto utilizando una consulta preparada.
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

            return $stmt->rowCount();
        }
    );
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

    $conn = $database->getConnection();

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
     * mostramos la vista de no encontrado.
     */
    if (!$producto) {
        return view(
            $renderer,
            $response,
            "/productos/not_found.php"
        );
    }

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
| Muestra el formulario para editar un producto existente.
*/

$app->get("/productos/update/{id}", function ($request, $response, $args) use ($renderer, $database) {

    $id = (int) $args["id"];

    $conn = $database->getConnection();

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
     * Si el producto no existe,
     * mostramos la vista correspondiente.
     */
    if (!$producto) {
        return view(
            $renderer,
            $response,
            "/productos/not_found.php"
        );
    }

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

    return $database->runTransaction(
        function (PDO $conn) use ($id, $nombre, $precio, $stock) {

            /*
             * Verificamos que todos los campos obligatorios
             * estén completos.
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
             * Verificamos que el producto exista.
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

            return $stmt->rowCount();
        }
    );
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

    return $database->runTransaction(
        function (PDO $conn) use ($id) {

            /*
             * Verificamos que el producto exista.
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
             * Eliminamos el producto.
             */
            $stmt = $conn->prepare(
                "DELETE FROM productos
                 WHERE id = :id"
            );

            $stmt->execute([
                ":id" => $id
            ]);

            return $stmt->rowCount();
        }
    );
});


/**
 * GET /entidad -> Lista a todos los cosos de entidad
 * GET /entidad/{id} -> Mostrar el detalle de un solo coso de entidad
 * POST /entidad -> Crea un coso del tipo entidad
 * PUT|PATCH /entidad/{id} -> Actualiza un coso del tipo entidad
 * DELETE /entidad/{id} -> Borra un coso de entidad especifico
 * composer run serve -> ejecuta el servidor
 */

$app->addErrorMiddleware($debug, true, true);

return $app;

