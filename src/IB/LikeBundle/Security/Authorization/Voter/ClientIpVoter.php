<?php

namespace IB\LikeBundle\Security\Authorization\Voter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ClientIpVoter implements VoterInterface
{
    public function supportsAttribute($attribute)
    {
        return 'OWNER' === $attribute;
    }

    public function supportsClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        return $reflectionClass->implementsInterface('IB\LikeBundle\Model\IBLikeVoterInterface');
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) 
        {
            if ($this->supportsAttribute($attribute)) 
            {
                if ($this->supportsClass($object)) 
                {
                   return VoterInterface::ACCESS_GRANTED;
                }          
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}