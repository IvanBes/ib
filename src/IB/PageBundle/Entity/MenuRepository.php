<?php

namespace IB\PageBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuRepository extends NestedTreeRepository
{
  /**
   * {@inheritDoc}
   */
  public function menuChildrenHierarchy($node = null, $direct = false, array $options = array(), $includeNode = false)
  {
      $meta = $this->getClassMetadata();
      if ($node !== null) {
          if ($node instanceof $meta->name) {
              $wrapperClass = $this->_em instanceof \Doctrine\ORM\EntityManager ?
                  '\Gedmo\Tool\Wrapper\EntityWrapper' :
                  '\Gedmo\Tool\Wrapper\MongoDocumentWrapper';
              $wrapped = new $wrapperClass($node, $this->_em);
              if (!$wrapped->hasValidIdentifier()) {
                  throw new InvalidArgumentException("Node is not managed by UnitOfWork");
              }
          }
      } else {
          $includeNode = true;
      }
      // Gets the array of $node results. It must be ordered by depth
      $nodesQueryBuilder = $this->getNodesHierarchyQueryBuilder($node, $direct, $options, $includeNode);

      $nodesQueryBuilder->leftJoin('node.redirection', 'r')
                        ->addSelect('r')
                        ->leftJoin('node.modules', 'mo')
                        ->addSelect('mo')
                        ->addOrderBy('mo.priority', 'DESC');

      $nodes = $nodesQueryBuilder->getQuery()->getArrayResult();

      return $this->buildTree($nodes, $options);
  }

  public function getMenuLaterale()
    {      
        $qb = $this->CreateQueryBuilder('m')
                   ->innerJoin('m.parent', 'p')
                   ->leftJoin('m.contenus', 'c')
                   ->addSelect('c')
                   ->leftJoin('m.redirection', 'r')
                   ->addSelect('r')
                   ->leftJoin('m.modules', 'mo')
                   ->addSelect('mo')
                   ->addOrderBy('m.root', 'ASC')
                   ->addOrderBy('m.lft', 'ASC')
                   ->addOrderBy('mo.priority', 'DESC')
                   ->where('m.lvl = 1 and p.slug = :name')
                   ->setParameter('name', 'body');
        return  $qb->getQuery()->getResult();
    }

  public function getMenuFooter()
    {      
        $qb = $this->CreateQueryBuilder('m')
                   ->innerJoin('m.parent', 'p')
                   ->leftJoin('m.contenus', 'c')
                   ->addSelect('c')
                   ->leftJoin('m.redirection', 'r')
                   ->addSelect('r')
                   ->leftJoin('m.modules', 'mo')
                   ->addSelect('mo')
                   ->addOrderBy('m.root', 'ASC')
                   ->addOrderBy('m.lft', 'ASC')
                   ->addOrderBy('mo.priority', 'DESC')
                   ->where('m.lvl = 1 and p.name = :name')
                   ->setParameter('name', 'Footer');
        return  $qb->getQuery()->getResult();
    }

	public function getCheckMenuTransversaleBySlug($slug)
  	{
  	    $qb = $this->CreateQueryBuilder('m')
                   ->innerJoin('m.children', 'c')
                   ->leftJoin('m.modules', 'mo')
            	     ->where('m.slug = :slug AND m.lvl = 1 AND c IS NOT NULL')
                   ->setParameter('slug', $slug);
        return  $qb->getQuery()->getOneOrNullResult();
  	}

  	public function getRepertoire($url)
  	{
  	    $qb = $this->CreateQueryBuilder('m')
                   ->innerJoin('m.parent', 'p')
                   ->addSelect('p')
                   ->leftJoin('m.redirection', 'r')
                   ->addSelect('r')
  	    		       ->leftJoin('m.contenus', 'c')
              	   ->addSelect('c')
                   ->leftJoin('m.menuHasGaleries', 'mg')
                   ->addSelect('mg')
                   ->leftJoin('mg.galerie', 'g')
                   ->addSelect('g')
                   ->leftJoin('g.galleryHasMedias', 'gm')
                   ->addSelect('gm')
                   ->leftJoin('gm.media', 'media')
                   ->addSelect('media')
                   ->leftJoin('m.modules', 'mo')
                   ->addSelect('mo')
                   ->addOrderBy('mo.priority', 'DESC')
            	     ->add('where', 'm.url = :url')
                   ->setParameter('url', $url);

        return  $qb->getQuery()->getOneOrNullResult();
  	}
}