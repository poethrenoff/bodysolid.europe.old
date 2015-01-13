<?php
namespace Adminko;

class Cart
{
    const SESSION_VAR = '__cart__';
    
    private $items = array();
    
    private static $instance = null;
    
    public static final function factory()
    {
        if (self::$instance == null) {
            self::$instance = new Cart();
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
    
    public function add($id, $price, $quantity = 1)
    {
        if (isset($this->items[$id])) {
            $this->items[$id]->quantity += $quantity;
        } else {
            $item = new \StdClass();
            
            $item->id = $id;
            $item->price = $price;
            $item->quantity = $quantity;
            
            $this->items[$id] = $item;
        }
    }
    
    public function in($id)
    {
        return isset($this->items[$id]);
    }
    
    public function delete($id)
    {
        unset($this->items[$id]);
    }
    
    public function clear()
    {
        $this->items = array();
    }
    
    public function getQuantity()
    {
        $cart_count = 0;
        foreach ($this->items as $item) {
            $cart_count += $item->quantity;
        }
        return $cart_count;
    }
    
    public function getSum()
    {
        $cart_sum = 0;
        foreach ($this->items as $item) {
            $cart_sum += $item->quantity * $item->price;
        }
        return $cart_sum;
    }
}
