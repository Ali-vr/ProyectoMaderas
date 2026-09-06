<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Productos</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin:40px;
            background:#f4f4f4;
        }
        h2{
            text-align:center;
        }
        form{
            background:#fff;
            padding:20px;
            border-radius:8px;
            width:350px;
            margin:auto;
            box-shadow:0 0 10px rgba(0,0,0,.2);
        }
        input{
            width:100%;
            padding:10px;
            margin:8px 0;
        }
        button{
            padding:10px 15px;
            margin:5px;
            cursor:pointer;
        }
        table{
            width:80%;
            margin:30px auto;
            border-collapse:collapse;
            background:#fff;
        }
        table, th, td{
            border:1px solid #ccc;
        }
        th, td{
            padding:10px;
            text-align:center;
        }
    </style>
</head>
<body>

<h2>CRUD DE PRODUCTOS</h2>

<form id="formProducto">
    <input type="hidden" id="id">

    <label>Nombre</label>
    <input type="text" id="nombre" placeholder="Nombre del producto">

    <label>Precio</label>
    <input type="number" id="precio" placeholder="Precio">

    <label>Stock</label>
    <input type="number" id="stock" placeholder="Stock">

    <button type="button" onclick="agregar()">Agregar</button>
    <button type="button" onclick="actualizar()">Actualizar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="tablaProductos">
        <!-- Aquí se cargarán los datos desde la base de datos -->
    </tbody>
</table>



</body>
</html>