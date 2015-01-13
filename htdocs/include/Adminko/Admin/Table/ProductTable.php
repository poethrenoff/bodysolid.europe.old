<?php
namespace Adminko\Admin\Table;

use Adminko\System;
use Adminko\Db\Db;
use Adminko\Field\Field;

class ProductTable extends Table
{
    protected function actionCopySave($redirect = true)
    {
        $primary_field = parent::actionCopySave(false);
        
        // Копируем свойства товара
        $product_properties = Db::selectAll('
            select property_id, value from product_property where product_id = :product_id', array('product_id' => System::id()));
        foreach ($product_properties as $product_property) {
            Db::insert('product_property', array('product_id' => $primary_field) + $product_property);
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

        parent::actionDelete(false);

        // Удаляем свойства товара
        Db::delete('product_property', array('product_id' => $primary_field));

        if ($redirect) {
            $this->redirect();
        }
    }

    protected function actionProperty()
    {
        $record = $this->getRecord();
        $primary_field = $record[$this->primary_field];

        $properties = Db::selectAll('
                select property.property_id, property.property_title, property.property_kind,
                    product_property.value, property.property_unit
                from property
                    left join product_property on product_property.property_id = property.property_id
                order by property.property_order');

        $form_fields = array();
        foreach ($properties as $property_index => $property_value) {
            $property_type = $property_value['property_kind'] == 'number' ? 'float' : $property_value['property_kind'];
            $property_errors = $property_type == 'float' ? array('float') : array();

            $form_fields['property[' . $property_value['property_id'] . ']'] = array(
                'title' => $property_value['property_title'] . ($property_value['property_unit'] ?
                    ' (' . $property_value['property_unit'] . ')' : ''),
                'type' => $property_type, 'errors' => $property_errors,
                'value' => Field::factory($property_type)->set($property_value['value'])->form());

            if ($property_value['property_kind'] == 'select') {
                $values = Db::selectAll('
                        select * from property_value
                        where value_property = :value_property
                        order by value_title', array('value_property' => $property_value['property_id']));

                $value_records = array();
                foreach ($values as $value) {
                    $value_records[] = array('value' => $value['value_id'], 'title' => $value['value_title']);
                }

                $form_fields['property[' . $property_value['property_id'] . ']']['values'] = $value_records;
            }
        }

        $record_title = $record[$this->main_field];
        $action_title = 'Редактирование свойств';

        $this->view->assign('record_title', $this->object_desc['title'] . ($record_title ? ' :: ' . $record_title : ''));
        $this->view->assign('action_title', $action_title);
        $this->view->assign('fields', $form_fields);

        $form_url = System::urlFor(array('object' => $this->object, 'action' => 'property_save', 'id' => $primary_field));
        $this->view->assign('form_url', $form_url);

        $prev_url = $this->restoreState();
        $this->view->assign('back_url', System::urlFor($prev_url));

        $this->content = $this->view->fetch('admin/form');
        $this->output['meta_title'] .= ($record_title ? ' :: ' . $record_title : '') . ' :: ' . $action_title;
    }

    protected function actionPropertySave($redirect = true)
    {
        $record = $this->getRecord();
        $primary_field = $record[$this->primary_field];

        $properties = Db::selectAll('
                select property.property_id, property.property_title, property.property_kind from property');

        $property_values = init_array('property');

        $insert_fields = array();
        foreach ($properties as $property_index => $property_value) {
            $property_type = $property_value['property_kind'] == 'number' ? 'float' : $property_value['property_kind'];
            $property_errors = $property_type == 'float' ? array('float') : array();

            $field = Field::factory($property_type)->set($property_values[$property_value['property_id']]);
            if (!($field->check($property_errors))) {
                throw new \AlarmException('Ошибочное значение поля "' . $property_value['property_title'] . '".');
            }
            $insert_fields[$property_value['property_id']] = $field->get();
        }

        Db::delete('product_property', array('product_id' => $primary_field));
        foreach ($insert_fields as $property_id => $property_value) {
            Db::insert('product_property', array(
                'product_id' => $primary_field, 'property_id' => $property_id, 'value' => $property_value));
        }

        if ($redirect) {
            $this->redirect();
        }
    }

    protected function getRecordActions($record)
    {
        $actions = parent::getRecordActions($record);

        $actions['property'] = array('title' => 'Свойства', 'url' =>
            System::urlFor(array('object' => $this->object, 'action' => 'property',
                'id' => $record[$this->primary_field])));

        return $actions;
    }
}
