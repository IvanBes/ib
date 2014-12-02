<?php

namespace IB\CommentaireBundle;

final class Events
{
    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Commentaire.
     *
     * This event allows you to modify the data in the Comment prior
     * to persisting occuring. The listener receives a
     * ib\CommentaireBundle\Event\CommentPersistEvent instance.
     *
     * Persisting of the comment can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const COMMENTAIRE_PRE_PERSIST = 'ib_commentaire.commentaire.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Commentaire.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Comment to be persisted before performing
     * those actions. The listener receives a
     * ib\CommentaireBundle\Event\CommentEvent instance.
     *
     * @var string
     */
    const COMMENTAIRE_POST_PERSIST = 'ib_commentaire.commentaire.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Commentaire.
     *
     * The listener receives a ib\CommentaireBundle\Event\CommentEvent
     * instance.
     *
     * @var string
     */
    const COMMENTAIRE_CREATE = 'ib_commentaire.commentaire.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the discussion.
     *
     * This event allows you to modify the data in the Thread prior
     * to persisting occuring. The listener receives a
     * ib\CommentaireBundle\Event\ThreadEvent instance.
     *
     * @var string
     */
    const DISCUSSION_PRE_PERSIST = 'ib_commentaire.discussion.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the discussion.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Thread to be persisted before performing
     * those actions. The listener receives a
     * ib\CommentaireBundle\Event\ThreadEvent instance.
     *
     * @var string
     */
    const DISCUSSION_POST_PERSIST = 'ib_commentaire.discussion.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a discussion.
     *
     * The listener receives a ib\CommentaireBundle\Event\ThreadEvent
     * instance.
     *
     * @var string
     */
    const DISCUSSION_CREATE = 'ib_commentaire.discussion.create';
}
