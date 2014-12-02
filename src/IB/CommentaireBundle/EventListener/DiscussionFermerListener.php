<?php

namespace IB\CommentaireBundle\EventListener;

use IB\CommentaireBundle\Events;
use IB\CommentaireBundle\Event\CommentaireEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Blames a commentaire using Symfony2 security component
 */
class DiscussionFermerListener implements EventSubscriberInterface
{
    /**
     * Disallows commentaires in a closed discussion.
     *
     * @param \IB\CommentaireBundle\Event\CommentaireEvent $event
     */
    public function onCommentairePersist(CommentaireEvent $event)
    {
        $discussion = $event->getCommentaire()->getDiscussion();

        if (!$discussion->isCommentable()) {
            $event->abortPersistence();
            throw new \LogicException('Impossible d\'ajouter un commentaire dans une discussion fermé.');
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENTAIRE_PRE_PERSIST => 'onCommentairePersist');
    }
}