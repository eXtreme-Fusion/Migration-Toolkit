<?php

/**
 * 
 *
 * @author Piotr Rusol <piotr.rusol@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class NodePermissions {
    
    private function __construct() {
        ;
    }
    
    /**
     * 
     * @param string $file
     * @return string
     */
    public static function getRawPermissions($file){
        $perms = substr(decoct( fileperms($file) ), 2);
        return $perms;
    }

        /**
     * 
     * @param string $file
     * @return boolen
     */
    public static function isOwnerReadable($file){
        $perms = fileperms($file);
        return (($perms & 0x0100) ? TRUE : FALSE);
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isGroupReadable($file){
        $perms = fileperms($file);
        return (($perms & 0x0020) ? TRUE : FALSE);
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isWorldReadable($file){
        $perms = fileperms($file);
        return (($perms & 0x0004) ? TRUE : FALSE);
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isOwnerWritable($file){
        $perms = fileperms($file);
        return (($perms & 0x0080) ? TRUE : FALSE);
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isGroupWritable($file){
        $perms = fileperms($file);
        return (($perms & 0x0010) ? TRUE : FALSE);
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isWorldWritable($file){
        $perms = fileperms($file);
        return (($perms & 0x0002) ? TRUE : FALSE);
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isOwnerExecutable($file){
        $perms = fileperms($file);
        return (($perms & 0x0040) ?
               (($perms & 0x0800) ? TRUE : TRUE ) :
               (($perms & 0x0800) ? TRUE : FALSE));
    }
    
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isGroupExecutable($file){
        $perms = fileperms($file);
        return (($perms & 0x0008) ?
               (($perms & 0x0400) ? TRUE : TRUE ) :
               (($perms & 0x0400) ? TRUE : FALSE));
    }
    
    /**
     * 
     * @param string $file
     * @return boolean
     */
    public static function isWorldExecutable($file){
        $perms = fileperms($file);
        return (($perms & 0x0001) ? 
               (($perms & 0x0200) ? TRUE : TRUE ) : 
               (($perms & 0x0200) ? TRUE : FALSE));
    }
    
}

?>
