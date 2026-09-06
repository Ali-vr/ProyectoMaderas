<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database/database.php';

use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Psr7\Response as SlimResponse;
use Slim\Views\PhpRenderer;

Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$env = $_ENV['APP_ENV'] ?? 'prod';
$allowedEnvs = ['dev', 'prod'];

if (!in_array($env, $allowedEnvs, true)) {
    throw new \RuntimeException("APP_ENV inválido: {$env}");
}

$debug = $env === 'dev';
$app = AppFactory::create();
$app->add(new MethodOverrideMiddleware());

$database = new Database();
$renderer = new PhpRenderer(
    templatePath: __DIR__ . '/views',
    attributes: ['title' => 'PDI | Slim Template 2026'],
);

function ensureSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function redirectTo(string $path, int $status = 302): Response
{
    return (new SlimResponse())
        ->withHeader('Location', $path)
        ->withStatus($status);
}

function authMiddleware(Request $request, RequestHandler $handler): Response
{
    ensureSession();

    $userId = $_SESSION['user_id'] ?? null;

    if ($userId === null || $userId === '') {
        return redirectTo('/auth/login');
    }

    $request = $request->withAttribute('user_id', $userId);

    return $handler->handle($request);
}

function logMiddleware(Request $request, RequestHandler $handler): Response
{
    $start = microtime(true);
    $response = $handler->handle($request);

    $elapsedMs = (microtime(true) - $start) * 1000;
    $timestamp = date('Y-m-d H:i:s');
    $method = $request->getMethod();
    $path = $request->getUri()->getPath();
    $statusCode = $response->getStatusCode();

    $logLine = sprintf(
        '[%s] %s %s %d %.2f ms',
        $timestamp,
        $method,
        $path,
        $statusCode,
        $elapsedMs
    );

    error_log($logLine);

    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir) && !mkdir($logDir, 0777, true) && !is_dir($logDir)) {
        throw new \RuntimeException('No se pudo crear el directorio de logs.');
    }

    file_put_contents($logDir . '/app.log', $logLine . PHP_EOL, FILE_APPEND);

    return $response;
}

$app->add(function (Request $request, RequestHandler $handler): Response {
    return logMiddleware($request, $handler);
});

function renderRegisterView(
    PhpRenderer $renderer,
    Response $response,
    string $template,
    array $data = [],
    array $errors = []
): Response {
    return view($renderer, $response, $template, [
        'errors' => $errors,
        'data' => $data,
    ]);
}

function handleRegisterRequest(
    Request $request,
    Response $response,
    PhpRenderer $renderer,
    Database $database,
    string $template
): Response {
    $method = $request->getMethod();

    if ($method === 'GET') {
        return renderRegisterView($renderer, $response, $template, [], []);
    }

    $body = $request->getParsedBody() ?? [];
    $nombre = trim((string) ($body['nombre'] ?? ''));
    $email = trim((string) ($body['email'] ?? ''));
    $password = (string) ($body['password'] ?? '');
    $errors = [];

    if ($nombre === '') {
        $errors[] = 'El nombre es obligatorio.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email es obligatorio y debe ser válido.';
    }

    if ($password === '' || strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if ($errors === []) {
        try {
            $conn = $database->getConnection();
            $check = $conn->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $check->execute([':email' => $email]);

            if ($check->fetch() !== false) {
                $errors[] = 'Ya existe un usuario registrado con ese email.';
            }
        } catch (\Throwable $e) {
            $errors[] = 'No se pudo validar la existencia del usuario.';
        }
    }

    if ($errors !== []) {
        return renderRegisterView($renderer, $response, $template, [
            'nombre' => $nombre,
            'email' => $email,
        ], $errors);
    }

    try {
        $conn = $database->getConnection();
        $stmt = $conn->prepare(
            'INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)'
        );

        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return redirectTo('/auth/login');
    } catch (\Throwable $e) {
        return renderRegisterView($renderer, $response, $template, [
            'nombre' => $nombre,
            'email' => $email,
        ], ['No se pudo completar el registro. Intenta nuevamente.']);
    }
}

function handleLoginRequest(
    Request $request,
    Response $response,
    PhpRenderer $renderer,
    Database $database,
    string $template
): Response {
    $method = $request->getMethod();

    if ($method === 'GET') {
        return view($renderer, $response, $template, ['errors' => []]);
    }

    $body = $request->getParsedBody() ?? [];
    $email = trim((string) ($body['email'] ?? ''));
    $password = (string) ($body['password'] ?? '');
    $errors = [];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email es obligatorio y debe ser válido.';
    }

    if ($password === '') {
        $errors[] = 'La contraseña es obligatoria.';
    }

    if ($errors === []) {
        try {
            $conn = $database->getConnection();
            $stmt = $conn->prepare(
                'SELECT id, nombre, password FROM usuarios WHERE email = :email LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user !== false && password_verify($password, $user['password'])) {
                ensureSession();
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];

                return redirectTo('/productos');
            }

            $errors[] = 'Credenciales inválidas.';
        } catch (\Throwable $e) {
            $errors[] = 'No se pudo iniciar sesión en este momento.';
        }
    }

    return view($renderer, $response, $template, [
        'errors' => $errors,
        'data' => ['email' => $email],
    ]);
}

$app->get('/auth/register', function ($request, $response) use ($renderer, $database) {
    return handleRegisterRequest($request, $response, $renderer, $database, '/auth/register.php');
});

$app->post('/auth/register', function ($request, $response) use ($renderer, $database) {
    return handleRegisterRequest($request, $response, $renderer, $database, '/auth/register.php');
});

$app->get('/auth/login', function ($request, $response) use ($renderer, $database) {
    return handleLoginRequest($request, $response, $renderer, $database, '/auth/login.php');
});

$app->post('/auth/login', function ($request, $response) use ($renderer, $database) {
    return handleLoginRequest($request, $response, $renderer, $database, '/auth/login.php');
});

$app->get('/formulario/register', function ($request, $response) use ($renderer, $database) {
    return redirectTo('/auth/register');
});

$app->post('/formulario/register', function ($request, $response) use ($renderer, $database) {
    return redirectTo('/auth/register');
});

$app->get('/productos', function ($request, $response) use ($renderer, $database) {
    $conn = $database->getConnection();
    $stmt = $conn->query('SELECT id, nombre, precio, stock FROM productos ORDER BY id');
    $productos = $stmt->fetchAll();

    return view($renderer, $response, '/productos/index.php', [
        'productos' => $productos,
    ]);
});

$app->get('/productos/', function ($request, $response) {
    return redirectTo('/productos');
});

$app->get('/productos/create', function ($request, $response) use ($renderer) {
    return view($renderer, $response, '/productos/create.php');
})->add(function (Request $request, RequestHandler $handler): Response {
    return authMiddleware($request, $handler);
});

$app->post('/productos', function ($request, $response) use ($database) {
    $data = $request->getParsedBody() ?? [];
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $precio = $data['precio'] ?? null;
    $stock = $data['stock'] ?? null;

    try {
        $database->runTransaction(function (\PDO $conn) use ($nombre, $precio, $stock) {
            if ($nombre === '' || $precio === null || $stock === null) {
                throw new \RuntimeException('Todos los campos son obligatorios.');
            }

            if (!is_numeric($precio) || !is_numeric($stock)) {
                throw new \RuntimeException('El precio y el stock deben ser numéricos.');
            }

            $stmt = $conn->prepare(
                'INSERT INTO productos (nombre, precio, stock) VALUES (:nombre, :precio, :stock)'
            );

            $stmt->execute([
                ':nombre' => $nombre,
                ':precio' => $precio,
                ':stock' => $stock,
            ]);

            return $stmt->rowCount();
        });

        return redirectTo('/productos');
    } catch (\Throwable $e) {
        $response = new SlimResponse();
        $response->getBody()->write('No se pudo crear el producto.');
        return $response->withStatus(400);
    }
})->add(function (Request $request, RequestHandler $handler): Response {
    return authMiddleware($request, $handler);
});

$app->get('/productos/{id}', function ($request, $response, $args) use ($renderer, $database) {
    $id = (int) $args['id'];
    $conn = $database->getConnection();
    $stmt = $conn->prepare('SELECT id, nombre, precio, stock FROM productos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        return view($renderer, $response, '/productos/not_found.php');
    }

    return view($renderer, $response, '/productos/show.php', [
        'producto' => $producto,
    ]);
});

$app->get('/productos/update/{id}', function ($request, $response, $args) use ($renderer, $database) {
    $id = (int) $args['id'];
    $conn = $database->getConnection();
    $stmt = $conn->prepare('SELECT id, nombre, precio, stock FROM productos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        return view($renderer, $response, '/productos/not_found.php');
    }

    return view($renderer, $response, '/productos/update.php', [
        'producto' => $producto,
    ]);
})->add(function (Request $request, RequestHandler $handler): Response {
    return authMiddleware($request, $handler);
});

$app->put('/productos/{id}', function ($request, $response, $args) use ($database) {
    $id = (int) $args['id'];
    $data = $request->getParsedBody() ?? [];
    $nombre = trim((string) ($data['nombre'] ?? ''));
    $precio = $data['precio'] ?? null;
    $stock = $data['stock'] ?? null;

    try {
        $database->runTransaction(function (\PDO $conn) use ($id, $nombre, $precio, $stock) {
            if ($nombre === '' || $precio === null || $stock === null) {
                throw new \RuntimeException('Todos los campos son obligatorios.');
            }

            if (!is_numeric($precio) || !is_numeric($stock)) {
                throw new \RuntimeException('El precio y el stock deben ser numéricos.');
            }

            $check = $conn->prepare('SELECT id FROM productos WHERE id = :id');
            $check->execute([':id' => $id]);
            if ($check->fetch() === false) {
                throw new \RuntimeException('El producto no existe.');
            }

            $stmt = $conn->prepare(
                'UPDATE productos SET nombre = :nombre, precio = :precio, stock = :stock WHERE id = :id'
            );

            $stmt->execute([
                ':nombre' => $nombre,
                ':precio' => $precio,
                ':stock' => $stock,
                ':id' => $id,
            ]);

            return $stmt->rowCount();
        });

        return redirectTo('/productos');
    } catch (\Throwable $e) {
        $response = new SlimResponse();
        $response->getBody()->write('No se pudo actualizar el producto.');
        return $response->withStatus(400);
    }
})->add(function (Request $request, RequestHandler $handler): Response {
    return authMiddleware($request, $handler);
});

$app->delete('/productos/{id}', function ($request, $response, $args) use ($database) {
    $id = (int) $args['id'];

    try {
        $database->runTransaction(function (\PDO $conn) use ($id) {
            $check = $conn->prepare('SELECT id FROM productos WHERE id = :id');
            $check->execute([':id' => $id]);
            if ($check->fetch() === false) {
                throw new \RuntimeException('El producto no existe.');
            }

            $stmt = $conn->prepare('DELETE FROM productos WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        });

        return redirectTo('/productos');
    } catch (\Throwable $e) {
        $response = new SlimResponse();
        $response->getBody()->write('No se pudo eliminar el producto.');
        return $response->withStatus(400);
    }
})->add(function (Request $request, RequestHandler $handler): Response {
    return authMiddleware($request, $handler);
});

$app->addErrorMiddleware($debug, true, true);

return $app;

