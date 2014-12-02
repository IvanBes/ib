<?php
namespace IB\CommentaireBundle\Event;

use IB\CommentaireBundle\Model\CommentaireInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a commentaire.
 */
class CommentaireEvent extends Event
{
    private $commentaire;

    /**
     * Constructs an event.
     *
     * @param \IB\CommentaireBundle\Model\CommentaireInterface $commentaire
     */
    public function __construct(CommentaireInterface $commentaire)
    {
        $this->commentaire = $commentaire;
    }

    /**
     * Returns the commentaire for this event.
     *
     * @return \IB\CommentaireBundle\Model\CommentaireInterface
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }
}
