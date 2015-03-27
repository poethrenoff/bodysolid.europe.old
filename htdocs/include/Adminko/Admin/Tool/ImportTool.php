<?php
namespace Adminko\Admin\Tool;

use Adminko\System;
use Adminko\Db\Db;
use Adminko\Model\Model;
use Adminko\Admin\Admin;

class ImportTool extends Admin
{
    protected function actionIndex()
    {
        $this->view->assign('title', $this->object_desc['title']);
        
        if (init_string('result') == 'ok') {
            $this->view->assign('message', 'Импорт успешно завершен!');
        }
        if (init_string('result') == 'error') {
            $this->view->assign('message', 'При импорте произошла ошибка!');
        }
        
        $form_url = System::urlFor(array('object' => 'import', 'action' => 'import'));
        $this->view->assign('form_url', $form_url);

        $this->content = $this->view->fetch('admin/import/import'); 
    }

    protected function actionImport()
    {
        if (!( isset($_FILES['file']) && $_FILES['file'] )) {
            System::redirectTo(array('object' => 'import', 'action' => 'index'));
        }

        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
            System::redirectTo(array('object' => 'import', 'action' => 'index', 'result' => 'error'));
        }

        $type = init_string('type');

        $fhandle = fopen($_FILES['file']['tmp_name'], 'r');

        while (( $string = fgetcsv($fhandle, 512, ';', '"') ) !== false) {
            @list($product_article, $product_title, $product_price_usd, $product_price_rub, $product_state) = $string;

            $product_price_usd = str_replace(',', '.', $product_price_usd);
            $product_price_rub = str_replace(',', '.', $product_price_rub);

            $product_row = Db::selectRow('select * from product where product_article = :product_article', array('product_article' => $product_article));

            if ($product_row) {
                if ($type == 'price') {
                    $product_state = $product_row['product_state'];
                } elseif ($type == 'state') {
                    $product_price_usd = $product_row['product_price_usd'];
                    $product_price_rub = $product_row['product_price_rub'];
                }

                $product_price_usd = round($product_price_usd, 2);
                $product_price_rub = round($product_price_rub, 2);

                db::update('product', array(
                    'product_price_usd' => $product_price_usd,
                    'product_price_rub' => $product_price_rub,
                    'product_state' => $product_state), array('product_article' => $product_article));
            }
        }

        fclose($fhandle);

        System::redirectTo(array('object' => 'import', 'action' => 'index', 'result' => 'ok'));
    }
}
