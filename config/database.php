<?php
class CSVDatabase {
    private $dataPath;
    
    public function __construct() {
        $this->dataPath = __DIR__ . '/../data/';
        $this->ensureDataDirectory();
    }
    
    private function ensureDataDirectory() {
        if (!file_exists($this->dataPath)) {
            mkdir($this->dataPath, 0777, true);
        }
    }
    
    public function read($filename) {
        $filepath = $this->dataPath . $filename;
        if (!file_exists($filepath)) {
            return [];
        }
        
        $data = [];
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            $headers = fgetcsv($handle, 1000, ",");
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
    
    public function write($filename, $data, $headers = null) {
        $filepath = $this->dataPath . $filename;
        
        if (($handle = fopen($filepath, "w")) !== FALSE) {
            if ($headers && !empty($data)) {
                fputcsv($handle, $headers);
            } elseif (!empty($data)) {
                fputcsv($handle, array_keys($data[0]));
            }
            
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            return true;
        }
        return false;
    }
    
    public function append($filename, $data, $headers = null) {
        $filepath = $this->dataPath . $filename;
        $fileExists = file_exists($filepath);
        
        if (($handle = fopen($filepath, "a")) !== FALSE) {
            if (!$fileExists && $headers) {
                fputcsv($handle, $headers);
            }
            fputcsv($handle, $data);
            fclose($handle);
            return true;
        }
        return false;
    }
    
    public function generateId($filename) {
        $data = $this->read($filename);
        if (empty($data)) {
            return 1;
        }
        
        $maxId = 0;
        foreach ($data as $row) {
            if (isset($row['id']) && $row['id'] > $maxId) {
                $maxId = $row['id'];
            }
        }
        return $maxId + 1;
    }
}
?>
