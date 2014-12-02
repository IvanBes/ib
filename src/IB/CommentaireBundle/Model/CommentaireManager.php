<?php
namespace IB\CommentaireBundle\Model;

use IB\CommentaireBundle\Events;
use IB\CommentaireBundle\Event\CommentaireEvent;
use IB\CommentaireBundle\Event\CommentairePersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidArgumentException;

/**
 * Abstract Commentaire Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class CommentaireManager implements CommentaireManagerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns an empty commentaire instance
     *
     * @return Commentaire
     */
    public function createCommentaire(DiscussionInterface $discussion)
    {
        $class = $this->getClass();
        $commentaire = new $class;

        $commentaire->setDiscussion($discussion);

        $event = new CommentaireEvent($commentaire);
        $this->dispatcher->dispatch(Events::COMMENTAIRE_CREATE, $event);

        return $commentaire;
    }

    /**
     * Saves a commentaire to the persistence backend used. Each backend
     * must implement the abstract doSaveCommentaire method which will
     * perform the saving of the commentaire to the backend.
     *
     * @param  CommentaireInterface         $commentaire
     * @throws InvalidArgumentException when the commentaire does not have a Discussion.
     */
    public function saveCommentaire(CommentaireInterface $commentaire)
    {
        if (null === $commentaire->getDiscussion()) {
            throw new InvalidArgumentException('Le commentaire doit être dans une discussion.');
        }

        $event = new CommentairePersistEvent($commentaire);
        $this->dispatcher->dispatch(Events::COMMENTAIRE_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return;
        }


        $this->doSaveCommentaire($commentaire);

        $event = new CommentaireEvent($commentaire);
        $this->dispatcher->dispatch(Events::COMMENTAIRE_POST_PERSIST, $event);            
    }

    /**
     * Saves a commentaire to the persistence backend used. Each backend
     * must implement the abstract doSaveCommentaire method which will
     * perform the saving of the commentaire to the backend.
     *
     * @param  CommentaireInterface         $commentaire
     * @throws InvalidArgumentException when the commentaire does not have a Discussion.
     */
    public function updateCommentaire(CommentaireInterface $commentaire)
    {
        $discussion = $commentaire->getDiscussion();

        if (null === $commentaire->getDiscussion()) {
            throw new InvalidArgumentException('Le commentaire doit être dans une discussion.');
        }

        $this->doSaveCommentaire($commentaire);
    }

    /**
     * Saves a commentaire to the persistence backend used. Each backend
     * must implement the abstract doSaveCommentaire method which will
     * perform the saving of the commentaire to the backend.
     *
     * @param  CommentaireInterface         $commentaire
     * @throws InvalidArgumentException when the commentaire does not have a Discussion.
     */
    public function softDeletableCommentaire(CommentaireInterface $commentaire)
    {
        $discussion = $commentaire->getDiscussion();

        if (null === $commentaire->getDiscussion()) {
            throw new InvalidArgumentException('Le commentaire doit être dans une discussion.');
        }

        $this->removeCommentaire($commentaire);
    }

    /**
     * Performs the persistence of a commentaire.
     *
     * @abstract
     * @param CommentaireInterface $commentaire
     */
    abstract protected function doSaveCommentaire(CommentaireInterface $commentaire);

    /**
     * Performs the remove of a commentaire.
     *
     * @abstract
     * @param CommentaireInterface $commentaire
     */
    abstract protected function removeCommentaire(CommentaireInterface $commentaire);
}