<?php

namespace IB\SondageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reponse
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Reponse
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
     * @ORM\ManyToOne(targetEntity="IB\SondageBundle\Entity\Sondage", inversedBy="reponses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sondage;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse", type="string", length=255)
     * @Assert\NotNull()
     */
    private $reponse;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vote;

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
     * Set sondage
     *
     * @param string $sondage
     * @return sondage
     */
    public function setSondage(\IB\SondageBundle\Entity\Sondage $sondage)
    {
        $this->sondage = $sondage;

        return $this;
    }

    /**
     * Get sondage
     *
     * @return string 
     */
    public function getSondage()
    {
        return $this->sondage;
    }

    /**
     * Set reponse
     *
     * @param string $reponse
     * @return Reponse
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return string 
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     */
    public function incrementVote()
    {
       $this->vote += 1;
    }

    public function getVote()
    {
        return $this->vote;
    }

    /**
    * @ORM\PreRemove
    * @ORM\PreUpdate
    */
    public function deleteVotes()
    {
        $sondage = $this->getSondage();
        if (!empty($sondage)) {
            $sondage->setTotalVote($sondage->getTotalVote() - $this->getVote());
        }
    }
}
