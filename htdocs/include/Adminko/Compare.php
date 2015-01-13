<?php
namespace Adminko;

class Compare
{
    const SESSION_VAR = '__compare__';
    
    private $items = array();
    
    private static $instance = null;
    
    public static final function factory()
    {
        if (self::$instance == null) {
            self::$instance = new Compare();
       }
        return self::$instance;
    }
    
    private function __construct()
    {
        if (!isset($_SESSION[self::SESSION_VAR]) || !is_array($_SESSION[self::SESSION_VAR])) {
            $_SESSION[self::SESSION_VAR] = array();
        }
        $this->items = $_SESSION[self::SESSION_VAR];
    }
    
    public function __destruct()
    {
        $_SESSION[self::SESSION_VAR] = $this->items;
    }
    
    public function get()
    {
        return $this->items;
    }
    
    
    public function add($id)
    {
        $this->items[$id] = $id;
    }
    
    public function in($id)
    {
        return isset($this->items[$id]);
    }
    
    public function delete($id)
    {
        unset($this->items[$id]);
        
        if (isset($this->items) && count($this->items) == 0) {
            $this->clear();
        }
    }
    
    public function clear()
    {
        $this->items = array();
    }
    
    public function count()
    {
        return isset($this->items) ? count($this->items) : 0;
    }
}
