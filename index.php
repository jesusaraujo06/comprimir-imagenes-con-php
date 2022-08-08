<?php

require 'vendor/autoload.php';

use Verot\Upload\Upload;
use TdTrung\Chalk\Chalk;
use AlecRabbit\Snake\Spinner;

$chalk = new Chalk();

// Constantes
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);
define('ANCHO_MAXIMO', 1920);

// Variables
$rutaCarpetaPrincipal = 'src/images-de-evidencias/';
$rutaCarpetaOptimizada = 'src/images-de-evidencias-optimizadas/';

// Verificar si es una carpeta
if(!is_dir($rutaCarpetaPrincipal)){
    print $chalk->red("La ruta: '$rutaCarpetaPrincipal' no hace referencia a una carpeta (Fin del programa) \n");
    return;
}

// Abrimos la carpeta principal
$carpetaPrincipal = opendir($rutaCarpetaPrincipal);

// Comprobamos que se haya abierto la carpeta
if(!$carpetaPrincipal){
    print $chalk->red("No se pudo abrir la carpeta principal, en la ruta: '$rutaCarpetaPrincipal' (Fin del programa) \n");
    return;
}

$contadorCarpeta = 0;
$carpetaObjetivo = 42;

// Recorremos los elementos de dicha carpeta
while (($carpeta = readdir($carpetaPrincipal)) !== FALSE) {

    if ($carpeta == "." || $carpeta == "..") {
        continue;
    }

    $contadorImagenes = 0;
    $contadorCarpeta++;
        
    $rutaCarpetaDeImagenes = $rutaCarpetaPrincipal . $carpeta;
    
    // Verificamos si es una carpeta
    if(!is_dir($rutaCarpetaDeImagenes)){
        continue;
    }

    $carpetaDeImagenes = opendir($rutaCarpetaDeImagenes);

    // Comprobamos que se haya abierto la carpeta
    if(!$carpetaDeImagenes){
        print $chalk->red("La carpeta en la ruta: '$rutaCarpetaDeImagenes' no se pudo abrir (Fin del programa) \n");
        break;
    }

    print $chalk->bold->green("\n\nCarpeta => '$carpeta' abierta \n");

    // if($contadorCarpeta < $carpetaObjetivo){
    //     echo "Ya se comprimieron las imagenes en esta carpeta\n";
    //     continue;
    // }

    $rutaCarpetaDeImagenesOptimizadas = $rutaCarpetaOptimizada . $carpeta . '/';

    while (($imagen = readdir($carpetaDeImagenes)) !== FALSE) {

        if ($imagen == "." || $imagen == "..") {
            continue;
        }

        echo "Imagen => $imagen ";

        $contadorImagenes++;

        if(file_exists($rutaCarpetaDeImagenesOptimizadas.$imagen)){
            print $chalk->yellow("= ya existe ✔ \n");
            continue;
        }

        $rutaImagen = $rutaCarpetaDeImagenes .'/'. $imagen;

        // Información de la imagen
        $dataImagen = GetImageSize($rutaImagen);
        $nombreImagen = $imagen;
        $tipoImagen = $dataImagen['mime'];
        $anchoImagen = $dataImagen[0];
        $altoImagen = $dataImagen[1];
        $pesoImagen = filesize($rutaImagen);

        $up = new Upload($rutaImagen);

        if (!$up->uploaded) {
            return false;
        }



        // Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
        if ($anchoImagen <= ANCHO_MAXIMO && $pesoImagen <= 1 * MB) {
            $up->Process($rutaCarpetaDeImagenesOptimizadas);
          
        } else {
            $up->image_resize = true;
            $up->image_x = ANCHO_MAXIMO;
            $up->image_ratio_y = true;
            $up->Process($rutaCarpetaDeImagenesOptimizadas);
       
         

        }

        if ($up->processed) {
            // $up->clean();
            print $chalk->yellow("= imagen creada y optimizada ✔ \n");
          
 
    
        } else {
            
            echo 'error : ' . $handle->error;

        }


        
        // echo "<img style='weight:20%; height:20%;' src=".$rutaCarpetaDeImagenes ."/". $imagen." $dataImagen[3]>";
       
    }

    print $chalk->bold->green("Total imagenes en la carpeta => $contadorImagenes\n");
    echo "===============================\n\n";
    closedir($carpetaDeImagenes);
} 

closedir($carpetaPrincipal);