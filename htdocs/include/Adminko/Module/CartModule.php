<?php
namespace Adminko\Module;

use Adminko\Cart;
use Adminko\System;
use Adminko\Model\Model;

class CartModule extends Module
{
    protected function actionIndex()
    {
        $this->view->assign(Cart::factory());
        $this->content = $this->view->fetch('module/cart/cart');
    }

    protected function actionInfo()
    {
        $this->view->assign(Cart::factory());
        $this->content = $this->view->fetch('module/cart/info');
    }

    protected function actionAdd()
    {
        $product = $this->getProduct(System::id());
        $quantity = max(1, intval(init_string('quantity')));
        Cart::factory()->add(
            $product->getId(), $product->getProductPrice(), $quantity
        );

        $this->actionInfo();
    }

    protected function actionSave()
    {
        if (!empty($_POST)) {
            if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
                Cart::factory()->clear();

                foreach ($_POST['quantity'] as $id => $quantity) {
                    $product = $this->getProduct($id);
                    $quantity = max(1, intval($quantity));
                    Cart::factory()->add(
                        $product->getId(), $product->getProductPrice(), $quantity
                    );
                }
            }
        }
        $this->actionInfo();
    }

    protected function actionDelete()
    {
        Cart::factory()->delete(System::id());
        System::redirectBack();
    }

    protected function actionClear()
    {
        Cart::factory()->clear();
        System::redirectBack();
    }

    // Получаем товар
    protected function getProduct($id)
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
