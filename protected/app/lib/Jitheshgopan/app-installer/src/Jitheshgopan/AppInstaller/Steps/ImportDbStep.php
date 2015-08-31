<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:37 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;


use Jitheshgopan\AppInstaller\Exceptions\InstallerException;
use Jitheshgopan\AppInstaller\Installer;
use \DB;

class ImportDbStep extends AbstractStep{
    public function process($prevStep) {
        $options = $this->getOptions();
        if(empty($options['dbFiles']) && empty($options['dbFile'])) {
            $this->errors[] = "Error importing Database. 'dbFiles' or 'dbFile' option should be passed";
        } else {
            $dbFiles = isset($options['dbFiles']) ? $options['dbFiles'] : array($options['dbFile']);
            try{
                foreach ($dbFiles as $dbFile) {
                    $this->importDbFile($dbFile);
                }

            } catch(InstallerException $e) {
                $this->errors[] = $e->getMessage();
            }
        }
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
    }

    public function handler() {
        return !$this->hasError();
    }

    public function importDbFile($filePath) {
        // Temporary variable, used to store current query
        $templine = '';
        if(!file_exists($filePath)) {
            throw new InstallerException("Error importing DB from file : \"" . $filePath . "\". File doesn't exist");
        }
        // Read in entire file
        $lines = file($filePath);
        // Loop through each line
        foreach ($lines as $line)
        {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';')
            {
                // Perform the query
                try{
                    DB::unprepared($templine);
                } catch (\PDOException $e) {
                    throw new InstallerException("Error running query : \"" . substr($templine, 0, 120) . '...' . "\". " . $e->getMessage());
                }
                // Reset temp variable to empty
                $templine = '';
            }
        }
        return true;
    }
}