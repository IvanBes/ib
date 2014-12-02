<?php

namespace IB\LikeBundle\Model;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\EntityManager;
use IB\LikeBundle\Entity\IPAddress;
use IB\LikeBundle\Model\IBLikeVoterInterface;

/**
* Voter manager
*/
class VoterManager
{
	private $em;
	private $configuration;

  private $className;
  private $id;
  private $ip;

	function __construct(EntityManager $entityManager, array $treeConfigurationBundles = null)
	{
		$this->em = $entityManager;
		$this->configuration = $treeConfigurationBundles;
	}

  public function init($driver, $id, $ip, $predefined = false)
  {
    if (!$predefined)
    {
      $this->className = $this->findClassNameByEntity($driver);
    }
    else
    {
      $this->className = $driver;
    }

    $this->id = intval($id);
    $this->ip = $ip;
    return $this;
  }

	public function putVote(IBLikeVoterInterface $object)
	{
		$query = $this->em->getRepository('IBLikeBundle:IPAddress')->findOneByIp($this->ip);

        if ($query !== null) {
          $IPAddress = $query;
        } else {
          $IPAddress = new IPAddress;
          $IPAddress->setIp($this->ip);
        }

		$object->addVoteIpAdres($IPAddress);
		$this->em->persist($object);
		$this->em->flush();
	}

  public function count()
  {
    return $this->em->getRepository($this->className)
    			      ->createQueryBuilder('o')
                ->select('count(o)')
                ->join('o.voteIpAdress', 'i')
                ->where('o.id = :objectId AND i.ip = :ipObject')
                ->setParameters(array('objectId' => $this->id, 'ipObject' => $this->ip))
                ->getQuery()
                ->getSingleScalarResult();
  }

  public function findEntityByNameAndId()
  {
  	$qb = $this->em->getRepository($this->className)
              ->createQueryBuilder('o')
              ->where('o.id = :objectId')
              ->setParameter('objectId', $this->id);    
    $object = $qb->getQuery()->getSingleResult();    
    return $object;
  }

  public function getClassName()
  {
    return $this->className;
  }

	private function findClassNameByEntity($entity)
	{		
    $entity = ucfirst($entity);

		foreach ($this->configuration as $classname) 
    {
      if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) 
      {
        if ($matches[1] === $entity) {
          return $classname;
        }
      }
    }

    unset($classname);
    throw new NotFoundHttpException("Error Processing Request");   
	}
}