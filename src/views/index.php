<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carpintería Artesanal - Tienda de Maderas</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand">Navbar</a>
    <form class="d-flex" role="search">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>
  </div>
</nav>
    <header class="main-header">
        <div class="logo">
              <button id="menu-toggle" class="menu-btn">&#9776;</button> 
              <h1>MADERA<span>VIVA</span></h1> 
            </div>
        <nav class="user-nav">
            <a href="/login">Iniciar Sesión</a>
            <a href="/registro">Registrarse</a>
        </nav>
    </header>

    <div class="layout-container">
        <aside class="sidebar">
            <h3>Categorías</h3>
            <ul>
                <li><a href="#mesas">Mesas</a></li>
                <li><a href="#lamparas">Lámparas</a></li>
                <li><a href="#estantes">Estantes</a></li>
                <li><a href="#comedores">Comedores</a></li>
                <li><a href="#carteles">Carteles</a></li>
                <li><a href="#mostradores">Mostradores</a></li>
            </ul>
        </aside>

        <main class="content">
            <h2>Nuestros Productos</h2>
            <p>Selecciona una categoría a la izquierda para ver nuestras creaciones en madera.</p>
            </main>
    </div>
<script src="js/lateral.js"></script>
</body>
</html>
