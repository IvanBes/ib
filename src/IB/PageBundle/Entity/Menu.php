<?php

namespace IB\PageBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Menu
 * @Gedmo\Tree(type="nested")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="IB\PageBundle\Entity\MenuRepository")
 */
class Menu
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="IB\PageBundle\Entity\Contenu", mappedBy="menu", cascade={"all"}, orphanRemoval=true)
     */
    private $contenus;

    /**
     * @ORM\OneToMany(targetEntity="IB\PageBundle\Entity\MenuHasGalerie", mappedBy="menu", cascade={"all"}, orphanRemoval=true)
     */
    private $menuHasGaleries;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type = 'default';

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
    */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="Menu")
     * @ORM\JoinColumn(name="redirection_id", referencedColumnName="id")
    */
    private $redirection;

    /**
    * @ORM\ManyToMany(targetEntity="Module")
    */
    private $modules;

    public function __construct()
    {
      $this->contenus = new \Doctrine\Common\Collections\ArrayCollection();
      $this->galeries = new \Doctrine\Common\Collections\ArrayCollection();
      $this->modules = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        $prefix = "";
        for ($i=1; $i <= $this->lvl; $i++){
            $prefix .= "| - - - ";
        }
        return $prefix . $this->name;
    }

    public function getLaveledTitle()
    {
        return (string)$this;
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
     * Set name
     *
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Menu
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
     * {@inheritdoc}
     */
    public function setContenus($contenus)
    {
        $this->contenus = new \Doctrine\Common\Collections\ArrayCollection();

        if (!empty($contenus))
        {
            foreach ($contenus as $contenu) 
            {
                $this->addContenu($contenu);
            }
        }
    }

    public function addContenu(\IB\PageBundle\Entity\Contenu $contenu)
    {        
        $this->contenus[] = $contenu;
        $contenu->setMenu($this);
        return $this;
    }
    
    public function removeContenu(\IB\PageBundle\Entity\Contenu $contenu)
    {
        $this->contenus->removeElement($contenu);
    }
    
    public function getContenus()
    {
        return $this->contenus;
    }

    /**
     * {@inheritdoc}
     */
    public function setMenuHasGaleries($menuHasGaleries)
    {
        $this->MenuHasGaleries = new \Doctrine\Common\Collections\ArrayCollection();
        
        if (!empty($menuHasGaleries))
        {
            foreach ($menuHasGaleries as $menuHasGalerie) {
                $this->addMenuHasGalerie($menuHasGalerie);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addMenuHasGalerie(MenuHasGalerie $menuHasGalerie)
    {
        $menuHasGalerie->setMenu($this);
        $this->menuHasGaleries[] = $menuHasGalerie;
    }

    public function removeMenuHasGalerie(MenuHasGalerie $menuHasGalerie)
    {
        $this->menuHasGaleries->removeElement($menuHasGalerie);
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuHasGaleries()
    {
        return $this->menuHasGaleries;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Menu
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

    /**
     * Set lft
     *
     * @param integer $lft
     * @return Menu
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return Menu
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return Menu
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return Menu
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param string $parent
     * @return Menu
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return string 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param string $children
     * @return Menu
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return string 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Menu
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

   /**
     * Set redirection
     *
     * @param \Redirection $redirection
     * @return Menu
     */
    public function setRedirection($redirection)
    {
        $this->redirection = $redirection;

        return $this;
    }

    /**
     * Get redirection
     *
     * @return \Redirection 
     */
    public function getRedirection()
    {
        return $this->redirection;
    }

    /**
      * Add modules
      *
      * @param Module $modules
      */
    public function addModule(Module $module) // addModule sans « s » !
    {
      // Ici, on utilise l'ArrayCollection vraiment comme un tableau, avec la syntaxe []
      $this->modules[] = $module;
    }
    
    /**
      * Remove modules
      *
      * @param Module $modules
      */
    public function removeModule(Module $module) // removeModule sans « s » !
    {
      // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
      $this->modules->removeElement($module);
    }
    
    /**
      * Get modules
      *
      * @return Doctrine\Common\Collections\Collection
      */
    public function getModules() // Notez le « s », on récupère une liste de catégories ici !
    {
      return $this->modules;
    }
}
