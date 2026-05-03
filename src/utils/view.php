<?php

use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface;

/**
 * Renderiza una vista con un layout intermedio opcional, envuelto en el  layout base.
 *
 * Flujo de renderizado:
 *   $template → [$layout →] layouts/base.php → $response
 *
 * @param  PhpRenderer          $renderer  La instancia de PhpRenderer.
 * @param  ResponseInterface    $response  La respuesta HTTP para escribir la información.
 * @param  string               $template  Ruta  a la plantilla de la vista (relativa al directorio views).
 * @param  array<string, mixed> $data      Variables con las cuales rellenar la plantilla y layout. Por defecto: [].
 * @param  string|null          $layout    Ruta a un layout intermedio (e.g. "layouts/app.php"). Si es null, la plantilla $template es renderizada directamente en el layout base.
 *
 * @return ResponseInterface La respuesta HTTP con el contenido renderizado escrito en el cuerpo.
 */
function view(
  PhpRenderer $renderer,
  ResponseInterface $response,
  string $template,
  array $data = [],
  ?string $layout = null,
): ResponseInterface {
  $page = $renderer->fetch($template, $data);

  $content = $layout
    ? $renderer->fetch($layout, [...$data, "content" => $page])
    : $page;

  return $renderer->render($response, "layouts/base.php", [
    ...$data,
    "content" => $content,
  ]);
}
