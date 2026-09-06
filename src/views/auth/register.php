<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h1>Registro</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="/auth/register" method="POST">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars((string) ($data['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars((string) ($data['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" minlength="8" required>
        </div>

        <button type="submit">Registrarme</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="/auth/login">Inicia sesión</a></p>
</body>
</html>
