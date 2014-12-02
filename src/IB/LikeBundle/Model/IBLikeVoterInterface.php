<?php

namespace IB\LikeBundle\Model;

interface IBLikeVoterInterface {

	public function addVoteIpAdres(\IB\LikeBundle\Entity\IPAddress $voteIpAdress);

    public function removeVoteIpAdres(\IB\LikeBundle\Entity\IPAddress $voteIpAdress);

    public function getVoteIpAdress();

}