<?php
namespace FOS\UserBundle\Controller;

use FOS\UserBundle\Entity\User;
use FOS\UserBundle\Form\UserType;
use FOS\UserBundle\Form\UserLoginType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
Use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller{

    /**
     * @Route("/index")
     */
    public function indexAction(Request $request)
    {
        $userEntityManager = $this->getDoctrine()->getManager();
        //recuperer le repository user
        $users = $userEntityManager->getRepository('FOSUserBundle:User')->findAll();

        return $this->render('FOSUserBundle:User:index.html.twig',[
            'users' => $users,
        ]);
    }


    /**
     * @Route("/register")
     */
    public function registerAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException(); //si deja logged renvoyé une page not found
        }
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request); //methode handklerequest: pré-envois la requete /prepare de type post
        if($form->isSubmitted() && $form->isValid(true)) {
            $validator = $this->get('validator');
            $errors = $validator->validate($user);
            if (count($errors)) {
                // echo (String) $errors;
                return $this->render('FOSUserBundle:User:register.html.twig', array(
                    'form'   => $form->createView(),
                    'errors' => (String) $errors
                ));
            } else {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
                $user = $form->getData();
                //recupère l'antité manager
                $userEntityManager = $this->getDoctrine()->getManager();
                //l'entité object user est gerée par doctrine=>elle la  garde en memoire en attente d'etre en db
                $userEntityManager->persist($user);
                $userEntityManager->flush(); //sauvegarde les données de l'oject user en db (insert to requetes)
                $message_success = "The Bonobo " . $user->getUsername(). " was successfuly created : Welcome !";
                $this->addFlash('message_success', $message_success);
                return $this->redirectToRoute('login');
            }
        }
        return $this->render('FOSUserBundle:User:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils )
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException(); //si deja logged renvoyé une page not found
        }
         //service qui recupere les erreurs
         $error =$authUtils->getLastAuthenticationError();
         $lastUsername = $authUtils->getLastUserName();

         return $this->render('FOSUserBundle:User:login.html.twig', array(
            'username' => $lastUsername,
            'errors' => $error,
          ));
    }

    /*
    @Route("/user_showme")
    *
    */
    public function showmeAction(Request $request ) //profil d'un bonobo
    {
        $user = $this->getUser();
        $user->getId();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //var_dump($form->isSubmitted());

        if ($form->isSubmitted() && $form->isValid(true)) {
            //éditer les champs désirés
            $editForm = $form->getData();
            $user->setUsername($editForm->getUsername());
            $user->setPassword($editForm->getPassword());
            $user->setAge($editForm->getAge());
            $user->setFamily($editForm->getFamily());
            $user->setRace($editForm->getRace());
            $user->setFoods($editForm->getFoods());

            //vérifier les erreurs
            $validator = $this->get('validator');
            $errors = $validator->validate($user);
            if (count($errors)) {
                // echo (String) $errors;
                return $this->redirectToRoute('user_showme', array(
                    'form' => $form->createView(),
                    'errors' => (String)$errors
                ));
            } else {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), 'toto'));
                $user = $form->getData();

                $userEntityManager = $this->getDoctrine()->getManager();
                $userEntityManager->persist($user);
                $userEntityManager->flush();
                $message_success = "Dear " . $user->getUsername() . ", your profil was successfully updated !";
                $this->addFlash('message_success', $message_success);
                return $this->redirectToRoute('user_showme', array(
                    'id' => $user->getId()
                ));
            }
        }
        //la methode createView permet a la vue d'afficher le formulaire
        return $this->render('FOSUserBundle:User:showme.html.twig', array(
            'form'  => $form->createView(),
            'user'  =>$user,
        ));
    }

    /*
    * @Route("/delete")
    */
    public function deleteAction($id)
    {
        $user = $this->getUser();
        $userEntityManager = $this->getDoctrine()->getManager();
        $friend = $userEntityManager->getRepository('FOSUserBundle:User')->find($id);
        if(!$friend){
            $this->get('session')->getFlashBag()->add('response', 'La suppression de '.$friend->getUsername().' échoué');
            return $this->redirectToRoute('user_delete');
        }else{
            //var_dump($user->getFriends());
            $index= array_search($id, $user->getFriends()); //recherche l'id du friend selectionné
            if ($index !== false ){
                $tab = $user->getFriends(); //recupere la liste d'ami
                array_splice($tab ,$index, 1 );//remplace le contenu de la liste d'amis au niveau de l'index choisi
                $user->setFriends($tab); //l'utilisateur recupere la liste de friend d'origine par le nouveau
                //var_dump($user->getFriends());
            }
            $userEntityManager->flush();
            $this->get('session')->getFlashBag()->add('success', $friend->getUsername().'  was succefully erased');
            return $this->redirectToRoute('fos_user_index');
        }
    }


    /**
     * @Route("/list")
     */
    public function listAction(){
        $userEntityManager = $this->getDoctrine()->getManager();
        //recuperer le repository user
        $user = $userEntityManager->getRepository('FOSUserBundle:User')->find($this->getUser()->getId());
        //recuperer le id pointé parmit la liste d'amis
        $users = $userEntityManager->getRepository('FOSUserBundle:User')->findById($user->getFriends());
        return $this->render('FOSUserBundle:User:list.html.twig',[
            'users' => $users,
        ]);
    }


     /**
     * @Route("/add")
     */

    public function AddAction(Request $request, $id)
    {
        $userEntityManager = $this->getDoctrine()->getManager();
        $friends = $userEntityManager->getRepository('FOSUserBundle:User')->find($id);
        $user = $userEntityManager->getRepository('FOSUserBundle:User')->find($this->getUser()->getId());
        if(!$friends){
            throw new NotFoundHttpException("Le bonobo d'id " . $id . " n'existe pas.");
        }else {
            $user->addFriend($id);
            $userEntityManager->persist($user);
            $userEntityManager->flush();
        return $this->redirect($this->generateUrl('user_list'));
        }
    }


    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user){
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array(
                'id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


}
