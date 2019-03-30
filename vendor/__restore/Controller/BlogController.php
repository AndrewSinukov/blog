<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/blog")
 */
class BlogController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param \Twig_Environment $twig
     * @param SessionInterface $session
     * @param RouterInterface $router
     */
    public function __construct(
        \Twig_Environment $twig,
        SessionInterface $session,
        RouterInterface $router
    ) {
        $this->twig = $twig;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index(): Response
    {
        return new Response($this->twig->render('blog/index.html.twig', [
            'posts' => $this->session->get('posts'),
        ]));
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add(): RedirectResponse
    {
        $posts[uniqid('', true)] = [
            'title' => 'A random title '.random_int(1, 500),
            'text' => 'Some random text nr '.random_int(1, 500),
            'date' => new \DateTime(),
        ];
        $this->session->set('posts', $this->session->get('posts'));

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        $posts = $this->session->get('posts');

        if (! $posts || ! isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found');
        }

        return new Response($this->twig->render('blog/post.html.twig', [
            'id' => $id,
            'post' => $posts[$id],
        ]));
    }
}