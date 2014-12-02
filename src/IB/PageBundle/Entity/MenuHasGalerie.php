<?php

namespace IB\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MenuHasGalerie
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
      * @ORM\ManyToOne(targetEntity="IB\PageBundle\Entity\Menu", inversedBy="menuHasGaleries")
      */
    private $menu;
    
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
     * Set menu
     *
     * @param \IB\PageBundle\Entity\Menu $menu
     * @return GalerieActeur
     */
    public function setMenu(\IB\PageBundle\Entity\Menu $menu)
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * Get menu
     *
     * @return \IB\PageBundle\Entity\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set galerie
     *
     * @param \IB\PageBundle\Entity\Galerie $galerie
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
     * @return \IB\PageBundle\Entity\Galerie 
     */
    public function getGalerie()
    {
        return $this->galerie;
    }
}