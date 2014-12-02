<?php

namespace IB\CommentaireBundle\Model;

use IB\CommentaireBundle\Events;
use IB\CommentaireBundle\Event\DiscussionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Discussion Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class DiscussionManager implements DiscussionManagerInterface
{
    protected $dispatcher;
    protected $configuration;
    protected $repositoryOfRelation = null;

    public function __construct(EventDispatcherInterface $dispatcher, $configuration)
    {
        $this->dispatcher = $dispatcher;
        $this->configuration = $configuration;
    }

    public function getRepositoryOfRelation()
    {
        return $this->repositoryOfRelation;
    }

    public function init($entity)
    {       
        $entity = ucfirst($entity);

        foreach ($this->configuration as $classname) 
        {
            if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) 
            {
                if ($matches[1] === $entity) {
                  $this->repositoryOfRelation = $classname;
                  return true;
                }
            }
        }

        unset($value);
        return false;
    }

    /**
     * @param  string          $id
     * @return DiscussionInterface
     */
    public function findDiscussionById($id)
    {
        return $this->findOneDiscussionBy(array('id' => $id));
    }

    /**
     * Creates an empty commentaire Discussion instance
     *
     * @return Discussion
     */
    public function createDiscussion($slug)
    {
        if (null !== $slug && $this->repositoryOfRelation && $entityOfRelation = $this->checkRepoByslug($slug)) 
        {
            $class = $this->getClass();
            $discussion = new $class;
            $discussion->setSlug($entityOfRelation->getSlug())
                       ->setTitre($entityOfRelation->getTitre())
                       ->setClassname($this->repositoryOfRelation);
 
            $event = new DiscussionEvent($discussion);
            $this->dispatcher->dispatch(Events::DISCUSSION_CREATE, $event);
    
            return $discussion;
        }

        return false;
    }

    /**
     * Persists a Discussion.
     *
     * @param DiscussionInterface $Discussion
     */
    public function saveDiscussion(DiscussionInterface $discussion)
    {
        $event = new DiscussionEvent($discussion);
        $this->dispatcher->dispatch(Events::DISCUSSION_PRE_PERSIST, $event);

        $this->doSaveDiscussion($discussion);

        $event = new DiscussionEvent($discussion);
        $this->dispatcher->dispatch(Events::DISCUSSION_POST_PERSIST, $event);
    }

    /**
     * Performs the persistence of the Discussion.
     *
     * @abstract
     * @param DiscussionInterface $Discussion
     */
    abstract protected function doSaveDiscussion(DiscussionInterface $discussion);

    abstract protected function checkRepoBySlug($slug);
}
