<?php
namespace Adminko\Model;

use Adminko\Metadata;

class PurchaseModel extends Model
{
    // Возвращает список позиций
    public function getItemList()
    {
        return Model::factory('purchase_item')->getList(
            array('item_purchase' => $this->getId())
        );
    }
    
    // Возвращает название статуса
    public function getPurchaseStatusTitle()
    {
        $status_list = array_reindex(
            Metadata::$objects['purchase']['fields']['purchase_status']['values'], 'value'
        );
        return $status_list[$this->getPurchaseStatus()]['title'];
    }
}
