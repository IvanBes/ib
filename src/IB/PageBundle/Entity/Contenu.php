<?php

namespace IB\PageBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Contenu
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Contenu
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
     * @ORM\ManyToOne(targetEntity="IB\PageBundle\Entity\Menu", inversedBy="contenus", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $menu;

    /**
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "3",
     *      max = "60"
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
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modificationDate", type="datetime")
     */
    private $modificationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type = 'default';

    public function __toString()
    {
        return '';
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
     * Set menu
     *
     * @param string $menu
     * @return menu
     */
    public function setMenu(\IB\PageBundle\Entity\Menu $menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return string 
     */
    public function getMenu()
    {
        return $this->menu;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Contenu
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set modificationDate
     *
     * @param \DateTime $modificationDate
     * @return Contenu
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * Get modificationDate
     *
     * @return \DateTime 
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return contenu
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
