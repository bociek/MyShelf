<?php
/**
 * Auth controller.
 *
 */
namespace Controller;
use Form\LoginType;
use Form\RegisterForm;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class AuthController.
 */
class AuthController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('login', [$this, 'loginAction'])
            ->method('GET|POST')
            ->bind('auth_login');
        $controller->get('logout', [$this, 'logoutAction'])
            ->bind('auth_logout');
        $controller->get('register', [$this, 'registerAction'])
            ->method('GET|POST')
            ->bind('auth_register');
        return $controller;
    }
    /**
     * Login action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function loginAction(Application $app, Request $request)
    {
        $user = ['email' => $app['session']->get('_security.last_username')];
        $form = $app['form.factory']->createBuilder(LoginType::class, $user)->getForm();
        return $app['twig']->render(
            'auth/login.html.twig',
            [
                'form' => $form->createView(),
                'error' => $app['security.last_error']($request),
            ]
        );
    }
    /**
     * Logout action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function logoutAction(Application $app)
    {
        $app['session']->clear();
        return $app['twig']->render('auth/logout.html.twig', []);
    }
    public function registerAction(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder(RegisterForm::class)->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $data['password'] = $app['security.encoder.bcrypt']->encodePassword($data['password'], '');
            $data['role_id'] = 1;
            $conn = $app['db'];
            $conn->insert('users', $data);
            echo 'Dodano usera';
        }
        $this->view['form'] = $form->createView();
        return $app['twig']->render('auth/register.html.twig', array('form' => $form->createView()));
    }
}