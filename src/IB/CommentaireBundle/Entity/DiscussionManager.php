<?php
namespace IB\CommentaireBundle\Entity;

use Doctrine\ORM\EntityManager;
use IB\CommentaireBundle\Model\DiscussionInterface;
use IB\CommentaireBundle\Model\DiscussionManager as BaseDiscussionManager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM DiscussionManager.
 */
class DiscussionManager extends BaseDiscussionManager
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
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, $configuration, EntityManager $em, $class)
    {
        parent::__construct($dispatcher, $configuration);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Finds one commentzire Discussion by the given criteria
     *
     * @param  array           $criteria
     * @return DiscussionInterface
     */
    public function findOneDiscussionBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findDiscussionsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Finds all Discussions.
     *
     * @return array of DiscussionInterface
     */
    public function findAllDiscussions()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewDiscussion(DiscussionInterface $discussion)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($discussion);
    }

    /**
     * Saves a Discussion
     *
     * @param DiscussionInterface $Discussion
     */
    protected function doSaveDiscussion(DiscussionInterface $discussion)
    {
        $this->em->persist($discussion);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified commentaire Discussion class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get the entity of relation with check
     *
     * @param DiscussionInterface $Discussion
     */
    protected function checkRepoByslug($slug)
    {   
        if ($this->repositoryOfRelation)
        {
            $reflectionClass = new \ReflectionClass($this->repositoryOfRelation);
            if ($reflectionClass->implementsInterface('IB\CommentaireBundle\Model\IBDiscussionInterface')) 
            {
                $q = $this->em->getRepository($this->repositoryOfRelation)
                ->createQueryBuilder('o')
                ->where('o.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
    
                if(null !== $q) return $q;               
            }
        }
        
        return false;
    }
}
