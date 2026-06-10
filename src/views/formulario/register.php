<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- FORMULARIO -->
<div class="container mt-5 mb-5">
    <div class="card p-4">

 <form action="/formulario/register" method="POST">

    <div class="row mb-3">
        <label class="col-12 col-md-3 col-form-label">Nombre</label>
        <div class="col-12 col-md-9">
            <input type="text" name="nombre" class="form-control" placeholder="Ingrese su nombre" required>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-12 col-md-3 col-form-label">precio</label>
        <div class="col-12 col-md-9">
            <input type="number" name="precio" class="form-control" placeholder="Ingrese su precio" required>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-12 col-md-3 col-form-label">descripcion</label>
        <div class="col-12 col-md-9">
            <input type="text" name="descripcion" class="form-control" placeholder="Ingrese su descripicon" required>
        </div>
    </div>


    <div class="row mb-3">
        <div class="col-12 offset-md-3">
            <button type="submit" class="btn btn-primary">Registrar</button>
        </div>
    </div>

</form>
</body>
</html>