<?php
namespace IB\CommentaireBundle\Entity;

use Doctrine\ORM\EntityManager;
use IB\CommentaireBundle\Model\CommentaireManager as BaseCommentaireManager;
use IB\CommentaireBundle\Model\DiscussionInterface;
use IB\CommentaireBundle\Model\CommentaireInterface;
use IB\CommentaireBundle\Sorting\SortingFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM CommentaireManager.
 */
class CommentaireManager extends BaseCommentaireManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \IB\CommentaireBundle\Sorting\SortingFactory                   $factory
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        parent::__construct($dispatcher);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;

        $em->getFilters()->enable('softdeleteable');
    }

    public function countCommentairesByDiscussion($idDiscussion)
    {
      return $this->repository->createQueryBuilder('c')
                  ->select('COUNT(c)')
                  ->innerJoin('c.discussion', 'd')
                  ->where('d.id = :discussion')
                  ->andWhere('c.parent IS NULL')
                  ->setParameter('discussion', $idDiscussion)
                  ->getQuery()
                  ->getSingleScalarResult();
    }

    /**
     * Returns a flat array of commentaires of a specific discussion.
     *
     * @param  discussionInterface $discussion
     * @return array           of discussionInterface
     */
    public function findCommentairesBydiscussion(discussionInterface $discussion)
    {
        $idDiscussion = $discussion->getId();
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->innerJoin('c.discussion', 'd')
                ->leftJoin('c.children', 'children')
                ->addSelect('children')
                ->innerJoin('c.account', 'u')
                ->addSelect('partial u.{id, username}')
                ->innerJoin('u.avatar', 'p')
                ->addSelect('partial p.{id, filename}')
                ->where('d.id = :discussion')
                ->andWhere('c.parent IS NULL')
                ->orderBy('c.date', 'DESC')
                ->setParameter('discussion', $idDiscussion);

        $commentaires = $qb
            ->getQuery()->setHint('knp_paginator.count', $this->countCommentairesByDiscussion($idDiscussion));

        return $commentaires;
    }    

    /**
     * Returns a flat array of commentaires of a specific discussion.
     *
     * @param  discussionInterface $discussion
     * @return array           of discussionInterface
     */
    public function findDraftCommentairesByDiscussion(discussionInterface $discussion, $lastFictifDate)
    {
        $lastFictifDate = new \datetime($lastFictifDate);
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->innerJoin('c.discussion', 'd')
                ->leftJoin('c.children', 'children')
                ->addSelect('children')
                ->innerJoin('c.account', 'u')
                ->addSelect('partial u.{id, username}')
                ->innerJoin('u.avatar', 'p')
                ->addSelect('partial p.{id, filename}')
                ->where('d.id = :discussion')
                ->andWhere('c.parent IS NULL')
                ->andWhere('c.date > :lastFictifDate AND c.date <= :lastReelDate')
                ->orderBy('c.date', 'DESC')
                ->setParameter('discussion', $discussion->getId())
                ->setParameter('lastFictifDate', $lastFictifDate->format('Y-m-d H:i:s'))
                ->setParameter('lastReelDate', $discussion->getLastUpdate()->format('Y-m-d H:i:s'));

        $commentaires = $qb->getQuery()->getResult();
        return $commentaires;
    }

    public function findDraftChildrensByCommentaireAndDiscussion(discussionInterface $discussion, $id_commentaire_parent, $lastFictifDate)
    {
        $lastFictifDate = new \datetime($lastFictifDate);
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->innerJoin('c.discussion', 'd')
                ->leftJoin('c.children', 'children')
                ->addSelect('children')
                ->innerJoin('c.account', 'u')
                ->addSelect('partial u.{id, username}')
                ->innerJoin('u.avatar', 'p')
                ->addSelect('partial p.{id, filename}')
                ->where('c.id = :idcommentaire')
                ->andWhere('d.id = :discussion')
                ->andWhere('children.date > :lastFictifDate AND children.date <= :lastReelDate')
                ->setParameter('discussion', $discussion->getId())
                ->setParameter('idcommentaire', $id_commentaire_parent)
                ->setParameter('lastFictifDate', $lastFictifDate->format('Y-m-d H:i:s'))
                ->setParameter('lastReelDate', $discussion->getLastUpdate()->format('Y-m-d H:i:s'));

        $commentaire = $qb->getQuery()->getSingleResult();
        return $commentaire;
    }

    /**
     * Find one commentaire by its ID and Discussion
     *
     * @return Commentaire or null
     **/
    public function findCommentaireByDiscussionAndId(discussionInterface $discussion, $id)
    {
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->innerJoin('c.discussion', 'd')
                ->innerJoin('c.account', 'u')
                ->addSelect('partial u.{id, username}')
                ->innerJoin('u.avatar', 'p')
                ->addSelect('partial p.{id, filename}')
                ->where('d.id = :discussion AND c.id = :idcommentaire')
                ->setParameter('discussion', $discussion->getId())
                ->setParameter('idcommentaire', $id);

        $commentaire = $qb
            ->getQuery()
            ->getSingleResult();

        return $commentaire;
    }

    /**
     * Find one commentaire by its ID
     *
     * @return Commentaire or null
     **/
    public function findCommentaireById($id)
    {
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->innerJoin('c.discussion', 'd')
                ->addSelect('partial d.{id, isCommentable}')
                ->innerJoin('c.account', 'u')
                ->addSelect('u')
                ->where('c.id = :idcommentaire')
                ->setParameter('idcommentaire', $id);

        $commentaire = $qb
            ->getQuery()
            ->getSingleResult();

        return $commentaire;
    }

    /**
     * get last commentaires
     *
     * @return Commentaires or null
     **/
    public function getListCommentaires($limit)
    {
        $qb = $this->repository
                   ->createQueryBuilder('c')
                   ->innerJoin('c.discussion', 'd')
                   ->addSelect('d')
                   ->innerJoin('c.account', 'u')
                   ->addSelect('partial u.{id, username}')
                   ->innerJoin('u.avatar', 'p')
                   ->addSelect('partial p.{id, filename}')
                   ->orderBy('c.date', 'DESC')
                   ->where('c.parent IS NULL')
                   ->setMaxResults($limit);

        $commentaires = $qb->getQuery()->getResult();
        return $commentaires;
    }

    /**
     * Performs persisting of the commentaire.
     *
     * @param CommentaireInterface $commentaire
     */
    protected function doSaveCommentaire(CommentaireInterface $commentaire)
    {
        $this->em->persist($commentaire->getdiscussion());
        $this->em->persist($commentaire);
        $this->em->flush();
    }

    /**
     * Performs persisting of the commentaire.
     *
     * @param CommentaireInterface $commentaire
     */
    protected function removeCommentaire(CommentaireInterface $commentaire)
    {
        $this->em->remove($commentaire);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewCommentaire(CommentaireInterface $commentaire)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($commentaire);
    }

    /**
     * Returns the fully qualified commentaire discussion class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}