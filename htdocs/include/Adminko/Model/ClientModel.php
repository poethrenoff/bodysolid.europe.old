<?php
namespace Adminko\Model;

use Adminko\Db\Db;

class ClientModel extends Model
{
    // Возвращает объект пользователя по email
    public function getByEmail($client_email, $throw_exception = false)
    {
        $record = Db::selectRow('select * from client where client_email = :client_email',
            array('client_email' => $client_email));
        if (empty($record)) {
            if ($throw_exception) {
                throw new \AlarmException("Ошибка. Запись {$this->object}({$client_email}) не найдена.");
            } else {
                return false;
            }
        }
        return $this->get($record['client_id'], $record);
    }
    
    // Возвращает список заказов
    public function getPurchaseCount()
    {
        return model::factory('purchase')->getCount(
            array('purchase_client' => $this->getId())
        );
    }
    
    // Возвращает список заказов
    public function getPurchaseList($limit = null, $offset = null)
    {
        return Model::factory('purchase')->getList(
            array('purchase_client' => $this->getId()), array('purchase_date' => 'desc'), $limit, $offset
        );
    }
    
    // Возвращает товары пользователя
    public function getClientProductList()
    {
        return Model::factory('product')->getByClient($this);
    }
    
    // Есть ли товар в избранном
    public function isLike($product)
    {
        return Db::selectCell('select true from client_product where client_id = :client_id and product_id = :product_id',
            array('client_id' => $this->getId(), 'product_id' => $product->getId()));
    }
    
    // Добавить или удалить товар из Избранного
    public function toggleLike($product)
    {
        if ($this->isLike($product)) {
            Db::delete('client_product', array('client_id' => $this->getId(), 'product_id' => $product->getId()));
            return false;
        } else {
            Db::insert('client_product', array('client_id' => $this->getId(), 'product_id' => $product->getId()));
            return true;
        }
    }
    
    // Голосовал ли пользователь за товар
    public function isVote($product)
    {
        return Db::selectCell('select true from client_vote where client_id = :client_id and product_id = :product_id',
            array('client_id' => $this->getId(), 'product_id' => $product->getId()));
    }
    
    // Сохраняем факт голосования
    public function setVote($product)
    {
        if (!$this->isVote($product)) {
            Db::insert('client_vote', array('client_id' => $this->getId(), 'product_id' => $product->getId()));
        }
    }
}