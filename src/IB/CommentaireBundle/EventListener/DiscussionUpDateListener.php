<?php

namespace IB\CommentaireBundle\EventListener;

use IB\CommentaireBundle\Events;
use IB\CommentaireBundle\Event\CommentaireEvent;
use IB\CommentaireBundle\Model\CommentaireManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that updates discussion counters when a new commentaire is made.
 */
class DiscussionUpDateListener implements EventSubscriberInterface
{
    /**
     * @var CommentaireManagerInterface
     */
    private $commentaireManager;

    /**
     * Constructor.
     *
     * @param CommentaireManagerInterface $commentaireManager
     */
    public function __construct(CommentaireManagerInterface $commentaireManager)
    {
        $this->commentaireManager = $commentaireManager;
    }

    /**
     *
     * @param \IB\CommentaireBundle\Event\CommentaireEvent $event
     */
    public function onCommentairePersist(CommentaireEvent $event)
    {
        $commentaire = $event->getCommentaire();

        if (!$this->commentaireManager->isNewCommentaire($commentaire)) {
            return;
        }

        $discussion = $commentaire->getDiscussion();
        $discussion->setLastUpdate($commentaire->getDate());
    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENTAIRE_PRE_PERSIST => 'onCommentairePersist');
    }
}