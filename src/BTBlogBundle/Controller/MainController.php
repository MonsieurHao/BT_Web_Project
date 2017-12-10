<?php
namespace BTBlogBundle\Controller;

use BTBlogBundle\Entity\Media;
use BTBlogBundle\Entity\Post;
use BTBlogBundle\Form\ArticlesType;
use BTBlogBundle\Form\MediaType;
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
            -> getLastArticlesPosted("benjamin",3);

        $articlesT = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            -> getLastArticlesPosted("thomas",3);


        $media = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Media')
            ->findAll();




        return $this->render('@BTBlogBundle/Resources/Views/index.html.twig',array(
            'articlesB'=>$articlesB,
            'articlesT'=>$articlesT,
            'medias' => $media)
        );
    }


    public function benjaminAction() // render benjamin's page
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->findByPostBy("benjamin");

        return $this->render('@BTBlogBundle/Resources/Views/benjamin.html.twig',array('articles'=> $articles));
    }


    public function thomasAction() // render thomas' page
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->findByPostBy("thomas");

        return $this->render('@BTBlogBundle/Resources/Views/thomas.html.twig',array('articles'=> $articles));
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

        return $this->render('@BTBlogBundle/Resources/Views/viewArticle.html.twig',
            array(
                'form'=>$form->createView(),
                'article' => $article,
                'commentaries' => $com,
                'media' => $media
            ));

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */

    public function rmComAction(Request $request, $id){
        $comment = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->find($id);

        if ($comment) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Comment were remove');
            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$comment->getArticles()->getId())));
        }
        else{

            $request->getSession()->getFlashBag()->add('notice', 'This comment does not exist');
            return $this->redirect($this->generateUrl('bt_blog_home'));
        }


    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function updComAction(Request $request, $id,$idPost)
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $post = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->find($idPost);

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


        $nForm = $this
            ->get('form.factory')
            ->create(PostType::class, $post);

        $com = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->findByArticles($id);


        if ($nForm->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commentary were added');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle', array('id' => $article->getId())));
        } else {
            return $this->render('@BTBlogBundle/Resources/Views/viewArticle.html.twig',
                array(
                    'form' => $nForm->createView(),
                    'article' => $article,
                    'commentaries' => $com,
                    'media' => $media
            ));


        }
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



        return $this->render('@BTBlogBundle/Resources/Views/viewArticle.html.twig', array(
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
        $article->setPostBy($this->get('security.token_storage')->getToken()->getUser());

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

        return $this->render('@BTBlogBundle/Resources/Views/addArticle.html.twig',array('article'=>$formArt->createView()));


    }

    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function rmArtAction(Request $request, $id)
    {

        $article = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        if ($article) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Article were remove');
        }
        else{

            $request->getSession()->getFlashBag()->add('notice', 'This article does not exist');
            return $this->redirect($this->generateUrl('bt_blog_home'));
        }

        return $this->redirect($this->generateUrl('bt_blog_home'));
    }

    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function updArtAction(Request $request, $id){

        $article = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

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

        return $this->render('@BTBlogBundle/Resources/Views/addArticle.html.twig',array('article'=>$formArt->createView()));
    }

    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function addMedAction(Request $request, $id){

        $media = new Media();

        $article = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        $media->setArticles($article);

        $formMed= $this
            ->get('form.factory')
            ->create(MediaType::class,$media);


        if($formMed->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($media);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Media were added');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$id)));
        }

        return $this->render('@BTBlogBundle/Resources/Views/addArticle.html.twig',array('article'=>$formMed->createView()));

    }
}



