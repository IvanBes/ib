<?php

namespace IB\CommentaireBundle\EventListener;

use IB\CommentaireBundle\Events;
use IB\CommentaireBundle\Event\CommentaireEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a commentaire using Symfony2 security component
 */
class CommentaireBlamerListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param LoggerInterface          $logger
     */
    public function __construct(SecurityContextInterface $securityContext = null, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->logger = $logger;
    }

    /**
     * Assigns the currently logged in user to a Commentaire.
     *
     * @param  \IB\CommentaireBundle\Event\CommentaireEvent $event
     * @return void
     */
    public function blame(CommentaireEvent $event)
    {
        $commentaire = $event->getCommentaire();

        if (null == $commentaire->getAccount()) 
        {
            if (null === $this->securityContext) 
            {
                if ($this->logger) {
                    $this->logger->debug("Le Commentaire Blamer n'a pas reçu le security.context.");
                }
    
                if (null === $this->securityContext->getToken()) {
                    if ($this->logger) {
                        $this->logger->debug("Probleme dans la configuration. (Commentaire Blamer)");
                    }
                }
            }
    
            if ($this->securityContext->isGranted('ROLE_USER')) {
                $this->securityContext->getToken()->getUser()->addCommentaire($commentaire);
                return;
            }

            $event->abortPersistence();
            throw new \LogicException('Impossible d\'ajouter un commentaire.');            
        }
        return;
    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENTAIRE_PRE_PERSIST => 'blame');
    }
}
