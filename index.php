<?php
require './classes/AutoLoad.php';
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <meta http-equiv="Cache-control" content="no-cache">
        <link rel="stylesheet" href="css/style.css"/>

        <script src="js/Ajax.js"></script>
        <script src="js/myjavascript.js"></script>

    </head>
    <body>
        <!-- Mi diálogo modal -->
    <dialog id="myDialog">
        <input type="hidden" id="fecha"/>
        <input type="hidden" id="hora"/>
        <h1 id="message">Acciones</h1>
        <div id="control">
            <button id="reservar">RESERVAR</button>
            <button id="eliminar">QUITAR RESERVA</button>
            <button id="cancelar">CANCELAR</button>
        </div>
        <div id="cerrar" class="hidden">
            <button id="btcerrar">CERRAR</button>
        </div>
    </dialog>
    <!-- Fin de mi dialogo-->
    <div id="wrapper" ng-app="calendar" ng-controller="myCtrl">
    </div>
</div> 
</body>
</html>
