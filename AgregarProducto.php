
<?php

 $conn = mysqli_connect("localhost","root","ulloa","barberia"); //conectamos a la base de datos
 //enviar informacion del login para iniciar sesion

 if(isset($_POST["guardar"])) //clic en boton
 {
        $id_producto = $_POST["id_producto"];
        $nombre = $_POST["nombre_producto"]; //por el metodo post se obtienen los datos de los input
        $precio = $_POST["precio_producto"];
        $descripcion = $_POST["descripcion_producto"];

        if(!empty($nombre) && !empty($precio) && !empty($descripcion)){

            $query2 = "SELECT * from producto where id_producto = '$id_producto' OR nombre = '$nombre'";

            $resul2 = mysqli_query($conn,$query2);

            if(mysqli_num_rows($resul2) > 0){
                return false;
            }else{

                $query = "INSERT INTO producto VALUES ($id_producto,'$nombre',$precio,'$descripcion');";//insercion a la base de datos

                $resul = mysqli_query($conn, $query);

                if ($resul) //conexion exitosa e insercion
                {
                    header("Location: Producto.php");
                    echo "<script> alert('Agregado correctamente');</script>";

                }else{
                        echo "Error " . $query . "<br>" . mysqli_error($conn);
                }
                return true;
            }
            
        }else{
            echo "Todos los campos son obligatorios. Por favor, complete el formulario";
            header("Location: Producto.php");
            exit();
        }

      
 }

?>
