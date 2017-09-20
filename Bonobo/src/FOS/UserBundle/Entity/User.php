<?php

namespace FOS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
// relation entre entities vis-à-vis d'une liste d'objets (many to many)*/
use Doctrine\Common\Collections\ArrayCollection;
// pr les champs uniques*/
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//interface obligatoire pour gèrer la couche d'authentification de l'object user
 // a l'aide de methodes de classes communes a chaque projet*/
use Symfony\Component\Security\Core\User\UserInterface;
/* contraintes annotées pr valider les champs */
use Symfony\Component\Validator\Constraints\date;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Serializer;
use Serializable;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="FOS\UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"username", "password"}, message="This username is already taken")
 */
class User implements UserInterface, \Serializable {
    
    /**
    * @var User $friends
    *
     * @ORM\ManyToMany(targetEntity="FOS\UserBundle\Entity\User")
    * @ORM\Column(name="friends", type="array", nullable=true)
     *
    */
    private $friends =[]; /* notre liste d'amis*/
    
    public function __construct(){
        $this->roles = array('ROLE_USER');
        //$this->setDateNaissance(new \DateTime('2016-01-01'));
        $this->friends = []; /*instanciation de notre liste d'amis*/
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @Assert\Regex( pattern="^[A-Za-z]{2,50}$^", message = "Entrer un nom entre 2 et 50 caracteres."
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false, unique=true)
     */
   
    private $password;

    /**
     * @var date

     * @ORM\Column(name="age", type="date")
     */
    private $age;

    /**
     * @var array
     *
     * @ORM\Column(name="family", type="array")
     */
    private $family;

    /**
     * @var array
     *
     * @ORM\Column(name="race", type="array")
     */
    private $race;


    /**
     * @var array
     * @ORM\Column(name="roles", type="array" , nullable=true )
     */
    private $roles;


    /**
     * @var string
     *
     * @ORM\Column(name="foods", type="text", nullable=true)
     */
    private $foods;


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
     * Set user
     *
     * @param string $user
     *
     * @return User
     */
    public function setUsername($Username)
    {
        $this->username = $Username;

        return $this;
    }

    /**
     * Get User
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
     * @param date $age
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
     * @return date
     */
    public function getAge()
    {
        return $this->age;
    }

    public function calculateAge() {
        $age = date_diff($this->getAge() , date_create('today'))->y;
        return $age;
    }
    /**
     * Set family
     *
     * @param array $family
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
     * @return array
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set race
     *
     * @param array $race
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
     * @return array
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

    /**
     * @param $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    public function setFriends($Friends)
    {
        $this->friends = $Friends;

        return $this;
    }
    public function getFriends()
    {
        return $this->friends;
    }


    public function addfriend($friends){
        //ajout d'ami ds ma liste d'amis définie sous forme d'un array
        $this->friends[] = $friends;

        return $this;
    }

    public function removefriend(User $friend){
        $this->friends->removeElement($friend);
    }


    /**
     *methode attribuée à l'interface/ on ne l'utilise pas dans notre cas
     */
    public function eraseCredentials(){

    }

    /**
     *methode attribuée à l'interface/ on ne l'utilise pas dans notre cas

     */

    #public function getUsername(){}


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,

        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,

             ) = unserialize($serialized);
    }

    /*
    * Get Salt
    * @return string|null the salt
    */
    public function getSalt(){
        return NULL;
    }
}

