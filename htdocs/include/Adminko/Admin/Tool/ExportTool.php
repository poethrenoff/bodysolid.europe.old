<?php
namespace Adminko\Admin\Tool;

use Adminko\System;
use Adminko\Db\Db;
use Adminko\Model\Model;
use Adminko\Admin\Admin;

class ExportTool extends Admin
{
    protected function actionIndex()
    {
        $catalogue_tree = Model::factory('catalogue')->getTree(
            Model::factory('catalogue')->getList(
                array(), array('catalogue_order' => 'asc')
            )
        );
        
        $this->view->assign('title', $this->object_desc['title']);
        $this->view->assign('catalogue_tree', $catalogue_tree);
        
        $form_url = System::urlFor(array('object' => 'export', 'action' => 'export'));
        $this->view->assign('form_url', $form_url);

        $this->content = $this->view->fetch('admin/export/export');        
    }

    protected function actionExport()
    {
        $filter_conds = array();
        $filter_binds = array();

        $product_catalogue = init_string('product_catalogue');
        $product_title = init_string('product_title');
        $product_active = init_string('product_active');

        if ($product_catalogue != '') {
            $filter_conds[] = '(product.product_catalogue = :product_catalogue or product.product_catalogue in (select catalogue_id from catalogue where catalogue_parent = :catalogue_parent))';
            $filter_binds['product_catalogue'] = $product_catalogue;
            $filter_binds['catalogue_parent'] = $product_catalogue;
        }
        if ($product_title != '') {
            $filter_conds[] = 'product.product_title like :product_title';
            $filter_binds['product_title'] = '%' . $product_title . '%';
        }
        if ($product_active != '') {
            $filter_conds[] = 'product.product_active = :product_active';
            $filter_binds['product_active'] = $product_active;
        }

        $filter_clause = count($filter_conds) ? 'where ' . join(' and ', $filter_conds) : '';
        $product_query = 'select product_article, product_title, product_price_usd, product_price_rub, product_state
            from product ' . $filter_clause . ' order by product_title asc';
        $product_list = Db::selectAll($product_query, $filter_binds);

        $product_stream = array();
        foreach ($product_list as $product_item) {
            $product_item['product_price_usd'] = floatval($product_item['product_price_usd']);
            $product_item['product_price_rub'] = floatval($product_item['product_price_rub']);

            $product_string = array();
            foreach ($product_item as $product_field) {
                $product_string[] = '"' . str_replace('"', '""', iconv('UTF-8', 'windows-1251', $product_field)) . '"';
            }

            $product_stream[] = join(';', $product_string);
        }

        if (!count($product_stream)) {
            header('Location: ' . System::urlFor(array('object' => 'export', 'action' => 'index')));
            exit;
        }

        header('Content-type: application/octed-stream');
        header('Content-Disposition: attachment; filename="export.csv"');

        print join("\r\n", $product_stream);

        exit;
    }
}
