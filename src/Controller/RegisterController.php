<?php
namespace Controller;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Form\RegisterForm;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
class RegisterController implements ControllerProviderInterface
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
        $controller->match('/', array($this, 'indexAction'))
            ->bind('register');
        return $controller;
    }
    /**
     * Index action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request Request object
     *
     * @return string Response
     */
    public function indexAction(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder(FormType::class)
            ->add('name', TextType::class, array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('surname', TextType::class, array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('address', TextType::class, array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('email', TextType::class, array(
                'constraints' => new Assert\Email()
            ))
            ->add('phone', TextType::class, array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('password', PasswordType::class, array())
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $data['password'] = $app['security.encoder.digest']->encodePassword($data['password'], '');
            $data['role_id'] = 1;
            $conn = $app['db'];
            $conn->insert('users', $data);
            echo 'Dodano usera';
        }
        $this->view['form'] = $form->createView();
        return $app['twig']->render('register.html.twig', array('form' => $form->createView()));
    }
}