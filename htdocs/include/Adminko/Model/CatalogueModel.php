<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Db\Db;

class CatalogueModel extends HierarchyModel
{
    // Возвращает объект каталога по системному имени
    public function getByName($catalogue_name)
    {
        $record = Db::selectRow('select * from catalogue where catalogue_name = :catalogue_name',
            array('catalogue_name' => $catalogue_name));
        if (!$record){
            throw new \AlarmException("Ошибка. Запись {$this->object}({$catalogue_name}) не найдена.");
        }
        return $this->get($record['catalogue_id'], $record);
    }
    
    // Возвращает список подкаталогов
    public function getCatalogueList()
    {
        return Model::factory('catalogue')->getList(
            array('catalogue_active' => 1, 'catalogue_parent' => $this->getId()),
            array('catalogue_order' => 'asc')
        );
    }
    
    // Возвращает список товаров каталога
    public function getProductList($limit = null)
    {
        return Model::factory('product')->getList(
            array('product_active' => 1, 'product_catalogue' => $this->getId()),
            array('product_order' => 'asc'), $limit
        );
    }
    
    // Возвращает URL каталога
    public function getCatalogueUrl()
    {
        return System::urlFor(array('controller' => 'product', 'catalogue' => $this->getCatalogueName()));
    }
}
