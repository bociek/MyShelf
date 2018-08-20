<?php
/**
 * Bookmarks controller.
 */
namespace Controller;
use Model\Bookmarks;
use Form\BookmarkType;
use Repository\BookmarksRepository;
use Repository\TagsRepository;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
/**
 * Class BookmarksController.
 */
class BookmarksController implements ControllerProviderInterface
{
    protected $bookmarksModel = null;
    public function __construct()
    {
        $this->bookmarksModel = new Bookmarks();
    }
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
        $controller->get('/', [$this, 'indexAction'])
            ->bind('bookmarks_index');
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('bookmarks_view');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('bookmarks_add');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('bookmarks_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('bookmarks_delete');
        return $controller;
    }
    /**
     * Add action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAction(Application $app, Request $request)
    {
        $bookmark = [];
        $form = $app['form.factory']->createBuilder(
            BookmarkType::class,
            $bookmark,
            ['tags_repository' => new TagsRepository($app['db'])]
        )->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bookmark = $form->getData();
            $bookmarksRepository = new BookmarksRepository($app['db']);
            $bookmarksRepository -> save($bookmark);
            dump($bookmark);
        }
        return $app['twig']->render(
            'bookmarks/add.html.twig',
            [
                'bookmark' => $bookmark,
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * Edit action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param int                                       $id      Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function editAction(Application $app, $id, Request $request)
    {
        $tagsRepository = new BookmarksRepository($app['db']);
        $tag = $tagsRepository->findOneById($id);
        if (!$tag) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );
            return $app->redirect($app['url_generator']->generate('tags_index'));
        }
        $form = $app['form.factory']->createBuilder(
            BookmarkType::class,
            $bookmark,
            ['tags_repository' => new TagsRepository($app['db'])]
        )->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tagsRepository->save($form->getData());
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );
            return $app->redirect($app['url_generator']->generate('tags_index'), 301);
        }
        return $app['twig']->render(
            'tags/edit.html.twig',
            [
                'tag' => $tag,
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * Delete action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param int                                       $id      Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deleteAction(Application $app, $id, Request $request)
    {
        $bookmarksRepository = new BookmarksRepository($app['db']);
        $bookmark = $bookmarksRepository->findOneById($id);
        if (!$bookmark) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );
            return $app->redirect($app['url_generator']->generate('bookmarks_index'));
        }
        $form = $app['form.factory']->createBuilder(FormType::class, $bookmark)->add('id', HiddenType::class)
            ->add('id', HiddenTYpe::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bookmarksRepository->delete($form->getData());
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );
            return $app->redirect(
                $app['url_generator']->generate('bookmarks_index'),
                301
            );
        }
        return $app['twig']->render(
            'bookmarks/delete.html.twig',
            [
                'bookmark' => $bookmark,
                'form' => $form->createView(),
            ]
        );
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
        $bookmarksRepository = new BookmarksRepository($app['db']);
        return $app['twig']->render(
            'bookmarks/index.html.twig',
            ['paginator' => $bookmarksRepository->findAllPaginated($page)]
        );
    }
    public function viewAction(Application $app, $id)
    {
        $bookmark = $this->bookmarksModel->findOneById($id);
        return $app['twig']->render(
            'bookmarks/view.html.twig',
            ['bookmark' => $bookmark]
        );
    }
}