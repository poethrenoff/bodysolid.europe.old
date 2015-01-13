<?php
namespace Adminko\Module;

use Adminko\Model\Model;

class SearchModule extends Module
{
    protected function actionIndex()
    {
        $search_value = trim(init_string('text'));
        
        if (!is_empty($search_value)) {
            $product_list = Model::factory('product')->getSearchResult($search_value);
            $this->view->assign('product_list', $product_list);
        }
        
        $this->content = $this->view->fetch('module/search/result');
    }
    
    protected function actionForm()
    {
        $this->content = $this->view->fetch('module/search/form');
    }
}
