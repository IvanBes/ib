<?php

namespace IB\CommentaireBundle\Event;

use IB\CommentaireBundle\Model\DiscussionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a discussion.
 */
class DiscussionEvent extends Event
{
    private $discussion;

    /**
     * Constructs an event.
     *
     * @param \IB\CommentaireBundle\Model\discussionInterface $discussion
     */
    public function __construct(DiscussionInterface $discussion)
    {
        $this->discussion = $discussion;
    }

    /**
     * Returns the discussion for this event.
     *
     * @return \IB\CommentaireBundle\Model\DiscussionInterface
     */
    public function getDiscussion()
    {
        return $this->discussion;
    }
}
