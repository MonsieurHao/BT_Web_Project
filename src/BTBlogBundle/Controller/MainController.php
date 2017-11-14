<?php
namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;



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
    public function thomasAction()
    {
        return $this->render('thomas.html.twig');
    }
    public function addAction(Request $request)
    {

        $post = new Post();

        $form = $this
            ->get('form.factory')
            ->create(PostType::class,$post);


        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Annonce bien enregistrée.');

            return $this->redirect($this->generateUrl('bt_blog_view',array('id'=>$post->getId())));
        }

        return $this->render('add.html.twig',array('form'=>$form->createView()));

    }
    public function viewAction($id)
    {
        $post = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->find($id);

        if (null == $post){
            throw new NotFoundHttpException("This Commentary doesn't exist");
        }

        return $this->render('view.html.twig', array(
            'post' => $post
        ));
    }
}
