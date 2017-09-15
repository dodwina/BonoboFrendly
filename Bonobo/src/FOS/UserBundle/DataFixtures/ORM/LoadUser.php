<?php  
/* test de User */

namespace FOS\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Entity\User;
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
        $bonobo= new user(); 
        $bonobo->setlogin('Martin');
        //       $password = $passwordEncoder->encodePassword($bonobo, 'platypus');
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($bonobo, 'bonobo');
        $bonobo->setPassword($password);
        $bonobo->setAge(new \Date('2016-10-02'));
        $bonobo->setFamily('Hominidé');
        $bonobo->setRace('Primate');
        $bonobo->setfoods('banane');
        $manager->persist($bonobo);
        $manager->flush(); //enregistre
            
        /*user 2*/

        $bonobo2= new user();
        $bonobo2->setlogin('Joe');
        $encoder = $this->container->get('security.password_encoder');
        $password2 = $encoder->encodePassword($bonobo2, 'bonobo2');
       //        $password2 = $passwordEncoder->encodePassword($bonobo2, 'coding');
        $bonobo2->setPassword($password2);
        $bonobo2->setAge(new \Date('2016-01-01'));
        $bonobo2->setFamily('Hominini');
        $bonobo2->setRace('Primate');
        $bonobo->setfoods('poisson');
        $manager->persist($bonobo2);
        $manager->flush();
   }
}

?>