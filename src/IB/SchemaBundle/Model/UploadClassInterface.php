<?php
namespace IB\SchemaBundle\Model;

interface UploadClassInterface
{
	public function getAbsolutePath();

	public function getWebPath();

	public function getUploadRootDir();

	public function getUploadRacineDir();

	public function getUploadDir();
	
	public function setPath($path);

	public function getPath();

	public function setFilename($filename);

	public function getFilename();	
}