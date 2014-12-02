<?php

namespace IB\SondageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use IB\LikeBundle\Model\IBLikeVoterInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IB\SondageBundle\Entity\Sondage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SondageRepository")
 */
class Sondage implements IBLikeVoterInterface
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
     * @var string $question
     * @Assert\NotNull()
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;

    /**
     * @var string $reponse
     * @ORM\OneToMany(targetEntity="IB\SondageBundle\Entity\Reponse", mappedBy="sondage", cascade={"persist"})
     * @Assert\Valid()
     */
    private $reponses;

    /**
     * @var datetime $date
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime")
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
     * @var integer $totalVote
     *
     * @ORM\Column(type="integer")
     */
    private $totalVote = 0;

    /**
     * @var boolean $close
     *
     * @ORM\Column(name="close", type="boolean")
     */
    private $close = false;
    
    /**
     * @ORM\ManyToMany(targetEntity="IB\LikeBundle\Entity\IPAddress", cascade={"persist"})
     * @Assert\Valid()
     */
    private $voteIpAdress;

    public function __construct()
    {
        $this->reponses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->voteIpAdress = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set question
     *
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * {@inheritdoc}
     */
    public function setReponses($reponses)
    {
        $this->reponses = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($reponses as $reponse) {
            $this->addReponse($reponse);
        }
    }

    public function addReponse(\IB\SondageBundle\Entity\Reponse $reponse)
    {        
        $this->reponses[] = $reponse;
        $reponse->setSondage($this);
        return $this;
    }
    
    public function removeReponse(\IB\SondageBundle\Entity\Reponse $reponse)
    {
        $this->reponses->removeElement($reponse);
    }
    
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Set VoteChoice
     *
     * @param $VoteChoice
     */
    public function setVoteChoice($voteChoice)
    {  
        foreach($this->getReponses() as $reponse)
        {
            if ($reponse->getId() == $voteChoice[0]) {                
                $reponse->incrementVote();
            }
        }
    }

    /**
     * get VoteChoice
     *
     * @param $VoteChoice
     */
    public function getVoteChoice()
    {  
        return;
    }

    public function getChoices()
    {                
        $choicesArray = new \Doctrine\Common\Collections\ArrayCollection();
        foreach($this->getReponses() as $reponse)
        {
            $choicesArray->offsetSet($reponse->getId(), $reponse->getReponse());
        }

        return $choicesArray->toArray();
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
     * Set close
     *
     * @param boolean $close
     */
    public function setClose($close)
    {
        $this->close = (bool) $close;
    }

    /**
     * Get close
     *
     * @return boolean 
     */
    public function isClose()
    {
        return $this->close;
    }

 	/**
     * Get totalVote
     *
     * @return integer 
     */
    public function getTotalVote()
    {
        return $this->totalVote;
    }

    /**
     * Set totalVote
     *
     * @param integer $totalVote
     */
    public function setTotalVote($totalVote)
    {
       $this->totalVote = $totalVote;
    }

    /**
     * Set totalVote
     *
     * @param integer $totalVote
     */
    public function incrementTotalVote()
    {
       $this->totalVote += 1;
    }

    /**
     * Add voteIpAdress
     *
     * @param \IB\LikeBundle\Entity\IPAddress $voteIpAdress
     * @return Sondage
     */
    public function addVoteIpAdres(\IB\LikeBundle\Entity\IPAddress $voteIpAdress)
    {
        $this->voteIpAdress[] = $voteIpAdress;
        $this->incrementTotalVote();
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
}