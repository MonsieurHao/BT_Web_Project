<?php
namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\ArticlesType;
use BTBlogBundle\Form\PostType;
use BTBlogBundle\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class MainController extends Controller
{
    public function indexAction()   // render the Home page
    {
        $articlesB = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            -> getLastArticlesPosted("b",3);

        $articlesT = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            -> getLastArticlesPosted("t",3);


        $media = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Media')
            ->findAll();




        return $this->render('index.html.twig',array('articlesB'=>$articlesB,
            'articlesT'=>$articlesT,
            'medias' => $media)
        );
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


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addComAction(Request $request, $id)      //Provide the add commentary, and put it on the Database
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $post = new Post();
        $article = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        $post
            ->setArticles($article)
            ->setPseudo($user->getUsername());

        $media = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Media')
            ->findByArticles($id);


        $form = $this
            ->get('form.factory')
            ->create(PostType::class,$post);

        $com = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->findByArticles($id);


        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Commentary were added');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$article->getId())));
        }

        return $this->render('viewArticle.html.twig',
            array(
                'form'=>$form->createView(),
                'article' => $article,
                'commentaries' => $com,
                'media' => $media
            ));

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

        $media = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Media')
            ->findByArticles($id);



        return $this->render('viewArticle.html.twig', array(
            'article' => $article,
            'commentaries' => $post,
            'media' => $media
        ));
    }




    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function addArtAction(Request $request){

        $article = new Articles();
        $formArt = $this
            ->get('form.factory')
            ->create(ArticlesType::class,$article);

        if($formArt->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Article were added');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$article->getId())));
        }

        return $this->render('BTBlogBundle::addArticle.html.twig',array('article'=>$formArt->createView()));



    }




}



