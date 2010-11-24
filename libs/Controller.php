<?php

abstract class Controller {
    function __invoke() {
        return App::error(500);
    }
}
