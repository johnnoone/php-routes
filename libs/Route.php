<?php

class Route {
    public
        $path,
        $callback,
        $methods;
    
    function __construct($path, $callback, $methods='GET') {
        $this->path = $path;
        $this->callback = $callback;
        
        if (!is_null($methods)) {
            $this->methods = (array) $methods;
        }
    }
    
    function regex() {
        $PATTERN = preg_replace('`{(\w+)}`', '(?P<$1>[^/]+)', $this->path);
        return "`^$PATTERN$`";
    }
    
    function resolve($path, $method, array $params) {
        if (!preg_match($this->regex(), $path, $matches)) {
            return false;
        }
        
        if (!in_array($method, $this->methods)) {
            return 405;
        }
        
        foreach ($matches as $k => $v) {
            if (is_string($k)) {
                $params[$k] = $v;
            }
        }
        
        // si c'est un object et qu'il implemente des methods GET, POST ...
        if (is_object($this->callback) && method_exists($this->callback, $method)) {
            return array(array($this->callback, $method), $params);
        }
        return array($this->callback, $params);
    }
}
