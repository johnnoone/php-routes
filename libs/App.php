<?php

class App {
    public $router;
    
    function __construct($basepath) {
        $this->router = new Router($basepath);
    }
    
    public function attach($route, $data=null) {
        return $this->router->attach($route, $data);
    }
    
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $params = array();
        if ($resp = $this->router->resolve($path, $method, $params)) {
            if (is_int($resp)) {
                return App::error($resp);
            }
            list($callback, $params) = $resp;
            $params = $this->params($callback, $params);
            return call_user_func_array($callback, $params);
        }
        
        return App::error(500);
    }
    
    function reflex($callback) {
        if (!is_callable($callback, false, $canonical)) {
            throw new Exception('Not callable');
        }
        
        if (strpos($canonical, "::") !== false) {
            list($class, $method) = explode("::", $canonical);
            return new ReflectionMethod($class, $method);
        }
        
        return new ReflectionFunction($canonical);
    }
    
    function params($callback, $params) {
        $intr = $this->reflex($callback);
        
        $formatted = array();
        foreach ($intr->getParameters() as $p) {
            if (isset($params[$p->name])) {
                $formatted[] = $params[$p->name];
            }
            elseif ($p->isDefaultValueAvailable()) {
                $formatted[] = $p->getDefaultValue();
            }
            else {
                trigger_error('Missing param '. $p->name, E_USER_WARNING);
                $formatted[] = null;
            }
        }
        
        return $formatted;
    }
    
    static function error($code) {
        $codes = array(
            400	=> 'Bad Request',
            401	=> 'Unauthorized',
            404	=> 'Not Found',
            405	=> 'Method Not Allowed',
            409	=> 'Conflict',
            500 => 'Internal Server Error',
            501	=> 'Not Implemented',
        );
        
        header(sprintf('%s %s %s', 
            $_SERVER['SERVER_PROTOCOL'],
            $code,
            $codes[$code]
        ), true, $code);
        
        die('Kaboum');
    }

}
