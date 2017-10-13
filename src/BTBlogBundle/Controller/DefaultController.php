<?php

namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BTBlogBundle:Default:index.html.twig');
    }
    public function mathieuAction()
    {
        return $this->render('BTBlogBundle:Default:tata.html.twig');
    }
    public function addAction()
    {

        $post = new Post();

        $form = $this
            ->get('form.factory')
            ->create(PostType::class,$post);
        return $this->render('BTBlogBundle:Default:add.html.twig', array(
            'form' => $form->createView()
        ));

    }
    public function viewAction($id)
    {
        $post = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Post')
            ->find($id);
        return $this->render('BTBlogBundle:Default:view.html.twig', array(
            'post' => $post
        ));
    }
}
