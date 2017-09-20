<?php  
/* test de User */

namespace FOS\UserBundle\DataFixtures\ORM;
use FOS\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUser implements FixtureInterface, ContainerAwareInterface {

    private $container;    

    public function setContainer(ContainerInterface $container = null){
       $this->container = $container;

    }   
    /*l'objet $manager est l'EntityManager*/
    public function load(ObjectManager $manager) {
        $friends= new user();
        $friends->setUsername('Martin');
        //       $password = $passwordEncoder->encodePassword($bonobo, 'platypus');
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($friends, 'bonobo');
        $friends->setPassword($password);
        $friends->setAge(new \DateTime());
        $friends->setFamily('Hominini');
        $friends->setRace('chimpenzé');
        $friends->setfoods('banane');
        $manager->persist($friends);
        $manager->flush(); //enregistre
            
        /*user 2*/

        $friends2= new user();
        $friends2->setUsername('Joe');
        $password2 = $encoder->encodePassword($friends2, 'bonobo2');
        $friends2->setPassword($password2);
        $friends2->setAge(new \DateTime());
        $friends2->setFamily('Hominini');
        $friends2->setRace('Primate');
        $friends2->setfoods('poisson');
        $manager->persist($friends2);
        $manager->flush();

        /*user 3*/
        $friends3= new user();
        $friends3->setUsername('damien');
        $password3 = $encoder->encodePassword($friends3, 'bonobo3');
        $friends3->setPassword($password3);
        $friends3->setAge(new \DateTime());
        $friends3->setFamily('Hominini');
        $friends3->setRace('Bonobo');
        $friends3->setfoods('miel');
        $manager->persist($friends3);
        $manager->flush();

        /*user 4*/

        $friends4= new user();
        $friends4->setUsername('Xavier');
        $password4 = $encoder->encodePassword($friends4, 'bonobo4');
        $friends4->setPassword($password4);
        $friends4->setAge(new \DateTime());
        $friends4->setFamily('Hominini');
        $friends4->setRace('Primate');
        $friends4->setfoods('poisson');
        $manager->persist($friends4);
        $manager->flush();
        /*user 5*/
        $friends5= new user();
        $friends5->setUsername('Katia');
        $password5 = $encoder->encodePassword($friends5, 'bonobo5');
        $friends5->setPassword($password5);
        $friends5->setAge(new \DateTime());
        $friends5->setFamily('Hominini');
        $friends5->setRace('Bonobo');
        $friends5->setfoods('insecte');
        $manager->persist($friends5);
        $manager->flush();
   }
}

?>