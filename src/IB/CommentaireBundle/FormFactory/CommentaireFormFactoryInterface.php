<?php

namespace IB\CommentaireBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Comment form creator
*/
interface CommentaireFormFactoryInterface
{
    /**
     * Creates a comment form
     *
     * @return FormInterface
     */
    public function createForm();
}
