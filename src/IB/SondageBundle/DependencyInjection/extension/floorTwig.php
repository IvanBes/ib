<?php
namespace IB\SondageBundle\DependencyInjection\extension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class floorTwig extends \Twig_Extension
{    
    public function getName()
    {
        return 'IBmyTwigSondage';
    }
	
    public function getFunctions()
    {
        return array(
            'myFloor' => new \Twig_Function_Method($this, 'tooFloor'),
            'myCeil' => new \Twig_Function_Method($this, 'tooCeil'),
            'ibRand' => new \Twig_Function_Method($this, 'ibRand')
        );
    }

    public function getFilters()
    {
        return array(
            'cropFilter' => new \Twig_Filter_Method($this, 'cropFilter'),
            'smiley' => new \Twig_Filter_Method($this, 'smiley'),
            'classname' => new \Twig_Filter_Method($this, 'classname'),
            'rating_star' => new \Twig_Filter_Method($this, 'rating_star')
        );
    }
	
    public function tooFloor($number)
    {
        $number = (int) $number;
        return floor($number);
    }

    public function tooCeil($number)
    {
        return ceil($number);
    }

    public function cropFilter($chaine, $tailleMax)
    {
        if(strlen($chaine) >= $tailleMax)
        {
          $chaine = substr($chaine,0,$tailleMax);
          $positionDernierEspace = strrpos($chaine,' ');
          $chaine = substr($chaine,0,$positionDernierEspace).'...';
        }
        return $chaine;
    }

    public function classname($classname)
    {
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) 
        {
            return $matches[1];
        }
        return false;
    }

    public function ibRand($start, $end)
    {
        return rand($start, $end);
    }

    public function rating_star($decimal)
    {
        $int = round($decimal * 2)/2;

        if (($int - floor($int)) == 1/2)
        {
            return 'star star-'.floor($int).' star-half';
        } else {
            return 'star star-'.$int;
        }
    }

    public function smiley($chaine)
    {
        $message = $chaine;
        $patterns = array(':)', ':D', '8)', ':cool:', ':o', ':(', '<3');
        $replacements = array('<img src="/img/smiley/sourire.png"/>', '<img src="/img/smiley/content.png"/>', '<img src="/img/smiley/confus.png"/>', '<img src="/img/smiley/cool.png"/>', '<img src="/img/smiley/ooo.png"/>', '<img src="/img/smiley/mignion.png"/>', '<img src="/img/smiley/coeur.png"/>');
        $message = str_replace($patterns, $replacements, $message);

        return $message;
    }
}