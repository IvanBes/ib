<?php
namespace IB\CommentaireBundle\Controller;

use IB\CommentaireBundle\Model\DiscussionInterface;
use IB\CommentaireBundle\Model\CommentaireInterface;
use IB\SchemaBundle\Model\JsonAjaxOnlyControllerInterface;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

 class DiscussionController extends Controller implements JsonAjaxOnlyControllerInterface
 {
    /**
     * Obtenir les Commentaires de la discussion avec le formulaire. Crée une nouvelle discussion s'il n'en existe pas.
     *
     * @param Request $request Current request
     * @return View
     */
    public function getDiscussionFormCommentairesAction(Request $request, $entity)
    {
        $discussionManager = $this->container->get('ib_commentaire.manager.discussion'); 
        $slug = urldecode($request->query->get('slug', null));
        if (null === $slug OR !$discussionManager->init($entity)) return $this->onErrorMessage('Impossible de generer la discussion.');

        $data['discussion'] = $discussionManager->findOneDiscussionBy(array('slug' => $slug, 'classname' => $discussionManager->getRepositoryOfRelation()));
        if (null === $data['discussion']) 
        {
            if ($discussion = $discussionManager->createDiscussion($slug)) 
            {    
                $validator = $this->get('validator');
                $errors = $validator->validate($discussion);
                if (count($errors) > 0) {
                    return $this->onErrorMessage('Impossible de creer la discussion.');                    
                }

                $discussionManager->saveDiscussion($discussion);
                $data['discussion'] = $discussion;
                $data['commentaires'] = null;                               
            } else {
                return $this->onErrorMessage('Données invalides.');
            }

        } else {
            $data['commentaires'] = $this->getPaginationCommentaires($request, $data['discussion']);
        }

        if (TRUE == $this->get('security.context')->isGranted('ROLE_USER') AND $data['discussion']->isCommentable())
        {
            $data['form'] = $this->getFormCommentaire()->createView();
        }

        $view = View::create()
            ->setData(array_merge($data, array('path' => urldecode($request->query->get('path', null)))))
            ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'discussion'));

        $handler = $this->getViewHandler();
        return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'))));
    }

    /**
     * Obtenir les Commentaires de la discussion
     * @param Request $request Current request
     * @param string $id        Id of the discussion
     * @return Response json
     */
    public function getDiscussionCommentairesAction(Request $request, $id)
    {   
        $data['discussion'] = $this->container->get('ib_commentaire.manager.discussion')->findDiscussionById($id);
        if (!(null === $data['discussion']))
        {
            $data['commentaires'] = $this->getPaginationCommentaires($request, $data['discussion']);

            if(count($data['commentaires']) > 0)
            {   
                $view = View::create()
                    ->setData($data)
                    ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'commentaires'));

                $handler = $this->getViewHandler();
                return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'), 'lastdate' => $data['discussion']->getLastUpdate()->format('Y-m-d H:i:s'), 'reload' => true)));
            } 

            return $this->onErrorMessage("La page existe pas ou plus.");
        }

        return $this->onErrorMessage('Discussion introuvable.');      
    }

    /**
     * Obtenir les derniers tirages d'un commentaire.
     *
     * @param string $id id du commentaire
     * @param number  $lastFictifDate
     *
     * @return View
     */
    public function getDiscussionDraftCommentaireChildrensAction(Request $request, $id, $id_commentaire_parent, $lastFictifDate)
    {
        $discussion = $this->container->get('ib_commentaire.manager.discussion')->findDiscussionById($id);
        $commentaire = $this->container->get('ib_commentaire.manager.commentaire')->findDraftChildrensByCommentaireAndDiscussion($discussion, $id_commentaire_parent, $lastFictifDate);

        if (null === $discussion || null === $commentaire) throw new NotFoundHttpException('Une erreur est survenue.');

        $view = View::create()
            ->setData(array('commentaire' => $commentaire))
            ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'commentaire_childrens'));

        $handler = $this->getViewHandler();
        return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'), 'id_commentaire_parent' => $id_commentaire_parent, 'lastdate' => $discussion->getLastUpdate()->format('Y-m-d H:i:s'), 'count_result' => count($commentaire->getChildren()))));        
    }    

    /**
     * Obtenir les derniers tirages d'une discussion.
     *
     * @param string $id        Id of the discussion
     * @param number  $lastFictifDate
     *
     * @return View
     */
    public function getDiscussionDraftCommentairesAction(Request $request, $id, $lastFictifDate)
    {
        $discussion = $this->container->get('ib_commentaire.manager.discussion')->findDiscussionById($id);
        $commentaires = $this->container->get('ib_commentaire.manager.commentaire')->findDraftCommentairesByDiscussion($discussion, $lastFictifDate);

        if (null === $discussion || null === $commentaires) throw new NotFoundHttpException('Une erreur est survenue.');

        $view = View::create()
            ->setData(array('commentaires' => $commentaires, 'discussion' => $discussion, 'specific_focus' => true))
            ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'commentaires'));

        $handler = $this->getViewHandler();
        return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'), 'lastdate' => $discussion->getLastUpdate()->format('Y-m-d H:i:s'), 'count_result' => count($commentaires), 'reload' => false)));        
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function getEditFormCommentaireAction(Request $request, $id)
    {
        try {
            $commentaire = $this->container->get('ib_commentaire.manager.commentaire')->findCommentaireById($id);          
        } catch (\Doctrine\ORM\NoResultException $e) {
            return $this->onErrorMessage('Le commentaire n\'existe pas ou plus.');      
        }

        if (!$commentaire->getDiscussion()->isCommentable()) return $this->onErrorMessage('Une erreur est survenue.'); 

        $data['form'] = $this->getFormCommentaire($commentaire)->createView();
        $view = View::create()
                ->setData($data)
                ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'formulaire_edit_commentaire'));

        $handler = $this->getViewHandler();
        return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'), 'id' => $id)));
    }

    /**
     * Crée un nouveau Commentaire pour la discussion à partir des données présentées.
     * @Security("has_role('ROLE_USER')")
     * @param Request $request The current request
     * @param string  $id      The id of the discussion
     *
     * @return View
     */
    public function postDiscussionCommentaireAction(Request $request)
    {
        $id = $request->request->get('id_discussion', null);

        if (null !== $id AND 'POST' === $request->getMethod()) 
        {
            $discussion = $this->container->get('ib_commentaire.manager.discussion')->findDiscussionById($id);
            
            if (!$discussion OR !$discussion->isCommentable()) return $this->onErrorMessage('Une erreur est survenue.'); 

            $commentaireManager = $this->container->get('ib_commentaire.manager.commentaire');
            $commentaire = $commentaireManager->createCommentaire($discussion);

            $form = $this->getFormCommentaire($commentaire)->bind($request);
    
            if ($form->isValid()) 
            {
                $id_commentaire_parent = $request->request->get('id_commentaire_parent', null);

                if (null !== $id_commentaire_parent) 
                {
                    $commentaire_parent = $this->container->get('ib_commentaire.manager.commentaire')->findCommentaireByDiscussionAndId($discussion, $id_commentaire_parent);

                    if ($commentaire_parent->getLvl() < 1) 
                    {
                        $commentaire->setParent($commentaire_parent);
                    }
                    else 
                    {
                        return $this->onErrorMessage('Action impossible.');    
                    }                
                }

                $commentaireManager->saveCommentaire($commentaire);  
                $count_commentaire_before = $this->container->get('ib_commentaire.manager.commentaire')->countCommentairesByDiscussion($id)-1;

                if (null !== $id_commentaire_parent) {
                    $methode = $this->onCreateCommentaireChildrenSuccess($id, $id_commentaire_parent, $request->request->get('lastdate', null));
                }
                else if (($request->request->get('page') > 1) or ($count_commentaire_before % 6 == 0)) 
                { 
                    $methode = $this->onReloadCreateCommentaireSuccess($id);
                } else {
                    $methode = $this->onCreateCommentaireSuccess($id, $request->request->get('lastdate', null)); 
                }

                return $this->getViewHandler()->handle($methode);
            }
    
            return $this->onCreateCommentaireError($form);
        }

        return $this->onErrorMessage('Vous ne pouvez pas generer cette page.');           
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function putDiscussionCommentaireAction(Request $request)
    {
        if ('PUT' === $request->getMethod())
        {  
            $id = intval($request->request->get('id_commentaire', null));

            $manager = $this->container->get('ib_commentaire.manager.commentaire');
            $commentaire = $manager->findCommentaireById($id);

            if (!$commentaire->getDiscussion()->isCommentable()) return $this->onErrorMessage('Une erreur est survenue.');
            if ($this->getUser() !== $commentaire->getAccount()) return $this->onErrorMessage('Vous n\'avez pas le droit d\'éditer ce commentaire.');

            $form = $this->getFormCommentaire($commentaire)->bind($request);

            if ($form->isValid()) 
            {
                $manager->updateCommentaire($commentaire);

                $view = View::create()
                ->setData(array('chaine' => $commentaire->getCommentaire()))
                ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Commentaire', 'string_commentaire'));
                $handler = $this->getViewHandler();
                return $handler->handle($view->setData(array('edition' => $handler->renderTemplate($view, 'html'), 'success' => true)));
            }

            return $this->onEditCommentaireError($form);
        }

        return $this->onErrorMessage('Vous ne pouvez pas generer cette page.');  
    }

    /**
     * @Security("has_role('ROLE_USER')")
    */
    public function deleteCommentaireAction(Request $request)
    {
        if ('DELETE' === $request->getMethod())
        {  
            $token = $request->request->get('_token');
            $id = intval($request->request->get('id', null));

            if (0 == $id OR !$this->get('form.csrf_provider')->isCsrfTokenValid('delete_IBCommentaireBundle_OFsGa0Wtnl', $token)) 
            {
                return $this->onErrorMessage('Requête invalide.');
            }

            $manager = $this->container->get('ib_commentaire.manager.commentaire');

            try {
                $commentaire = $manager->findCommentaireById($id);
            } catch (\Doctrine\ORM\NoResultException $e) {
                return $this->onErrorMessage("Commentaire introuvable.");
            }

            if (!$commentaire->getDiscussion()->isCommentable()) return $this->onErrorMessage('Une erreur est survenue.');               
            if ($this->getUser() !== $commentaire->getAccount()) return $this->onErrorMessage('Vous n\'avez pas le droit de supprimer ce commentaire.');

            $manager->softDeletableCommentaire($commentaire);
       
            $view = View::create()->setData(array('message' => 'Commentaire supprimé.', 'success' => true, 'id' => $id));
            return $this->getViewHandler()->handle($view);
        }

        return $this->onErrorMessage('Vous ne pouvez pas generer cette page.');   
    }

    /**
     * Obtenir le formulaire à partir des données présentées.
     *
     * @param Request $request The current request
     * @param string  $id      The id of the commmentaire
     *
     * @return View
     */
    private function getFormCommentaire(CommentaireInterface $commmentaire = null)
    {
        return $this->container->get('ib_commentaire.form_factory.commentaire')
                               ->createForm()
                               ->setData($commmentaire);
    }

    /**
     * @return \Knp\PaginatorBundle\SlidingPagination
     */
    private function getPaginationCommentaires(Request $request, DiscussionInterface $discussion)
    {
        try {
            $commentairesQuery = $this->container->get('ib_commentaire.manager.commentaire')->findCommentairesByDiscussion($discussion);
            $pagination = $this->get('knp_paginator')->paginate($commentairesQuery, intval($request->query->get('page', 1)), 6);
            if (count($pagination) == 0 && intval($request->query->get('page', 1)) !== 1) {
                $pagination = $this->get('knp_paginator')->paginate($commentairesQuery, 1, 6);       
            }
            $pagination->setTemplate('KnpPaginatorBundle:Pagination:sliding_ajax.html.twig');
            return $pagination;            
        } catch (Exception $e) {
            throw new \Exception("Error Processing Request", 1);
        }
    }

    /**
     * Transmet l'action à la vue de commentaire sur une soumission de formulaire succès et sans reload.
     *
     * @param string           $id     Id of the Discussion
     *
     * @return View
     */
    private function onCreateCommentaireSuccess($id, $lastFictifDate)
    {
        return RouteRedirectView::create('api_ib_commentaire_get_discussion_draft_commentaires', array('id' => $id, 'lastFictifDate' => $lastFictifDate, '_format' => 'json'), Codes::HTTP_FOUND);
    }

    /**
     * Transmet l'action à la vue de commentaire sur une soumission de formulaire succès et sans reload.
     *
     * @param string           $id     Id of the Discussion
     *
     * @return View
     */
    private function onCreateCommentaireChildrenSuccess($id, $id_commentaire_parent, $lastFictifDate)
    {
        return RouteRedirectView::create('api_ib_commentaire_get_discussion_draft_commentaire_childrens', array('id' => $id, 'id_commentaire_parent' => $id_commentaire_parent, 'lastFictifDate' => $lastFictifDate, '_format' => 'json'), Codes::HTTP_FOUND);
    }

    /**
     * Transmet l'action à la vue de commentaire sur une soumission de formulaire succès et avec reload.
     *
     * @param string           $id     Id of the Discussion
     *
     * @return View
     */
    protected function onReloadCreateCommentaireSuccess($id)
    {
        return RouteRedirectView::create('api_ib_commentaire_get_discussion_commentaires', array('id' => $id, '_format' => 'json'), Codes::HTTP_FOUND);
    }

    /**
     * Retourne une réponse HTTP_BAD_REQUEST lorsque la soumission du formulaire échoue.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the Discussion
     *
     * @return View
     */
    private function onCreateCommentaireError(FormInterface $form)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array('form' => $form))
            ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'formulaire_commentaire'));

        $handler = $this->getViewHandler();
        return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'))));
    }

    /**
     * Retourne une réponse HTTP_BAD_REQUEST lorsque la soumission du formulaire échoue.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the Discussion
     *
     * @return View
     */
    private function onEditCommentaireError(FormInterface $form)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array('form' => $form))
            ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'formulaire_edit_commentaire'));

        $handler = $this->getViewHandler();
        return $handler->handle($view->setData(array('html' => $handler->renderTemplate($view, 'html'))));
    }

    private function onErrorMessage($message)
    {
        $view = View::create()
                    ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                    ->setData(array('message' => $message, 'success' => false))
                    ->setTemplate(new TemplateReference('IBCommentaireBundle', 'Discussion', 'errors'));

        return $this->getViewHandler()->handle($view);    
    }

    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }
}