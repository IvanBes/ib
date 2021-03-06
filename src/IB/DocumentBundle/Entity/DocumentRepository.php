<?php

namespace IB\DocumentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends EntityRepository
{
	public function myFindOneByMenu($menu)
	{
	      $qb = $this->CreateQueryBuilder('d')
	      ->innerJoin('d.documentHasGaleries', 'dg')
	      ->addSelect('dg')
	      ->innerJoin('dg.galerie', 'g')
	      ->addSelect('g')
	      ->innerJoin('g.galleryHasMedias', 'gm')
	      ->addSelect('gm')
	      ->add('where', 'd.menu = :id AND g.enabled = 1 AND gm.enabled = 1')
	      ->orderBy('gm.position', 'ASC')
	      ->setParameter('id', $menu->getId());
	      
	      return $qb->getQuery()->getArrayResult();
	}

	public function getPagination($galerie, $count)
	{
    	$qb = $this->_em->createQueryBuilder()
                              ->select('gm')
                              ->from('Application\Sonata\MediaBundle\Entity\GalleryHasMedia', 'gm')
                              ->innerJoin('gm.gallery', 'g')
	      					  ->innerJoin('gm.media', 'm')
	      					  ->addSelect('m')
	      					  ->add('where', 'g.id = :id AND g.enabled = 1 AND gm.enabled = 1 AND m.enabled = 1')
	      					  ->orderBy('gm.position', 'ASC')
	      					  ->setParameter('id', $galerie['id']);

		return $qb->getQuery()->setHint('knp_paginator.count', $count);
	}
}