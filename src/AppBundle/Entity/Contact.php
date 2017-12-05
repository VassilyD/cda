<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="contacts")
 */
class Contact
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 */
    private $name;

	/**
	 * @ORM\Column(type="string")
	 */
    private $surname;

	/**
	 * @ORM\Column(type="string")
	 */
    private $mail;

	/**
	 * @ORM\Column(type="string")
	 */
    private $phoneNumber;

    public function __construct($data)
    {
        if(!empty($data)) $this->hydrate($data);

        //return $this;
    }

    public function hydrate($donnees)
    {
        foreach ($donnees as $attribut => $valeur)
        {
	        $methode = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));
	             
	        if (is_callable(array($this, $methode)))
	        {
	        	$this->$methode($valeur);
	        }
        }

        //return $this;
    }
 

    public function getId() {
    	return $this->id;
    }

    public function getName() {
    	return $this->name;
    }

    public function getSurname() {
    	return $this->surname;
    }

    public function getMail() {
    	return $this->mail;
    }

    public function getPhoneNumber() {
    	return $this->phoneNumber;
    }

    public function setId($id) {
    	$this->id = $id;
    	return $this;
    }

    public function setName($name) {
    	$this->name = $name;
    	return $this;
    }

    public function setSurname($surname) {
    	$this->surname = $surname;
    	return $this;
    }

    public function setMail($mail) {
    	$mail = (string) $mail;
    	$length = strlen($mail);

    	if($length > 5 && $length < 255 && preg_match('/^[\w.-]{1,64}@[\w.-]{2,253}\.\w{2,6}$/', $mail))
    	{
    		$this->mail = $mail;
    	}
    	//else throw new Exception("Error Processing Request", 1);
    	//else throw {'bouuuh':'biiii'};

    	/* Meilleur façon de faire :
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
    		//L'email est bonne
		}
    	*/

    		return $this;
    	//return $this;
    }

    public function setPhoneNumber($phone) {
    	$phone = (string) $phone;
    	$phone = str_replace(' ', '', $phone);
    	$length = strlen($phone);

    	if($length > 7 && $length < 15 && preg_match('/^[+]?[\d]{7,15}$/', $phone)) 
    	{
    		$this->phoneNumber = $phone;
    	}

    		return $this;
    	//else throw new Exception("Error Processing Request", 1);
    	//else throw {'bouuuh':'biiii'};
    }
}
