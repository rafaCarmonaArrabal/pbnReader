<?php
$cssUrl = SERVER_URL . "css/index.css";
$logoUrl = SERVER_URL . "img/logo.svg";
$insertPbnUrl = SERVER_URL . "index.php/pbn";
$resultUrl = SERVER_URL . "index.php/resultados";
$current = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prueba Bridge</title>
    <link rel="stylesheet" href="<?php echo $cssUrl ?>">
</head>
<body class="bg-gray-100 flex flex-col m-0">
<header class="w-full bg-gray-900">
    <div class="container mx-auto">
        <a href="/" class="py-5 md:py-4 px-1 md:px-2 block h-full w-1/3 md:w-2/12">
            <h1 class="text-white text-xl lg:text-1xl xl:text-2xl inline align-middle">PBNReader</h1>
            <img class="inline" src="<?php echo $logoUrl ?>" alt="Logo" width="30">
        </a>
    </div>
    <nav class="bg-main-blue-500">
        <div class="container mx-auto flex">
            <a href="<?php echo $resultUrl?>" class="py-1 md:py-2 text-white px-2 hover:bg-blue-800 font-bold animation <?php echo $current == $resultUrl ? "bg-blue-800" : ""; ?>">Resultados</a>
            <a href="<?php echo $insertPbnUrl?>"  class="py-1 md:py-2 text-white px-2 hover:bg-blue-800 font-bold animation <?php echo $current == $insertPbnUrl ? "bg-blue-800" : ""; ?>">Insertar Pbn</a>
        </div>
    </nav>
</header>
<main class="pt-6 md:pt-10 pb-6 md:pb-10 flex-1">
