<?php
namespace FOS\UserBundle\Controller;

use FOS\UserBundle\Entity\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
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


class UserController extends Controller{

    /**
     * @Route("/index")
     */
    public function indexAction(Request $request)
    {    
        return $this->render('FOSUserBundle:User:index.html.twig');
    }


    /**
     * @Route("/register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('login', TextType::class ,array('attr' => array(
                'placeholder'=>'Your username',
                'class'      =>'errors')))
            ->add('password', PasswordType::class ,array('attr' => array(
                'placeholder'=>'Your password',
                'class'      =>'errors')))
            ->add('age', IntegerType::class ,array('attr' => array(
                'placeholder'=> 'Your birthday',
                'class'      =>'errors')))
            ->add('family', TextType::class ,array('attr' => array(
                'placeholder'=> 'Your family',
                'class'      =>'errors' )))
            ->add('race', TextType::class ,array('attr' => array(
                'placeholder'=> 'Your race',
                'class'      =>'errors')))
            ->add('foods', TextType::class ,array('attr' => array(
                'placeholder'=> 'Your favorite foods',
                'class'      =>'errors')))
            ->add('Register', SubmitType::class, array ('attr' => array( 
                'class'      =>'btn btn_success', 
                'label'      =>'register')))
            ->getForm();

            $form->handleRequest($request); //methode handklerequest:requete de type post
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
                    $user->setPassword($encoder->encodePassword($user->getPassword(),'toto'));
                    $user = $form->getData();
                    // si user est une doctrine entity, le sauvegarder
                    $userEntityManager = $this->getDoctrine()->getManager();
                    $userEntityManager->persist($user);// inserer en bdd
                    $userEntityManager->flush(); //actualiser les données
                    $message_success = "The Bonobo " . $user->getLogin(). " was successfuly created : Welcome !";
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
    public function loginAction(Request $request)
    {
        $user = new User();
        $authUsers = $this->get('security.authentication_utils');
        $TokenUsers= $this->get('security.token_storage');
        $errors = $authUsers->getLastAuthenticationError();
        $lastPassword = $TokenUsers->getToken()->getUser();
        $lastLogin = $authUsers->getLastUserName();

        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
        // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $message_success = "Dear Bonobo " . $user->getLogin(). " you are logg-in";
            $this->addFlash('message_success', $message_success);
        return $this->redirectToRoute('index');
        }
        return $this->render('FOSUserBundle:User:login.html.twig', array(
            'lastlogin' => $lastLogin,
            'errors'=> (string) $errors, 
        ));
    }

    public function showmeAction(Request $request){ //profil du user bonobo

        return $this->render('FOSUserBundle:User:showme.html.twig');
    }
    /**
     * @Route("/list")
     */
    public function listAction(){
        $userEntityManager = $this->getDoctrine()->getManager();
        $users = $userEntityManager->getRepository('FOSUserBundle:User')->findAll();

        return $this->render('FOSUserBundle:User:list.html.twig',[
            'users' => $users,
        ]);
    }    
    

    /**
     * @Route("/delete")
     */
    public function deleteAction($id)
    {
        $userEntityManager = $this->getDoctrine()->getManager();
        $user = $userEntityManager->getRepository('FOSUserBundle:User')->find($user->getId());

            if(!$user){
                $this->get('session')->getFlashBag()->add('response', 'Suppression de '.$user->getLogin().' échoué');
                return $this->redirectToRoute('user_delete');
            }else{
                $userEntityManager->remove($user);
                $userEntityManager->flush();
                $this->get('session')->getFlashBag()->add('success', $user->getLogin().'  was succefully erased');
                return $this->redirectToRoute('fos_user_index');
            }
    }

     /**
     * @Route("/add")
     */
    public function addAction(Request $request){
       
        $user = new User();
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user)
            ->add('login', TextType::class)
            ->add('age', TextType::class)
            ->add('family', TextType::class)
            ->add('race', TextType::class)
            ->add('foods', TextType::class)
            ->add('register', SubmitType::class);
    
        $form = $formBuilder->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $userEntityManager = $this->getDoctrine()->getManager();
                $userEntityManager->persist($user);
                $userEntityManager->flush();
                $message_success = "Dear " . $user->getLogin(). $user->getId(). ", is my new friend !";
                $this->addFlash('message_success', $message_success);
        return $this->redirectToRoute('user_list');
            }
        }
        return $this->render('FOSUserBundle:User:showme.html.twig', array('form' => $form->createView()));   
    }

     
     /**
     * @Route("/update")
     */
    public function updateAction(Request $request , User $user){
        $form = $this->createFormBuilder($user)
           ->add('login', TextType::class ,array('attr' => array(
                'placeholder'=>'Your username',
                'class'      =>'errors')))
            ->add('password', PasswordType::class ,array('attr' => array(
                'placeholder'=>'Your password',
                'class'      =>'errors')))
            ->add('age', IntegerType::class ,array('attr' => array(
                'placeholder'=> 'Your birthday',
                'class'      =>'errors')))
            ->add('family', TextType::class ,array('attr' => array(
                'placeholder'=> 'Your family',
                'class'      =>'errors' )))
            ->add('race', TextType::class ,array('attr' => array(
                'placeholder'=> 'Your race',
                'class'      =>'errors')))
            ->add('foods', TextType::class ,array('attr' => array(
                'placeholder'=> 'Your favorite foods',
                'class'      =>'errors')))
            ->add('Updated', SubmitType::class, array ('attr' => array( 
                'class'      =>'btn btn_success' , 
                'label'      =>'update')))
            ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {

                //éditer les champs désirés
                $editForm = $form->getData();
                $user->setLogin($editForm->getLogin());
                $user->setPassword($editForm->getPassword());
                $user->setAge($editForm->getAge());
                $user->setFamily($editForm->getFamily());
                $user->setRace($editForm->getRace());
                $user->setFoods($editForm->getFoods());

                //vérifier les erreurs
                $validator = $this->get('validator');
                $errors = $validator->validate($user);
                if (count($errors) ) {
                    // echo (String) $errors;
                    return $this->render('FOSUserBundle:User:update.html.twig', array(
                        'form'   => $form->createView(),
                        'errors' => (String) $errors
                    )); 
                } else {
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $user->setPassword($encoder->encodePassword($user->getPassword(),'toto'));
                    $user = $form->getData();
            
                    $userEntityManager = $this->getDoctrine()->getManager();
                    $userEntityManager->persist($user);//inserer en bdd
                    $userEntityManager->flush(); //actualiser les donnees
                    $message_success = "Dear " . $user->getLogin(). ", your profil was successfully updated !";
                    $this->addFlash('message_success', $message_success);
                    return $this->redirectToRoute('user_update', array(
                        'id' => $user->getId()
                        ));
                }
            }    
        return $this->render('FOSUserBundle:User:update.html.twig', array(
            'form'    => $form->createView()
            ));
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
