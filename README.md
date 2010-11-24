PHP Routes
==========

Minimal Routing processing.

## Exemple

set an .htaccess file like this one

    Options +FollowSymLinks
    RewriteEngine On
    RewriteBase /define/the/path/to/access/this/app/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php [L]

and your bootstrap

    <?php
    define('BASEPATH', '/define/the/path/to/access/this/app');
    
    require_once ROUTES_DIR . '/index.php';
    
    function home($sub=null) {
        print "you are in home `$sub`";
    }

    class FooController extends Controller {
        public function __invoke($sub=null) {
            print "you are in FooController `$sub`";
        }
    }

    class BarController extends Controller {
        public function GET() {
            print "you are in BarController, baby";
        }
    }

    $main = new App(BASEPATH);
    $main->attach(new Route('/', 'home', 'GET'));
    $main->attach(new Route('/foo', new FooController, array('GET', 'POST')));
    $main->attach(new Route('/bar', new BarController));

    $sub = $main->attach(new Router('/{sub}'));
    $sub->attach(new Route('/', 'home', 'GET'));
    $sub->attach(new Route('/foo', new FooController, 'GET'));
    $sub->attach(new Route('/bar', new BarController));

    $main->run();

voilà !