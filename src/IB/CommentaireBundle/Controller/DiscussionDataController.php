<?php
namespace IB\CommentaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DiscussionDataController extends Controller
{
    public function ListAction(Request $request)
    {
        $data['commentaires'] = $this->container->get('ib_commentaire.manager.commentaire')->getListCommentaires($this->container->getParameter('ib_commentaire.limit_list'));
        return $this->render('IBCommentaireBundle:Commentaire:list.html.twig', $data);
    }
}