<?php

namespace IB\CommentaireBundle\Model;

/**
 * Interface to be implemented by commentaire Discussion managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to commentaire Discussions should happen through this interface.
 */
interface DiscussionManagerInterface
{
    /**
     * @param  string          $id
     * @return DiscussionInterface
     */
    public function findDiscussionById($id);

    /**
     * Finds one commentaire Discussion by the given criteria
     *
     * @param  array           $criteria
     * @return DiscussionInterface
     */
    public function findOneDiscussionBy(array $criteria);

    /**
     * Finds Discussions by the given criteria
     *
     * @param array $criteria
     *
     * @return array of DiscussionInterface
     */
    public function findDiscussionsBy(array $criteria);

    /**
     * Finds all Discussions.
     *
     * @return array of DiscussionInterface
     */
    public function findAllDiscussions();

    /**
     * Creates an empty commentaire Discussion instance
     *
     * @param  string   $permalink
     * @return Discussion
     */
    public function createDiscussion($slug);

    /**
     * Saves a Discussion
     *
     * @param DiscussionInterface $Discussion
     */
    public function saveDiscussion(DiscussionInterface $discussion);

    /**
     * Checks if the Discussion was already persisted before, or if it's a new one.
     *
     * @param DiscussionInterface $Discussion
     *
     * @return boolean True, if it's a new Discussion
     */
    public function isNewDiscussion(DiscussionInterface $discussion);

    /**
     * Returns the commentaire Discussion fully qualified class name
     *
     * @return string
     */
    public function getClass();
}
