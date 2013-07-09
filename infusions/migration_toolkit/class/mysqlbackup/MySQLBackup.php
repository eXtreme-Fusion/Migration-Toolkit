<?php
require_once './exceptions/DBException.php';
require_once './exceptions/BackupFolderException.php';
require_once './exceptions/BackupFileException.php';
require_once './exceptions/FunctionNotExistsException.php';

/**
 * This simple class gives ability to perform <br>
 * MySQL database backup nad restore operations.
 * 
 * Please be advised that class is based on <br>
 * PHP MySQL Extension which is deprecated <br>
 * and it should be trited as so.
 * 
 * Future updates and refactoring 
 *
 * @author Piotr Rusol <piotr.rusol@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class MySQLBackup {
    
    private $_server = NULL;
    
    private $_db = NULL;
    
    private $_user = NULL;
    
    private $_pass = NULL;
    
    private $_conn = NULL;
    
    /**
     * 
     * @param string $server
     * @param string $database
     * @param string $user
     * @param string $pass
     */
    public function __construct($server = 'localhost:3306', $database = '', $user = '', $pass = '') {
        
        $this->_server = $server;
        $this->_db = $database;
        $this->_user = $user;
        $this->_pass = $pass;
        
    }
    
    public function __destruct() {
        $this->close();
    }
    
    public function setServer($server = ''){
        $this->_server = $server;
    }

    public function setDataBase($database = ''){
        $this->_db = $database;
    }
    
    public function setUser($user = ''){
        $this->_user = $user;
    }
    
    public function setPassword($password = ''){
        $this->_pass = $password;
    }

        
    /**
     * Opens separate database connection.
     * 
     * If something goes wrong throws DBException <br>
     * with message as mysql_error() <br>
     * and code as mysql_errno() <br>
     * 
     * @throws DBException
     */
    public function open(){
        
        $this->_conn = mysql_connect($this->_server, $this->_user, $this->_pass, true);
        if(!$this->_conn){
            throw new DBException(mysql_error(), mysql_errno());
        }
        
    }
    
    /**
     * Closes database connection.
     */
    public function close(){
        
        if($this->_conn){
            mysql_close($this->_conn);
        }
                
    }
    
    /**
     * 
     * @throws DBException
     */
    public function reopen(){
        
        $this->close();
        $this->open();
        
    }

    /**
     * Performs full database backup in separate files <br>
     * under well defined folder.
     * 
     * Backup Folder - mysqlbackup_[YmdHis]_[dbname]<br>
     * Schema SQL file - [dbname].schema<br>
     * Data CSV file - [table].csv<br>
     * 
     * @param string $dstFolder <br>
     * Fullpath to root folder <br>
     * without ending forward slash <br>
     * where Backup Folder will be created. 
     * 
     * Please be advised that you have to have <br>
     * atleast write permision to root folder <br>
     * like also user under MySQL works. <br>
     * Default /tmp
     * 
     * @param string $delimiter <br>
     * SQL delimiter for Schema file
     * 
     * Default $$ (two dolar signs)
     * 
     * @return string <br>
     * Backup folder name
     * 
     * @throws DBException
     * @throws BackupFolderException
     * @throws BackupFileException
     * @throws FunctionNotExistsException
     */
    public function backup($dstFolder = '/tmp', $delimiter = '$$') {
        
        if(!mysql_select_db($this->_db, $this->_conn)){
            throw new DBException(mysql_error($this->_conn), mysql_errno($this->_conn));
        }
        
        $currentDateTime = date("YmdHis");
        $backupFolder = $dstFolder . '/mysqlbackup_' . $currentDateTime . '_' . $this->_db;
        $backupSchemaFile = $backupFolder . '/' . $this->_db . '.schema';
        
        $this->mkBackupFolder($backupFolder);
        
        $out = fopen($backupSchemaFile, "x");
        if(!$out){
            throw new BackupFileException();
        }
        
        fwrite($out, $this->dumpDBSchema($delimiter));
        
        fclose($out);
        
        $result = mysql_list_tables($this->_db, $this->_conn);
        
        while (($row = mysql_fetch_row($result))){
            
            $tableName = $row[0];
            $this->dumpTableDataCSV($tableName, "$backupFolder/$tableName.csv");
            
        }
        
        mysql_free_result($result);
        
        return $backupFolder;
        
    }
    
    /**
     * 
     * @param string $tableName
     * @return string
     * @throws DBException
     */
    private function dumpTableSchema($tableName){
        
        $query = 'SHOW CREATE TABLE `' . $tableName . '`';
        $result = mysql_query($query, $this->_conn);
        if(!$result){
            throw new DBException(mysql_error($this->_conn), mysql_errno($this->_conn));
        }
        
        $statement = mysql_fetch_array($result, MYSQL_NUM);
        if(!$statement){
            throw new DBException(mysql_error($this->_conn), mysql_errno($this->_conn));
        }
        
        return $statement[1];
        
    }
    
    /**
     * 
     * @param string $delimiter
     * @return string
     * @throws DBException
     */
    private function dumpDBSchema($delimiter = '$$'){
        
        $query = "SHOW CREATE DATABASE `$this->_db`";
        
        $result = mysql_query($query, $this->_conn);
        if(!$result){
            throw new DBException(mysql_error($this->_conn), mysql_errno($this->_conn));
        }
        
        $statement = mysql_fetch_array($result, MYSQL_NUM);
        if(!$statement){
            throw new DBException(mysql_error($this->_conn), mysql_errno($this->_conn));
        }
        
        mysql_free_result($result);
        
        $ret = "DELIMITER $delimiter" . PHP_EOL;
        $ret .= "DROP DATABASE IF EXISTS $this->_db $delimiter" . PHP_EOL;
        $ret .= $statement[1] . $delimiter . PHP_EOL;
        $ret .= "USE $this->_db $delimiter" . PHP_EOL;
        
        $result = mysql_list_tables($this->_db, $this->_conn);
        
        while (($row = mysql_fetch_row($result))){
            
            $tableName = $row[0];
            $ret .= $this->dumpTableSchema($tableName);
            $ret .= $delimiter . PHP_EOL;
            
        }
        
        mysql_free_result($result);
        
        $ret .= 'DELIMITER ;' . PHP_EOL;
        
        return $ret;
        
    }
    
    /**
     * 
     * @param string $tableName
     * @param string $dstFile
     */
    private function dumpTableDataCSV($tableName, $dstFile){
        
        $query = "SELECT * FROM `$tableName` INTO OUTFILE '$dstFile' 
                  FIELDS TERMINATED BY ',' 
                  ENCLOSED BY '\"' 
                  LINES TERMINATED BY '\n'";
        
        mysql_query($query, $this->_conn);
        
    }
    
    /**
     * 
     * @param string $backupFolder
     * @param string $delimiter
     */
    public function restore($backupFolder, $delimiter = '$$'){
        
        $schemaFile = "$backupFolder/$this->_db.schema";
        $content = file_get_contents($schemaFile);
        $queries = explode($delimiter, $content);
        
        unset($queries[0]);
        unset($queries[count($queries)]);
        
        var_dump($queries);
        
        mysql_query('SET @@foreign_key_checks = 0');
        
        foreach ($queries as $query) {
            
            mysql_query($query, $this->_conn);
            
        }
        
        $dataFiles = glob("$backupFolder/*.csv");
        foreach ($dataFiles as $dataFile) {
            $tableName = str_replace(array("$backupFolder/",".csv"), '', $dataFile);
            var_dump($tableName);
            $this->restoreTableDataCSV($dataFile, $tableName);
        }
        
        mysql_query('SET @@foreign_key_checks = 1');
        
    }
    
    /**
     * 
     * @param string $tableDataFile
     * @param string $tableName
     */
    private function restoreTableDataCSV($tableDataFile, $tableName){
        
        $query = "LOAD DATA INFILE '$tableDataFile'
                  INTO TABLE $tableName
                  COLUMNS TERMINATED BY ','
                  ENCLOSED BY '\"'
                  LINES TERMINATED BY '\n'";
        
        mysql_query($query, $this->_conn);
        
    }
    
    private function mkBackupFolder($backupFolder){
        
        if(!function_exists('mkdir')){
            throw new FunctionNotExistsException('mkdir');
        }
        
        if(!function_exists('chmod')){
            throw new FunctionNotExistsException('chmod');
        }
        
        if(!mkdir($backupFolder, 0777)){
            throw new BackupFolderException();
        }
        
        clearstatcache();
        
        if(!chmod($backupFolder, 0777)){
            rmdir($backupFolder);
            throw new BackupFolderException();
        }
        
        clearstatcache();
        
        if(!NodePermissions::isWorldReadable($backupFolder)){
            clearstatcache();
            rmdir($backupFolder);
            throw new BackupFolderException();
        }
        
        if(!NodePermissions::isWorldWritable($backupFolder)){
            clearstatcache();
            rmdir($backupFolder);
            throw new BackupFolderException();
        }
        
    }
    
}

?>
