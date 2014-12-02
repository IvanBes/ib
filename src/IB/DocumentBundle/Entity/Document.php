<?php

namespace IB\DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IB\PageBundle\Entity\Menu;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="IB\DocumentBundle\Entity\DocumentRepository")
 */
class Document
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
     * @ORM\OneToOne(targetEntity="IB\PageBundle\Entity\Menu")
    */
    private $menu;

    /**
     * @ORM\OneToMany(targetEntity="IB\DocumentBundle\Entity\DocumentHasGalerie", mappedBy="document", cascade={"all"}, orphanRemoval=true)
     */
    private $documentHasGaleries;

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
     * {@inheritdoc}
     */
    public function setDocumentHasGaleries($documentHasGaleries)
    {
        $this->DocumentHasGaleries = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($documentHasGaleries as $documentHasGalerie) {
            $this->addDocumentHasGalerie($documentHasGalerie);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addDocumentHasGalerie(DocumentHasGalerie $documentHasGalerie)
    {
        $documentHasGalerie->setDocument($this);
        $this->documentHasGaleries[] = $documentHasGalerie;
    }

    public function removeDocumentHasGalerie(DocumentHasGalerie $documentHasGalerie)
    {
        $this->documentHasGaleries->removeElement($documentHasGalerie);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentHasGaleries()
    {
        return $this->documentHasGaleries;
    }

    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function getMenu()
    {
        return $this->menu;
    }
}
