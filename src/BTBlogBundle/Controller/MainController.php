<?php
namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\PostType;
use BTBlogBundle\Entity\Articles;
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
        $articles = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->findByPostBy("b");

        return $this->render('benjamin.html.twig',array('articles'=> $articles));
    }
    public function thomasAction() // render thomas' page
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->findByPostBy("t");

        return $this->render('thomas.html.twig',array('articles'=> $articles));
    }
    public function addComAction(Request $request, $id)      //Provide the add commentary, and put it on the Database
    {

        $post = new Post();
        $article = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        $post->setArticles($article);

        $form = $this
            ->get('form.factory')
            ->create(PostType::class,$post);


        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Annonce bien enregistrée.');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$article->getId())));
        }

        return $this->render('addCommentary.html.twig',array('form'=>$form->createView()));

    }

    public function viewArtAction($id)
    {

        $article = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        if (!$article) {
            throw new NotFoundHttpException("Article doesn't exist");
        }

        $post = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->findByArticles($id);

        if (!$post) {
            throw new NotFoundHttpException("No commentaries for this article");
        }

        $media = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Media')
            ->findByArticles($id);

        if (!$media) {
            throw new NotFoundHttpException("No commentaries for this article");
        }



        return $this->render('viewArticle.html.twig', array(
            'article' => $article,
            'commentaries' => $post,
            'media' => $media
        ));
    }
}
