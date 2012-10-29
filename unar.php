<?php
// Unar for Symbiose WebOS
// Version 1.0
// Coded by TiBounise (http://tibounise.com)
// Released as GPL v3 software

if (!$this->arguments->isParam(0))
	throw new InvalidArgumentException('Aucun fichier fourni');

$FileManager = $this->webos->managers()->get('File');
$ArchiveLocation = $this->terminal->getAbsoluteLocation($this->arguments->getParam(0));
$phpArLocation = $this->terminal->getAbsoluteLocation('/usr/lib/unar/phpAr.php');

if (!$FileManager->exists($ArchiveLocation))
	throw new InvalidArgumentException('Le fichier n\'existe pas !');

// Including phpAr
if (!$FileManager->exists($phpArLocation))
	throw new InvalidArgumentException('phpAr n\'a pas été installé !');

include $FileManager->get($phpArLocation)->realpath();

// Getting paths for the archive and the destination
$Archive = $FileManager->get($ArchiveLocation);
$ArchiveRealpath = $Archive->realpath();
$ArchiveFilename = $Archive->basename();

// Stores the times at the beginning of the script
$startTime = time();

echo 'Symbiose-unar - Extracting '.$this->arguments->getParam(0);

// Extract !
try {
	$ArchiveHandler = new phpAr($ArchiveRealpath);
	$ArchiveFiles = $ArchiveHandler->listfiles();

	foreach ($ArchiveFiles as $Filename) {
		echo '<br />Extracting : '.$Filename;

		// Check if the file doesn't already exists
		if ($FileManager->exists($this->terminal->getAbsoluteLocation($Filename)))
			throw new Exception($Filename.' already exists.');
		
		$File = $ArchiveHandler->getfile($Filename);
		$FileManager->createFile($this->terminal->getAbsoluteLocation($Filename))->setContents($File->content);
	}

	echo '<br />'.$ArchiveFilename.' a été décompressé en '.(time() - $startTime).' secondes.';
} catch (Exception $e) {
	echo '<br /><strong>Une erreur s\'est produite : '.$e->getMessage().'</strong>';
}