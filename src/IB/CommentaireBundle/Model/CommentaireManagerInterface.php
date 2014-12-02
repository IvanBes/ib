<?php

namespace IB\CommentaireBundle\Model;

/**
 * Interface to be implemented by commentaire managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to commentaires should happen through this interface.
 */
interface CommentaireManagerInterface
{
    /**
     * Returns a flat array of commentaires with the specified Discussion.
     *
     * The sorter parameter should be left alone if you are sorting in the
     * tree methods.
     *
     * @param  DiscussionInterface $Discussion
     * @param  integer         $depth
     * @return array           of CommentaireInterface
     */
    public function findCommentairesByDiscussion(DiscussionInterface $discussion);

    /**
     * Saves a commentaire.
     *
     * @param CommentaireInterface $commentaire
     */
    public function saveCommentaire(CommentaireInterface $commentaire);

    /**
     * Find one commentaire by its ID and Discussion.
     *
     * @return Commentaire or null
     */
    public function findCommentaireByDiscussionAndId(DiscussionInterface $discussion, $id);

    /**
     * Find one commentaire by its ID.
     *
     * @return Commentaire or null
     */
    public function findCommentaireById($id);

    /**
     * Creates an empty commentaire instance.
     *
     * @return Commentaire
     */
    public function createCommentaire(DiscussionInterface $discussion);

    /**
     * Checks if the commentaire was already persisted before, or if it's a new one.
     *
     * @param CommentaireInterface $commentaire
     *
     * @return boolean True, if it's a new commentaire
     */
    public function isNewCommentaire(CommentaireInterface $commentaire);

    /**
     * Returns the commentaire fully qualified class name.
     *
     * @return string
     */
    public function getClass();
}
