<?php
namespace Adminko\Module;

use Adminko\Model\Model;

class TeaserModule extends Module
{
    // Вывод тизеров
    protected function actionIndex()
    {
        $teaser_list = Model::factory('teaser')->getList(array('teaser_active' => 1), array('teaser_order' => 'asc'));

        $this->view->assign('teaser_list', $teaser_list);
        $this->content = $this->view->fetch('module/teaser/index');
    }
}
