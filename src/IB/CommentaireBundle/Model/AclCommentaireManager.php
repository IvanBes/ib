<?php

namespace IB\CommentaireBundle\Model;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use FOS\UserBundle\Model\UserInterface;
use IB\CommentaireBundle\Entity\Commentaire;

class AclCommentaireManager
{
	private $aclProvider;
	
	function __construct(AclProviderInterface $AclProvider)
	{
		$this->aclProvider = $AclProvider;
	}

	public function initAcl(Commentaire $commentaire, UserInterface $user)
	{
    	$objectIdentity = ObjectIdentity::fromDomainObject($commentaire);
		
		$acl_parent = null;
		if ($commentaire->getParent() !== null) {
			$objectIdentity_parent = ObjectIdentity::fromDomainObject($commentaire->getParent());
			$acl_parent = $this->aclProvider->findAcl($objectIdentity_parent);
		}

		$acl = $this->aclProvider->createAcl($objectIdentity);
    	$securityIdentity = UserSecurityIdentity::fromAccount($user);
    	$acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OPERATOR);
    	if ($acl_parent !== null) $acl->setParentAcl($acl_parent);
    	$this->aclProvider->updateAcl($acl);
    	return true;
	}

	public function loadAcls($commentaires)
	{
		$oids = array();
		foreach ($commentaires as $commentaire) 
		{
    		$oid = ObjectIdentity::fromDomainObject($commentaire);
    		$oids[] = $oid;
		}

		$this->aclProvider->findAcls($oids);
	}

	public function deleteAcl(Commentaire $commentaire)
	{
		$objectIdentity = ObjectIdentity::fromDomainObject($commentaire);
		$this->aclProvider->deleteAcl($objectIdentity);
		return true;
	}
}