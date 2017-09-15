<?php

namespace FOS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/* relation entre entities vis-à-vis d'une liste d'objets (many to many)*/
use Doctrine\Common\Collections\ArrayCollection;
/* interface obligatoire pour gèrer la couche d'authentification de l'object user
 * a l'aide de methodes de classes communes a chaque projet*/
use Symfony\Component\Security\Core\User\UserInterface;
/* pr les champs uniques*/
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/* contraintes annotées pr valider les champs */
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="FOS\UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"login"}, message="This username is already taken")
 */
class User implements UserInterface {
    
    /**
    * @ORM\ManyToMany(targetEntity="FOS\UserBundle\Entity\user", cascade={"persist"})
    */
    private $users; /* notre liste d'amis*/
    
    public function __construct(){
        //$this->roles = array('ROLE_USER');
        //$this->setDateNaissance(new \DateTime('2016-01-01'));
        $this->users = new ArrayCollection(); /*instanciation de notre liste d'amis*/
    }
    

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, unique=true)
     * @Assert\Regex( pattern="^[A-Za-z]{2,50}$^", message = "Entrer un nom entre 2 et 50 caracteres."
     * )
     */
    private $login;

    /**
     * @var string
     * 
     * @ORM\Column(name="password", type="string", length=255, nullable=false, unique=true)
     */
   
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="family", type="string", length=255, nullable=true)
     */
    private $family;

    /**
     * @var string
     *
     * @ORM\Column(name="race", type="string", length=255, nullable=true)
     */
    private $race;

    /**
     * @var string
     *
     * @ORM\Column(name="foods", type="text", nullable=true)
     */
    private $foods;
    


    public function addUser(User $user){ 
        //ajout d'ami ds ma liste d'amis définie sous forme d'un array
        $this->users[] = $user;

    return $this;
    }

    public function removeUser(User $user){
        $this->users->removeElement($user);
    }

    public function getUsers(){
    return $this->users;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }


    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set age
     *
     * @param integer $age
     * 
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return datetime
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set family
     *
     * @param string $family
     *
     * @return User
     */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set race
     *
     * @param string $race
     *
     * @return User
     */
    public function setRace($race)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get race
     *
     * @return string
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * Set foods
     *
     * @param string $foods
     *
     * @return User
     */
    public function setFoods($foods)
    {
        $this->foods = $foods;

        return $this;
    }

    /**
     * Get foods
     *
     * @return string
     */
    public function getFoods()
    {
        return $this->foods;
    }

    /* 
     * Get Salt 
     * 
     */
    public function getSalt(){
        return NULL;
    }

    /**
     *methode attribuée à l'interface/ on ne l'utilise pas dans notre cas
      
    */
    public function eraseCredentials(){

    }

    /**
     *methode attribuée à l'interface/ on ne l'utilise pas dans notre cas
     * Get Username
     * 
     * @return string
     */
    
    public function getUsername(){

    }

    /**
     *methode attribuée à l'interface/on ne l'utilise pas ici
     * Get roles
     *
     * @return array
     */
    public function getRoles(){

    }


}

