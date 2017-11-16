<?php
namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;



class MainController extends Controller
{
    public function indexAction()   // render the Home page
    {
        return $this->render('index.html.twig');
    }
    public function benjaminAction() // render benjamin's page
    {
        return $this->render('benjamin.html.twig');
    }
    public function thomasAction() // render thomas' page
    {
        return $this->render('thomas.html.twig');
    }
    public function addAction(Request $request)      //Provide the add commentary, and put it on the Database
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
    public function commentaryAction($idMedia)  // Take commentary from an IdVideo and give back a object with all the commentaries to the view.
    {

        $post = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->findByidMedia($idMedia);



        if (!$post){
            throw new NotFoundHttpException("No commentaries for this video");
        }


        return $this->render('commentary.html.twig', array(
            'commentaries' => $post
        ));


    }
}
