<?php
namespace Adminko\Module;

use Adminko\System;
use Adminko\Compare;
use Adminko\Model\Model;

class CompareModule extends Module
{
    protected function actionIndex()
    {
        $compare = Compare::factory();
        
        $property_list = Model::factory('property')->getPropertyList();

        $product_list = $compare->get();
        $property_compare_list = array();
        
        foreach ($product_list as $product_id) {
            $product_list[$product_id] = Model::factory('product')->get($product_id);
            $product_property_list = $product_list[$product_id]->getPropertyList();
            foreach ($product_property_list as $property_id => $product_property) {
                $property_compare_list[$property_id][$product_id] =
                    $product_property->getPropertyValue();
            }
        }
        
        foreach ($property_compare_list as $property_id => $property_value_list) {
            $property_list[$property_id]->setIsEqual(count($property_value_list) > 1 &&
                count($property_value_list) == $compare->count() && count(array_unique($property_value_list)) == 1);
            if ($property_list[$property_id]->getIsEqual() && init_string('show') == 'diff') {
                unset($property_list[$property_id]);
            }
        }

        $this->view->assign('product_list', $product_list);
        $this->view->assign('property_list', $property_list);

        $this->view->assign('property_compare_list', $property_compare_list);
        
        $this->content = $this->view->fetch('module/compare/index');
    }
    
    protected function actionInfo()
    {
        $this->view->assign(Compare::factory());
        $this->content = $this->view->fetch('module/compare/info');
    }
    
    protected function actionAdd()
    {
        $product = $this->getProduct(System::id());
        
        $compare = Compare::factory();
        $limit = max(1, intval($this->getParam('limit')));
        
        if ($compare->count() >= $limit) {
            $this->content = json_encode(array(
                'error' => 'Извините, сравнение более ' . decl_of_num($limit, array('товара', 'товаров', 'товаров')) . ' не предусмотрено',
            ));
        } else {
            $compare->add($product->getId());
            
            $this->view->assign($compare);
            $this->view->fetch('module/compare/info');
            
            $this->content = json_encode(array(
                'message' => $this->view->fetch('module/compare/info'),
            ));
        }
    }
    
    protected function actionDelete()
    {
        Compare::factory()->delete(System::id());
        System::redirectBack();
    }
    
    protected function actionClear()
    {
        Compare::factory()->clear();
        System::redirectBack();
    }
       
    /**
     * Получение товара
     */
    public function getProduct($id)
    {
        try {
            $product = Model::factory('product')->get($id);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$product->getProductActive()) {
            System::notFound();
        }
        return $product;
    }
    
    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}
