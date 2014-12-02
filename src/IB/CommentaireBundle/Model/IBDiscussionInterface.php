<?php

namespace IB\CommentaireBundle\Model;

interface IBDiscussionInterface {

	public function setSlug($slug);

	public function getSlug();

	public function setTitre($titre);

	public function getTitre();
}