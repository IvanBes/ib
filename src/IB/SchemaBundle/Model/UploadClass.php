<?php
namespace IB\SchemaBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class UploadClass implements UploadClassInterface
{   
    public function getAbsolutePath()
    {
        return null === $this->getFilename() ? null : $this->getUploadRootDir().'/'.$this->getFilename().'.'.$this->getExtension();
    }

    public function getWebPath()
    {
        return null === $this->getFilename() ? null : $this->getUploadDir().'/'.$this->getFilename().'.'.$this->getExtension();
    }

    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getUploadRacineDir()
    {
        return '../web/'.$this->getUploadDir();
    }
}