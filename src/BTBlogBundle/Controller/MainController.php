<?php

namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('index.html.twig');
    }
    public function benjaminAction()
    {
        return $this->render('benjamin.html.twig');
    }
    public function addAction()
    {

        $post = new Post();

        $form = $this
            ->get('form.factory')
            ->create(PostType::class,$post);
        return $this->render('add.html.twig', array(
            'form' => $form->createView()
        ));

    }
    public function viewAction($id)
    {
        $post = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Post')
            ->find($id);
        return $this->render('view.html.twig', array(
            'post' => $post
        ));
    }
}
