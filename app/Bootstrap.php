<?php //proyecto/app/bootstrap.php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

//configuración para cargar las vistas
define('VIEW_PATH', APP_PATH . '../resources/views/');

//configuración del router

$requestContext = new RequestContext();
$requestContext->fromRequest($request); // el contexto se actualiza con la info del request

$locator = new FileLocator(array(APP_PATH . 'config/')); // nos ubicamos en la carpeta app/config
$loader = new YamlFileLoader($locator);

$options = array(
    'cache_dir' => APP_PATH . 'cache/', //directorio donde serán cacheadas las rutas
    'debug' => DEBUG, // depende de la constante creada en public/web.php
);

$router = new Router($loader, 'routing.yml', $options, $requestContext);

//configuración de ruteo

try {
    $match = $router->match($request->getPathInfo()); //obtenemos la uri desde el pathinfo
    $request->attributes->add($match); //agregamos los datos definidos para la ruta en el request
    //leemos el indice _file definido en la opción defaults de la ruta.
    $file = APP_PATH . 'controllers/' . $match['_file'];

    if (!is_file($file)) {
        $response = new Response('Internal Server Error', 505);
        throw new InvalidArgumentException("No existe el archivo controlador " . $file);
    }

    ob_start(); //vamos a capturar la salida para agregarla luego a un objeto response
    // We get the class.
    $class = getClassName($file);
    $controller = new $class;
    // get the method.
    $method = $match['_method'] ?? 'index';
    $args = [];
    if (isset($match['_args']) && count($match['_args']) > 0) {
        foreach ($match['_args'] as $arg) {
            array_push($args, ($request->get($arg) ?? null));
        }
    }
    if (\strtoupper($request->getMethod()) == 'POST') {
        $args[] = $_POST;
        $args[] = $_FILES;
        // check the token
        if (empty($_POST['_token']) || $_POST['_token'] != $_SESSION['_token'])
            return header('Location: ' . SERVER_URL . 'index.php');
    }
    if (!isset($args)) {
        $response = call_user_func(array($controller, $method));
    } else {
        $response = call_user_func_array(array($controller, $method), $args);
    }
    /*
     * Si no se devuelve una instancia de response, la creamos y le pasamos el contenido del buffer
     */
    if (!($response instanceof Response)) {
        $response = new Response(ob_get_clean());
    }
} catch (ResourceNotFoundException $e) {
    /*if (DEBUG) {
        throw new ResourceNotFoundException("No existe una definición para la url "
            . $request->getPathinfo(), $e->getCode(), $e); //en desarrollo mostramos la excepción
    } else {
        // en producción creamos una respuesta
        $response = new Response('Internal Server Error', 500);
    }*/
    $response = new Response('<h1 class="text-center text-4xl w-full mt-10">Url not found.<h1/>', 404);
}

return $response; //devolvemos la respuesta


function getClassName($file)
{
    $fp = fopen($file, 'r');
    $class = $namespace = $buffer = '';
    $i = 0;
    while (!$class) {
        if (feof($fp)) break;

        $buffer .= fgets($fp);
        $tokens = \token_get_all($buffer);

        if (strpos($buffer, '{') === false) continue;

        for (; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if ($tokens[$j] === '{') {
                        $class = $tokens[$i + 2][1];
                    }
                }
            }
        }
    }
    return "$namespace\\$class";
}
