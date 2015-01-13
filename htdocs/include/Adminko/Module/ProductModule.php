<?php
namespace Adminko\Module;

use Adminko\System;
use Adminko\Model\Model;

class ProductModule extends Module
{
    // Вывод списка товаров
    protected function actionIndex()
    {
        $catalogue_name = System::getParam('catalogue');
        $catalogue = $this->getCatalogue($catalogue_name);

        $this->view->assign($catalogue);
        $this->content = $this->view->fetch('module/product/list');
    }
    
    // Лучшие товары
    protected function actionBest()
    {
        $product_list = Model::factory('product')->getBestProductList();
        
        $this->view->assign('product_list', $product_list);
        $this->content = $this->view->fetch('module/product/best');
    }

    protected function actionMenu()
    {
        $catalogue_tree = Model::factory('catalogue')->getTree(
            Model::factory('catalogue')->getList(
                array('catalogue_active' => 1), array('catalogue_order' => 'asc')
            )
        );

        $this->view->assign($catalogue_tree);
        $this->content = $this->view->fetch('module/product/menu');
    }

    protected function actionItem()
    {
        $product = $this->getProduct(System::id());
        
        $this->view->assign('product', $product);
        $this->content = $this->view->fetch('module/product/item');
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
    
    /**
     * Получение каталога
     */
    public function getCatalogue($catalogue_name)
    {
        try {
            $catalogue = model::factory('catalogue')->getByName($catalogue_name);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$catalogue->getCatalogueActive()) {
            System::notFound();
        }
        return $catalogue;
    }
}
