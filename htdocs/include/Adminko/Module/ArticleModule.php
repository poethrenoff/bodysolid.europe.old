<?php
namespace Adminko\Module;

use Adminko\System;
use Adminko\Model\Model;

class ArticleModule extends Module
{
    protected function actionIndex()
    {
        $group_list = Model::factory('article_group')->getList();

        $this->view->assign('group_list', $group_list);
        $this->content = $this->view->fetch('module/article/index');
    }
    
    protected function actionItem()
    {
        $article_name = System::getParam('article');
        $article = $this->getArticle($article_name);

        $this->view->assign($article);
        $this->content = $this->view->fetch('module/article/item');
    }
        
    /**
     * Получение статьи
     */
    public function getArticle($article_name)
    {
        try {
            $article = Model::factory('article')->getByName($article_name);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        return $article;
    }

}
