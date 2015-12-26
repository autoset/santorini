<?php
namespace org\autoset\santorini\io;

class FileSystem
{
	static function globr($sDir, $sPattern, $nFlags = NULL)
	{
		$aFiles = glob("$sDir/$sPattern", $nFlags);

		$files = self::getDir($sDir);
		
		if (is_array($files))
		{
			foreach( $files as $file )
			{
				$aSubFiles = self::globr($file, $sPattern, $nFlags);
				$aFiles = array_merge($aFiles,$aSubFiles);
			}
		}
	
		return $aFiles;
	}

	static function getDir($sDir)
	{
		$i=0;
		$aDirs = array();
		
		if(is_dir($sDir))
		{
			if($rContents = opendir($sDir))
			{
				while($sNode = readdir($rContents))
				{
					if(is_dir($sDir.'/'.$sNode ))
					{
						if($sNode !="." && $sNode !="..")
						{
							$aDirs[$i] = $sDir.'/'.$sNode;
							$i++;
						}
					}
				}
			}
		}
		
		return $aDirs;
	}


} 

?>