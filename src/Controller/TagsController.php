<?php
/**
 * Tags controller.
 */
namespace Controller;
use Repository\TagsRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Form\TagType;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class TagsController.
 */
class TagsController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('tags_index');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->value('page', 1)
            ->bind('tags_index_paginated');
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('tags_view');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('tags_add');
        return $controller;
    }
    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function indexAction(Application $app, $page = 1)
    {
        $tagsRepository = new TagsRepository($app['db']);
        return $app['twig']->render(
            'tags/index.html.twig',
            ['paginator' => $tagsRepository->findAllPaginated($page)]
        );
    }
    /**
     * View action.
     *
     * @param \Silex\Application $app Silex application
     * @param string $id Element Id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function viewAction(Application $app, $id)
    {
        $tagsRepository = new TagsRepository($app['db']);
        return $app['twig']->render(
            'tags/view.html.twig',
            ['tag' => $tagsRepository->findOneById($id)]
        );
    }
    /**
     * Add action.
     *
     * @param \Silex\Application $app Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAction(Application $app, Request $request)
    {
        $tag = [];
        $form = $app['form.factory']->createBuilder(TagType::class, $tag)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tagsRepository = new TagsRepository($app['db']);
            $tagsRepository->save($form->getData());
            return $app->redirect($app['url_generator']->generate('tags_index'), 301);
        }
        return $app['twig']->render(
            'tags/add.html.twig',
            [
                'tag' => $tag,
                'form' => $form->createView(),
            ]
        );
    }
}