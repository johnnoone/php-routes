<?php

class Router extends SplObjectStorage {
    public $basepath;
    
    function __construct($basepath=null) {
        $this->basepath = $basepath;
    }
    
    function regex() {
        $PATTERN = preg_replace('`{(\w+)}`', '(?P<$1>[^/]+)', $this->basepath);
        return "`^$PATTERN`";
    }
    
    function resolve($path, $method, array $params) {
        if (!preg_match($this->regex(), $path, $matches)) {
            return false;
        }
        
        $remind = substr($path, strlen($matches[0]));
        foreach ($matches as $k => $v) {
            if (is_string($k)) {
                $params[$k] = $v;
            }
        }
        
        $error = 404;
        foreach ($this as $route) {
            if ($resp = $route->resolve($remind, $method, $params)) {
                if ($error == 404 && is_int($resp)) {
                    $error = $resp;
                }
                else {
                    return $resp;
                }
            }
        }
        return $error;
    }
    
    public function attach($route, $data=null) {
        if ($route instanceof Route) {
            parent::attach($route, $data);
            return $route->callback;
        }
        elseif ($route instanceof Router) {
            parent::attach($route, $data);
            return $route;
        }
        
        throw new InvalidArgumentException('Only Route or Router allowed');
    }
}
