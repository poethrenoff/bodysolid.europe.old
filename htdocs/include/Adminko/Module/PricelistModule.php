<?php
namespace Adminko\Module;

use Adminko\Model\Model;

class PricelistModule extends Module
{
    protected function actionIndex()
    {
        $catalogue_tree = Model::factory('catalogue')->getTree(
            Model::factory('catalogue')->getList(
                array('catalogue_active' => 1), array('catalogue_order' => 'asc')
            )
        );

        $this->view->assign($catalogue_tree);
        $this->content = $this->view->fetch('module/pricelist/index');
    }
}
