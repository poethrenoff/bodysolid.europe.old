<?php
namespace Adminko\Admin\Table;

use Adminko\Db\Db;

class PropertyValueTable extends Table
{
    protected function actionDelete($redirect = true)
    {
        $record = $this->getRecord();
        $primary_field = $record[$this->primary_field];
        
        $value_property = Db::selectCell('
                select value_property from property_value where value_id = :value_id',
            array('value_id' => $primary_field));
        
        $records_count = Db::selectCell('
                select count(*) from product_property where property_id = :property_id and value = :value',
            array('property_id' => $value_property, 'value' => $primary_field));
        
        if ($records_count) {
            throw new \AlarmException('Ошибка. Невозможно удалить запись, так как у нее есть зависимые записи в таблице "Свойства товаров".');
        }

        parent::actionDelete($redirect);
    }
}
