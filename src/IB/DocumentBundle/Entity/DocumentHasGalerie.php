<?php

namespace IB\DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DocumentHasGalerie
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
      * @ORM\ManyToOne(targetEntity="IB\DocumentBundle\Entity\Document", inversedBy="documentHasGaleries")
      */
    private $document;
    
    /**
      * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery")
      */
    private $galerie;

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
     * Set document
     *
     * @param \IB\DocumentBundle\Entity\Document $document
     * @return GalerieActeur
     */
    public function setDocument(\IB\DocumentBundle\Entity\Document $document)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * Get document
     *
     * @return \IB\DocumentBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set galerie
     *
     * @param \IB\DocumentBundle\Entity\Galerie $galerie
     * @return Galerie
     */
    public function setGalerie($galerie)
    {
        $this->galerie = $galerie;
        return $this;
    }

    /**
     * Get galerie
     *
     * @return \IB\DocumentBundle\Entity\Galerie 
     */
    public function getGalerie()
    {
        return $this->galerie;
    }
}