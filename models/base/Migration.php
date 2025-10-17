<?php

namespace models\base;

use PDOException;

class Migration extends Database
{
    public function run_migration($filename)
    {
        $pdo = self::connect();

        echo "=> Start migration of « $filename »\r\n";

        $templine = "";
        $delimiter = ";";
        $lines = file($filename);
        
        foreach ($lines as $lineNum => $line) {
            // Ignorer les commentaires et lignes vides
            if (substr($line, 0, 2) == '--' || trim($line) == '')
                continue;
            
            // Gérer le changement de délimiteur
            if (preg_match('/^\s*DELIMITER\s+(\S+)\s*$/i', $line, $matches)) {
                $delimiter = $matches[1];
                continue;
            }
            
            $templine .= $line;
            
            // Vérifier si la ligne se termine par le délimiteur actuel
            if (substr(trim($line), -strlen($delimiter)) == $delimiter) {
                // Retirer le délimiteur personnalisé et ajouter un point-virgule
                if ($delimiter !== ';') {
                    $templine = rtrim($templine);
                    if (substr($templine, -strlen($delimiter)) == $delimiter) {
                        $templine = substr($templine, 0, -strlen($delimiter)) . ';';
                    }
                }
                
                $templine = trim($templine);
                if ($templine) {
                        $pdo->query($templine);
                }
                $templine = '';
            }
        }

        $pdo = null;

        echo "=> End migration of « $filename »\r\n";
    }
}
