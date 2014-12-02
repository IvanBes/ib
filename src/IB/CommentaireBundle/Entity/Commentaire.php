<?php

namespace IB\CommentaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Application\Sonata\UserBundle\Entity\User;
use IB\CommentaireBundle\Model\DiscussionInterface;
use IB\CommentaireBundle\Model\CommentaireInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IB\CommentaireBundle\Entity\Commentaire
 * @Gedmo\Tree(type="nested")
 * @ORM\Table()
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Commentaire implements CommentaireInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="IB\UserBundle\Entity\User", inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @var text $Commentaire
     * @ORM\Column(name="Commentaire", type="text") 
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "5",
     *      max = "200"
     * )
     */
    private $commentaire;

    /**
     * @var datetime $Date
     * @ORM\Column(type="datetime")
     */
    private $date;

   /**
     * @var datetime $contentChanged
     *
     * @ORM\Column(name="content_changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"commentaire"})
     */
    private $contentChanged;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * discussion of this comment
     *
     * @var discussion
     * @ORM\ManyToOne(targetEntity="Discussion", inversedBy="commentaires", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $discussion;

    /**
     * @ORM\ManyToMany(targetEntity="IB\LikeBundle\Entity\IPAddress", cascade={"persist"})
     * @Assert\Valid()
     */
    private $voteIpAdress;

    /**
    * @ORM\Column(name="rating", type="integer")
    */
    private $rating = 0;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Commentaire", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="parent", cascade={"remove"})
     */
    private $children;
    
	public function __construct() 
	{	
		$this->date = new \Datetime();
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
     * Set Commentaire
     *
     * @param text $commentaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    }

    /**
     * Get Commentaire
     *
     * @return text 
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set Date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get Date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * Set discussion
     *
     * @param \IB\CommentaireBundle\Entity\Thread $discussion
     * @return Commentaire
     */
    public function setDiscussion(DiscussionInterface $discussion)
    {
        $this->discussion = $discussion;
        $discussion->addCommentaire($this);
        return $this;
    }

    /**
     * Get discussion
     *
     * @return \IB\CommentaireBundle\Entity\Thread 
     */
    public function getDiscussion()
    {
        return $this->discussion;
    }

    /**
     * Add voteIpAdress
     *
     * @param \IB\LikeBundle\Entity\IPAddress $voteIpAdress
     * @return Commentaire
     */
    public function addVoteIpAdress(\IB\LikeBundle\Entity\IPAddress $voteIpAdress)
    {
        $this->voteIpAdress[] = $voteIpAdress;
    
        return $this;
    }

    /**
     * Remove voteIpAdress
     *
     * @param \IB\LikeBundle\Entity\IPAddress $voteIpAdress
     */
    public function removeVoteIpAdress(\IB\LikeBundle\Entity\IPAddress $voteIpAdress)
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
     * @return Commentaire
     */
    public function setRatingByIncrement()
    {
        $this->rating += 1;
    
        return $this;
    }

    public function setParent(Commentaire $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getLvl()
    {
        return $this->lvl;
    }    
}