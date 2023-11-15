<?php   

namespace Yosev\Login\Management\App;

class Router
{
    
    private static array $routes = []; 
        
    public static function add(string $path, string $method, string $controller, string $function, array $middlewares): void 
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'function' => $function,
            'middlewares' => $middlewares
        ];
    }

    public static function run(): void
    {

        $path = '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_SERVER['PATH_INFO'])) $path = $_SERVER['PATH_INFO'];

        foreach (self::$routes as $route) {
            
            $pattern = "#^" . $route['path'] . "$#";
            
            if (preg_match($pattern, $path, $variables) && $route['method'] == $method) {

                foreach($route['middlewares'] as $middleware) {
                    $instance = new $middleware();
                    $instance->before();
                }

                $controller = new $route['controller'];
                $function = $route['function'];
                $controller->$function();

                return;

            }

        }

        http_response_code(404);
        echo "Not Found";
    }

}