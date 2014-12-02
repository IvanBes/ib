<?php

namespace IB\ArticleBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

use Application\Sonata\UserBundle\Entity\User;
use IB\LikeBundle\Model\IBLikeVoterInterface;
use IB\CommentaireBundle\Model\IBDiscussionInterface;
use IB\CommentaireBundle\Entity\Commentaire;
use IB\PageBundle\Entity\Menu;

/**
 * IB\ArticleBundle\Entity\Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="IB\ArticleBundle\Entity\ArticleRepository")
 */
class Article implements IBLikeVoterInterface, IBDiscussionInterface
{
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @var date $date
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var date $date
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $modificationDate;

    /**
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "10",
     *      max = "20"
     * )
     */
    private $titre;
    
    /**
     * @var text $contenu
     *
     * @ORM\Column(name="contenu", type="text")
     * @Assert\Length(
     *      min = "20"
     * )
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="rawContenu", type="text")
    */
    private $rawContenu;

    /**
     * @var string
     *
     * @ORM\Column(name="contenuFormatter", type="text")
     */
    private $contenuFormatter;

    /**
    * @ORM\Column(length=16)
    */
    private $code;

    /**
     * @Gedmo\Slug(fields={"code", "titre"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="IB\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\ManyToMany(targetEntity="IB\LikeBundle\Entity\IPAddress", cascade={"persist"})
     * @Assert\Valid()
     */
    private $voteIpAdress;

    /**
     * @var boolean $published
     *
     * @ORM\Column(name="publier", type="boolean")
     */
    private $publier;

    /**
    * @ORM\Column(name="rating", type="integer")
    */
    private $rating = 0;

    /**
    * @ORM\ManyToMany(targetEntity="IB\PageBundle\Entity\Menu")
    */
    private $menus;
    
	public function __construct()
	{
        $this->voteIpAdress = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->code = hash('crc32b', uniqid());
	}

	/**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set titre
     *
     * @param string $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param text $contenu
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    /**
     * Get contenu
     *
     * @return text
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * {@inheritdoc}
     */
    public function getContenuFormatter()
    {
        return $this->contenuFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setContenuFormatter($contenuFormatter)
    {
        $this->contenuFormatter = $contenuFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawContenu($rawContenu)
    {
        $this->rawContenu = $rawContenu;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawContenu()
    {
        return $this->rawContenu;
    }

    /**
     * Set account
     *
     */
    public function setAccount(User $account)
    {
        $this->account = $account;
    }

    /**
     * Get account
     *
     * @return integer
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set publier
     *
     * @param boolean $publier
     * @return Article
     */
    public function setPublier($publier)
    {
        $this->publier = $publier;
    }

    /**
     * Get publier
     *
     * @return boolean 
     */
    public function getPublier()
    {
        return $this->publier;
    }
    
    /**
     * Set modificationDate
     *
     * @param \DateTime $modificationDate
     * @return Article
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * Get
     *
     * @return \DateTime 
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Article
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Add voteIpAdress
     *
     * @param \IB\LikeBundle\Entity\IPAddress $voteIpAdress
     * @return Article
     */
    public function addVoteIpAdres(\IB\LikeBundle\Entity\IPAddress $voteIpAdress)
    {
        $this->voteIpAdress[] = $voteIpAdress;
    
        return $this;
    }

    /**
     * Remove voteIpAdress
     *
     * @param \IB\LikeBundle\Entity\IPAddress $voteIpAdress
     */
    public function removeVoteIpAdres(\IB\LikeBundle\Entity\IPAddress $voteIpAdress)
    {
        $this->voteIpAdress->removeElement($voteIpAdress);
    }

    /**
     * Get voteIpAdress
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVoteIpAdress()
    {
        return $this->voteIpAdress;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Article
     */
    public function setRatingByIncrement()
    {
        $this->rating += 1;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
      * Add menu
      *
      * @param Menu $menu
      */
    public function addMenu(Menu $menu) // addMenu sans « s » !
    {
      // Ici, on utilise l'ArrayCollection vraiment comme un tableau, avec la syntaxe []
      $this->menus[] = $menu;
    }
    
    /**
      * Remove menu
      *
      * @param Menu $menu
      */
    public function removeMenu(Menu $menu) // removeMenu sans « s » !
    {
      // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
      $this->menus->removeElement($menu);
    }
    
    /**
      * Get menu
      *
      * @return Doctrine\Common\Collections\Collection
      */
    public function getMenus() // Notez le « s », on récupère une liste de catégories ici !
    {
      return $this->menus;
    }
}