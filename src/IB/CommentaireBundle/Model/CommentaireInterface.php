<?php

namespace IB\CommentaireBundle\Model;

/**
 * CommentaireInterface.
 *
 * Any commentaire to be used by IB\CommentaireBundle must implement this interface.
 *
 */
interface CommentaireInterface
{
    /**
     * @return mixed unique ID for this commentaire
     */
    public function getId();

    /**
     * @return string name of the commentaire author
     */
    public function getAccount();

    /**
     * @return string
     */
    public function getCommentaire();

    /**
     * @param string $body
     */
    public function setCommentaire($body);

    /**
     * @return DateTime
     */
    public function getDate();

    /**
     * @return discussionInterface
     */
    public function getDiscussion();

    /**
     * @param discussionInterface $discussion
     */
    public function setDiscussion(DiscussionInterface $discussion);
}
