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

// Ruta/Vista Listado
$app->get("/productos", function ($request, $response) use ($renderer) {
  return view($renderer, $response, "/productos/index.php");
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
    
    // getParsedBody()
    $data = $request->getParsedBody();

    // busco nombre y contraseña 
    $nombre = $data['nombre'] ?? null;
    $precio = $data['precio'] ?? null;
    $descripcion = $data['descripcion'] ?? null;
    //resultado
    $resultado = [
        "datos_recibidos" => [
            "nombre" => $nombre,
            "precio" => $precio,
            "descripcion" => $descripcion,
        ]
    ];

    //formato JSON 
    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
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

