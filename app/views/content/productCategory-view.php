<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle"><i class="fas fa-boxes fa-fw"></i> &nbsp; Productos por categoría</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        use app\controllers\productController;
        $insProducto = new productController();
    ?>
    <div class="columns">



        <div class="column is-one-third">
            <h2 class="title has-text-centered">Categorías</h2>
            <?php

                $datos_categorias=$insProducto->seleccionarDatos("Normal","categoria","*",0);

                if($datos_categorias->rowCount()>0){
                    $datos_categorias=$datos_categorias->fetchAll();
                    foreach($datos_categorias as $row){
                        echo '<a href="'.APP_URL.$url[0].'/'.$row['categoria_id'].'/" class="button is-link is-inverted is-fullwidth">'.$row['categoria_nombre'].'</a>';
                    }
                }else{
                    echo '<p class="has-text-centered" >No hay categorías registradas</p>';
                }
            ?>
        </div>



        <div class="column pb-6">
    <?php
        // Obtener el valor de categoria_id desde la URL y asegurarse de que sea válido
        $categoria_id = (isset($url[1]) && is_numeric($url[1]) && $url[1] > 0) ? (int)$url[1] : 0;

        // Verificar si el ID de la categoría es válido (mayor que 0)
        if ($categoria_id > 0) {
            // Llamar a la función seleccionarDatos solo si el ID es válido
            $categoria = $insProducto->seleccionarDatos("Unico", "categoria", "categoria_id", $categoria_id);
            
            if ($categoria->rowCount() > 0) {
                $categoria = $categoria->fetch();
                
                // Mostrar los datos de la categoría si se encontró
                echo '
                    <h2 class="title has-text-centered">' . $categoria['categoria_nombre'] . '</h2>
                    <p class="has-text-centered pb-6">' . $categoria['categoria_ubicacion'] . '</p>
                ';

                // Llamar a listarProductoControlador si la categoría es válida
                echo $insProducto->listarProductoControlador($url[2], 10, $url[0], "", $url[1]);
            } else {
                // Si no se encuentra la categoría, mostrar un mensaje
                echo '
                <p class="has-text-centered pb-6"><i class="far fa-grin-wink fa-5x"></i></p>
                <h2 class="has-text-centered title">Seleccione una categoría para empezar</h2>';
            }
        } else {
            // Si el ID de categoría no es válido, mostrar un mensaje de error
            echo '<h2 class="has-text-centered title">ID de categoría no válido</h2>';
        }
    ?>
</div>
