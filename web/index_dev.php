<?php

use Silex\Application;
use Symfony\Component\Debug\Debug;
use Model\Bookmarks;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpExeption;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Doctrine\Common\Persistence\ObjectManager;

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once __DIR__.'/../vendor/autoload.php';

Debug::enable();

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/dev.php';
require __DIR__.'/../src/controllers.php';

$bookmarksModel = new Bookmarks;

$app['route_class'] = 'MyRoute';

/*$app->extend('twig', function($twig, $app) {
    $twig->addGlobal('pi', 3.14);
    $twig->addFilter('levenshtein', new \Twig_Filter_Function('levenshtein'));

    return $twig;
});*/

/*$app->register(new Acme\HelloServiceProvider(), array(
    'hello.default_name' => 'Konstantyn',
));

$app->get('/hello/default', function (Request $request) use ($app) {
    $name = $request->get('name');

    return $app['hello']($name);
});*/

$app->get(
    '/hello/{name}',
    function ($name) use ($app) {
        return $app['twig']->render(
            'hello/index.html.twig',
            ['name' => $name]
        );
    }
);

/*$app->get('/main', function () use ($app) {
    return 'Welcome to MyShelf!';
});*/

$app->get('/main/{name}', function ($name) use ($app) {
    return $app['twig']->render(
        'shelf/index.html.twig',
        ['name' => $name]
    );
});

$app->get('/main/', function () use ($app) {
    return $app->redirect('/main');
});

$app->get('/main', function() use($app) {
    return $app['twig']->render(
        'shelf/main.html.twig',
        ['app' => $app]
    );
});

/*$app->get('/main', function() use($app) {
    $app['db']->insert('users', array(
        'firstname' => 'Andrzej',
        'lastname'  => 'Nowak',
        'username'  => 'anowak1'
    ));
});*/

class Planet
{
    function findAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('id', 'title')
            ->from('planetarium');

        $result = $queryBuilder->execute()->fetchAll();
        return $result;
        var_dump($result);
        exit;
    }
}

//findAll();



   /* function getLockTypes()
    {
        $query = 'SELECT id, title FROM planetarium';
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_KEY_PAIR);
        var_dump($statement);
        exit;
    }*/

//getLockTypes();

/**
 *
 * Redirect.
 *
 */
$app->get('/', function () use ($app) {
    return $app->redirect('/hello');
});

$app->get(
    '/bookmarks',
    function () use ($app, $bookmarksModel) {
        return $app['twig']->render(
            'bookmarks/index.html.twig',
            ['bookmarks' => $bookmarksModel->findAll()]
        );
    }
);

$app->get(
    '/bookmarks/{id}',
    function ($id) use ($app, $bookmarksModel) {
        return $app['twig']->render(
            'bookmarks/view.html.twig',
            ['bookmark' => $bookmarksModel->findOneById($id)]
        );
    }
);

$app->get('/users/{id}', function ($id) use ($app) {
  $user = getUser($id);     // add function getUser

  if (!$user) {
      $error = array('message' => 'The user was not found.');

      return $app->json($error, 404);
  }
  return $app->json($user);
});

/**
 *
 * Blog GET test.
 *
 */
$blogPosts = array(
    1   => array(
        'date'      => '2011-03-29',
        'author'    => 'igorw',
        'title'     => 'Using Silex',
        'body'      => '...',
    ),

    2   => array(
        'date'      => '2012-12-21',
        'author'    => 'danielg',
        'title'     => 'Detroit! Lift up your weary head! (Rebulid! Restore! Reconsider!)',
        'body'      => '...',
    ),
);

$app->get('/blog', function () use ($blogPosts) {
    $output = '';
    foreach ($blogPosts as $post) {
        $output .= $post['title'];
        $output .= '<br />';
    }

    return $output;
});

$app->get('blog/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
    if(!isset($blogPosts[$id])) {
        $app->abort(404, "Post $id does not exist.");
    }

    $post = $blogPosts[$id];

    return "<h1>{$post['title']}</h1>";
    "<p>{$post['body']}</p>";
})
    ->assert('id', '\d+');

/**
 *
 * View Handlers.
 *
 */

$app->view(function (array $controllerResult) use ($app) {
    return $app->json($controllerResult);
});

$app->view(function (array $controllerResult, Request $request) use ($app) {
    $acceptHeader = $request->headers->get('Accept');
    $bestFormat = $app['negotiator']->getBestFormat($acceptHeader, array('json', 'xml'));

    if ('json' === $bestFormat) {
        return new JsonResponse($controllerResult);
    }

    if ('xml' === $bestFormat) {
        return $app['serializer.xml']->renderResponse($controllerResult);
    }
    return $controllerResult;
});

/**
 *
 * Blog POST test.
 *
 */
$app->post('/feedback', function (Request $request) {
    $message = $request->get('message');
    mail('danio7352@wp.pl', 'wp.pl Feedback', $message);

    return new Response('Thank you for your feedback!', 201);
});

$app->get('/blog/feedback', function (Silex\Application $app, $message) use ($app) {
  if(!isset($message)) {
      $app->abort(404, "Your box is empty.");
  }

   $app->get('message');

  return new Response('You have received new mail!', 201);
  return $message['message'];
});

/*$app->error(function (\Exception $e, Request $request, $code) {
    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong. Everyone is dead. Our condolences.';
    }
    return new Response($message);
});*/

/**
 *
 * User Provider test.
 *
 */
/*$userProvider = function ($id) {
    return new User($id);
};

$app->get('/user/{user}'), function (User $user) {
    //...
})->convert('user', $userProvider);

$app->get('/user/{user}/edit'), function (User $user) {
    //...
})->convert('user', $userProvider);*/

/**
 *
 * User converter.
 *
 */
class UserConverter
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function convert($id)
    {
        if (null === $user = $this->om->find('User', (int) $id)) {
            throw new NotFoundHttpExeption(sprintf('User %d does not exist', $id));
        }
        return $user;
    }
}

/**
 *
 * Streaming.
 *
 * Used in cases when you don't want to buffer the data being sent eg. images
 *
 */
$app->get('/images/{file}', function ($file) use ($app) {
    if (!file_exists(__DIR__.'images/'.$file)) {
        return $app->abort(404, 'The image was not found.');
    }

    $stream = function () use ($file) {
        readfile($file);
    };
    return $app->stream($stream, 200, array('Content-Type' => 'image/png'));
});

$app->get('/files/{path}', function ($path) use ($app) {
    if (!file_exists('/base/path/' . $path)) {
        $app->abort(404, 'The file does not exist.');
    }
    return $app
        ->sendFile('/base/path/' . $path)
        ->setContentDisposition(ResponseHaeaderBag::DISPOSITION_ATTACHMENT, 'pic.jpg')
        ;
});

/**
 *
 * Traits.
 *
 */
class MyApplication extends Application
{
    use Application\TwigTrait;
    use Application\SecurityTrait;
    use Application\FormTrait;
    use Application\UrlGeneratorTrait;
    use Application\SwiftmailerTrait;
    use Application\MonologTrait;
    use Application\TranslationTrait;
}

use Silex\Route;

//$app->path('homepage');
//$app->url('homepage');

class MyRoute extends Route
{
    use Route\SecurityTrait;
}

/**
 *
 * Escaping HTML.
 *
 * Using the htmlspecialchar function. WARNING: Update that with Twig template engine. Chapter Providers.
 *
 */
$app->get('/name', function (Request $request, Silex\Application $app) {
    $name = $request->get('name');

    return "You provided the name {$app->escape($name)}.";
});

/**
 *
 * Escaping JSON.
 *
 */
$app->get('/name.json', function (Request $request, Silex\Application $app) {
    $name = $request->get('name');

    return $app->json(array('name' => $name));
});

/**
 *
 * Defining controllers for an admin.
 *
 * mount() prefixes all routes with the given prefix and merges them into the main Application.
 *
 */
$app->mount('/admin', function ($admin) {
    $admin->mount('/blog', function ($user) {
        $user->get('/', function() {
            return 'Admin Blog home page';
        });
    });
});

/**
 *
 * Container.
 *
 * Using Pimple.
 *
 */
/*$container = new Pimple\Container();

$container['session_storage'] = function ($c) {
    return new $c['session_storage_class'] ($c['cookie_name']);     //now I can change the cookie name by overriding the session_storage_class parameter
};

$container['session'] = $container->factory(function ($c) {       //thanks to the factory() method different instance is returned for all calls
    return new Session ($c['session_storage']);
});

//get session object
$session = $container['session'];       //now each call to $container['session'] returns a new instance of the session

//define some parameters
$container['cookie_name'] = 'SESSION_ID';
$container['session_storage_class'] = 'SessionStorage';

//protecting parameters
$container['random_func'] = $container->protect(function() {
    return rand();
});

//raw access to the function
$container['session'] = function ($c) {
    return new Session($c['session_storage']);
};

$sessionFunction = $container->raw('session');*/

/**
 *
 * Service Provider.
 *
 */
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Api\BootableProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HelloServiceProvider implements ServiceProviderInterface, BootableProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
        $app['hello'] = $app->protect(function ($name) use ($app) {
            $default = $app['hello.default_name'] ? $app['hello.default_name'] : '';
            $name = $name ?: $default;

            return 'Hello '.$app->escape($name);
        });
    }

    public function boot(Application $app)
    {
        echo 'Boot works!';
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(KernelEvents::REQUEST, function (FilterResponseEvent $event) use ($app) {
            echo 'Subscribing works!';
        });
    }
}

$app->run();
