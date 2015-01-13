<?php
namespace Adminko\Admin\Table;

use Adminko\System;
use Adminko\Db\Db;

class PropertyTable extends Table
{
    protected function actionCopySave($redirect = true)
    {
        $primary_field = parent::actionCopySave(false);

        $values = Db::selectAll('select * from property_value where value_property = :value_property', array('value_property' => System::id()));

        foreach ($values as $value) {
            Db::insert('property_value', array('value_property' => $primary_field, 'value_title' => $value['value_title']));
        }

        if ($redirect) {
            $this->redirect();
        }

        return $primary_field;
    }

    protected function actionDelete($redirect = true)
    {
        $record = $this->getRecord();
        $primary_field = $record[$this->primary_field];

        $records_count = Db::selectCell('
                select count(*) from product_property where property_id = :property_id', array('property_id' => $primary_field));

        if ($records_count) {
            throw new \AlarmException('Ошибка. Невозможно удалить запись, так как у нее есть зависимые записи в таблице "Свойства товаров".');
        }

        parent::actionDelete($redirect);
    }
}
