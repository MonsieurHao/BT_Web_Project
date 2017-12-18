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
    /**
     * @return mixed
     */
    public function indexAction()                                                                                                       // render the Home page
    {
        $articlesB = $this                                                                                                              // Get the 3 last articles posted by Benjamin (The list can be null)
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            -> getLastArticlesPosted("benjamin",3);

        $articlesT = $this                                                                                                              // Get the 3 last articles posted by Thomas    (The list can be null)
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            -> getLastArticlesPosted("thomas",3);


        $media = $this
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Media')
            ->findAll();




        return $this->render('@BTBlogBundle/Resources/Views/index.html.twig',array(                                                     // Render the Home page
            'articlesB'=>$articlesB,
            'articlesT'=>$articlesT,
            'medias' => $media)
        );
    }

    /**
     * @return mixed
     */
    public function benjaminAction()                                                                                                   // Render benjamin's page
    {
        $articles = $this                                                                                                              // Render the list of articles concerned by Benjamin (The list can be null)
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->findByPostBy("benjamin");

        return $this->render('@BTBlogBundle/Resources/Views/benjamin.html.twig',array('articles'=> $articles));
    }

    /**
     * @return mixed
     */
    public function thomasAction()                                                                                                     // Render thomas' page
    {
        $articles = $this                                                                                                              // Render the list of articles concerned by Thomas (The list can be null)
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->findByPostBy("thomas");

        return $this->render('@BTBlogBundle/Resources/Views/thomas.html.twig',array('articles'=> $articles));
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function addComAction(Request $request, $id)                                                                                //Provide the add commentary action, and push it on the Database
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();                                                           // Get the current user

        $post = new Post();

        $article = $this                                                                                                               // Get the article concerned
            ->getDoctrine()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);


        $media = $this                                                                                                                  // Get all the media concerned by the article (can be null)
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Media')
            ->findByArticles($id);


        $com = $this                                                                                                                   // Get all the commentaries concerned by the article (can be null)
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->findByArticles($id);


        if (!$article) {                                                                                                                // Return to the home page if the article doesn't exist and notify the user
            $request->getSession()->getFlashBag()->add('Warning', 'This article does not exist');

            return $this->redirect($this->generateUrl('bt_blog_home'));
        }


        $form = $this                                                                                                                   // Create the form to add the commentary
            ->get('form.factory')
            ->create(PostType::class,$post);

        $post                                                                                                                          // Set the pseudo of this new commentary
        ->setArticles($article)
            ->setPseudo($user->getUsername());



        if($form->handleRequest($request)->isSubmitted() and $form->isValid()){                                                        // Push the commentary on the database if the form is valid
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Commentary were added');                                              // Return the article concerned and notify that it was added

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$article->getId())));
        }

        return $this->render('@BTBlogBundle/Resources/Views/viewArticle.html.twig',                                                    // Return the form created and the article concerned
            array(
                'form'=>$form->createView(),
                'article' => $article,
                'commentaries' => $com,
                'media' => $media
            ));

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function rmComAction(Request $request, $id){

        $commentary = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->find($id);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($commentary and ($user->getUsername() == $commentary->getPseudo() or $user->hasRole('ROLE_AUTHOR')) ) {                      // remove the commentary if it exists and if it was posted by the user who wants to remove it
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentary);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Comment were remove');
            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$commentary->getArticles()->getId())));          // return the article concerned without the removed commentary and notify that the commentary was removed
        }
        else{

            $request->getSession()->getFlashBag()->add('Warning', 'This comment does not exist');                                        // return the article with no update and notify that the commentary doesn't exist
            return $this->redirect($this->generateUrl('bt_blog_home'));
        }

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @param int$idPost
     * @return mixed
     */
    public function updComAction(Request $request, $id, $idPost)                                                                        // update the commentary if it exists and if it was posted by the user who want to update it
    {


        $user = $this->get('security.token_storage')->getToken()->getUser();                                                            // Get the current user

        $post = $this->getDoctrine()->getManager()->getRepository('BTBlogBundle:Post')->find($idPost);                                  // Get the commentary concerned

        $article = $this->getDoctrine()->getRepository('BTBlogBundle:Articles')->find($id);                                             // Get the article concerned

        $media = $this->getDoctrine()->getManager()->getRepository('BTBlogBundle:Media')->findByArticles($id);                          // List all media on this article

        $commentaries = $this->getDoctrine()->getManager()->getRepository('BTBlogBundle:Post')->findByArticles($id);                    // List all commentaries on this article

        if (!$article or !$post) {                                                                                                      // Return to the home page if the article doesn't exist and notify the user

            $request->getSession()->getFlashBag()->add('Warning', 'This article or commentary does not exist');

            return $this->redirect($this->generateUrl('bt_blog_home'));
        }


        $form = $this                                                                                                                   // Create the form for the update
            ->get('form.factory')
            ->create(PostType::class, $post);


        if ($form->handleRequest($request)->isSubmitted() and $form->isValid() and ($user->getUsername() == $post->getPseudo() or $user->hasRole('ROLE_AUTHOR'))) {  //Push the updated commentary if it is valid and if the user is the owner of the commentary or is an author

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commentary were updated');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle', array('id' => $article->getId())));                        //Return the article concerned with the updated commentary


        } elseif ($user->getUsername() == $post->getPseudo() or $user->hasRole('ROLE_AUTHOR')) {
            return $this->render('@BTBlogBundle/Resources/Views/viewArticle.html.twig',                                                 // Return the form to update the commentary only if the user is the commentary's owner or is an author
                array(
                    'form' => $form->createView(),
                    'article' => $article,
                    'commentaries' => $commentaries,
                    'media' => $media
            ));


        }else{

            $request->getSession()->getFlashBag()->add('Warning', 'This is not your commentary');                                        //Return the article with no update
            return $this->redirect($this->generateUrl('bt_blog_viewArticle', array('id'=> $id)));

        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */

    public function viewArtAction(Request $request, $id)                                                                                // Provide the article
    {

        $article = $this                                                                                                                // Get the article concerned
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        if (!$article) {                                                                                                                // Return to the home page if the article doesn't exist and notify the user
            $request->getSession()->getFlashBag()->add('Warning', 'This article does not exist');

            return $this->redirect($this->generateUrl('bt_blog_home'));
        }

        $comments = $this                                                                                                               // Get the commentaries concerned by this article
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Post')
            ->findByArticles($id);

        $media = $this                                                                                                                  // Get the media concerned by this article
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Media')
            ->findByArticles($id);



        return $this->render('@BTBlogBundle/Resources/Views/viewArticle.html.twig', array(                                               // Render the article, all media, and commentaries concerned
            'article' => $article,
            'commentaries' => $comments,
            'media' => $media
        ));
    }




    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     * @param Request $request
     * @return  mixed
     */
    public function addArtAction(Request $request){                                                                                      // Provide the add article action

        $article = new Articles();

        $article->setPostBy($this->get('security.token_storage')->getToken()->getUser());                                                // Set the author of the article

        $formArt = $this                                                                                                                 // Create the form for the article
            ->get('form.factory')
            ->create(ArticlesType::class,$article);


        if($formArt->handleRequest($request)->isSubmitted() and $formArt->isValid() ){                                                   // Push the article to the database if the form is valid
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Article were added');                                                   // render the article and notify the author that it was added


            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$article->getId())));
        }

        return $this->render('@BTBlogBundle/Resources/Views/addArticle.html.twig',array('article'=>$formArt->createView()));             // render the add form


    }

    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function rmArtAction(Request $request, $id)                                                                                    // provide the remove article action
    {

        $article = $this                                                                                                                  // Get the article
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        if ($article) {                                                                                                                   // remove the article from the database if it exists
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Article were remove');                                                  // notify that it was removed
        }
        else{

            $request->getSession()->getFlashBag()->add('Warning', 'This article does not exist');                                          // notify the author that the article doesn't exist
        }

        return $this->redirect($this->generateUrl('bt_blog_home'));                                                                       // return the home page
    }

    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function updArtAction(Request $request, $id){                                                                                  // Provide the update article action

        $article = $this                                                                                                                  // Get the article concerned
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        if(!$article) {                                                                                                                   // return to home page if the article doesn't exist

            $request->getSession()->getFlashBag()->add('Warning','This article does not exist !');

            return $this->redirect($this->generateUrl('bt_blog_home'));
        }


        $formArt = $this                                                                                                                  // Create the form
            ->get('form.factory')
            ->create(ArticlesType::class,$article);


        if($formArt->handleRequest($request)->isSubmitted() and $formArt->isValid()){                                                    // Push the form if it is valid, notify the author that it was updated and renders it
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Article were updated');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$article->getId())));
        }

        return $this->render('@BTBlogBundle/Resources/Views/addArticle.html.twig',array('article'=>$formArt->createView()));
    }

    /**
     * @Security("is_granted('ROLE_AUTHOR')")
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function addMedAction(Request $request, $id){                                                                                   // Provide the add media action




        $article = $this                                                                                                                   // Get the article concerned
            ->getDoctrine()
            ->getManager()
            ->getRepository('BTBlogBundle:Articles')
            ->find($id);

        if(!$article) {                                                                                                                    // return to home page if the article doesn't exist

            $request->getSession()->getFlashBag()->add('Warning','Can not add media, there is no article posted');

            return $this->redirect($this->generateUrl('bt_blog_home'));                                                                    // Redirect to home page if reference is null

        }

        $media = new Media();

        $formMed= $this                                                                                                                    // Create the form
            ->get('form.factory')
            ->create(MediaType::class,$media);

        $media->setArticles($article);

        if($formMed->handleRequest($request)->isSubmitted() and $formMed->isValid()){                                                      // Push the media to the database if the form is valid, notify the author, and render the article
            $em = $this->getDoctrine()->getManager();
            $em->persist($media);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice','Media were added');

            return $this->redirect($this->generateUrl('bt_blog_viewArticle',array('id'=>$id)));
        }


        return $this->render('@BTBlogBundle/Resources/Views/addMedia.html.twig',array('media'=>$formMed->createView()));                   // Render the form view
    }
}



