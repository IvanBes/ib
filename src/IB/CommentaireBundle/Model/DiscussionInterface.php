<?php

namespace IB\CommentaireBundle\Model;

interface DiscussionInterface
{
    /**
     * Id, a unique string that binds the comments together in a thread (tree).
     * It can be a url or really anything unique.
     *
     * @return string
     */
    public function getId();

    /**
     * slug of the page where the thread lives
     * @return string
     */
    public function getSlug();

    /**
     * @param  string
     * @return null
     */
    public function setSlug($slug);    

    /**
     * @return string
     */
    public function getTitre();

    /**
     * @param  string
     * @return null
     */
    public function setTitre($titre);   

    /**
     * @return string
     */
    public function getClassname();

    /**
     * @param  string
     * @return null
     */
    public function setClassname($classname);

    /**
     * Tells if new comments can be added in this thread
     *
     * @return bool
     */
    public function isCommentable();

    /**
     * @param bool $isCommentable
     */
    public function setIsCommentable($isCommentable);

    /**
     * Denormalized date of the last comment
     * @return DateTime
     */
    public function getLastUpdate();

    /**
     * @param  DateTime
     * @return null
     */
    public function setLastUpdate($lastUpdate);
}