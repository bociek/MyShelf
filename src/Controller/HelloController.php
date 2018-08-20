<?php
/**
 * Hello controller.
 */
namespace Controller;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class HelloController.
 */
class HelloController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Silex\ControllerCollection Result
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/{name}', [$this, 'indexAction']);
        return $controller;
    }
    /**
     * Index action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request Request object
     *
     * @return string
     */
    public function indexAction(Application $app, $name)
    {
        return $app['twig']->render('hello/index.html.twig', ['name' => $name]);
    }
}