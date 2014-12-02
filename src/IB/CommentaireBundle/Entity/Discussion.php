<?php

namespace IB\CommentaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IB\CommentaireBundle\Model\DiscussionInterface;
use IB\CommentaireBundle\Model\CommentaireInterface;

/**
 * Discussion
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Discussion implements DiscussionInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var slug
     * @ORM\Column(type="string")
     */
    protected $slug;    

    /**
     * @var slug
     * @ORM\Column(type="string")
     */
    protected $titre;

    /**
     * @var classname
     * @ORM\Column(type="string")
     */
    protected $classname;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCommentable", type="boolean")
     */
    private $isCommentable = true;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    */
    protected $lastUpdate = null;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="discussion", cascade={"remove"})
     */
    private $commentaires;
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commentaires = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set titre
     *
     * @param string $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
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
     * Set classname
     *
     * @param array $classname
     * @return Discussion
     */
    public function setClassname($classname)
    {
        $this->classname = $classname;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return array 
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * Set isCommentable
     *
     * @param boolean $isCommentable
     * @return Discussion
     */
    public function setIsCommentable($isCommentable)
    {
        $this->isCommentable = (bool) $isCommentable;
    
        return $this;
    }

    /**
     * Get isCommentable
     *
     * @return boolean 
     */
    public function isCommentable()
    {
        return $this->isCommentable;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     * @return Discussion
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    
        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime 
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Add commentaires
     *
     * @param \IB\CommentaireBundle\Entity\Commentaire $commentaires
     * @return Discussion
     */
    public function addCommentaire(\IB\CommentaireBundle\Entity\Commentaire $commentaires)
    {
        $this->commentaires[] = $commentaires;
    
        return $this;
    }

    /**
     * Remove commentaires
     *
     * @param \IB\CommentaireBundle\Entity\Commentaire $commentaires
     */
    public function removeCommentaire(\IB\CommentaireBundle\Entity\Commentaire $commentaires)
    {
        $this->commentaires->removeElement($commentaires);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }
}