<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Db\Db;

class ArticleModel extends Model
{
    // Возвращает статью по системному имени
    public function getByName($article_name)
    {
        $record = Db::selectRow('select * from article where article_name = :article_name',
            array('article_name' => $article_name));
        if (!$record){
            throw new \AlarmException("Ошибка. Запись {$this->object}({$article_name}) не найдена.");
        }
        return $this->get($record['article_id'], $record);
    }
    
    // Возвращает статьи по группе
    public function getByGroup($group)
    {
        $records = Db::selectAll('
            select
                article.*
            from
                article
                inner join article_group_link on article_group_link.article_id = article.article_id
            where
                article_group_link.group_id = :group_id
            order by
                article_order',
            array('group_id' => $group->getId()));
        
        return $this->getBatch($records);
    }
    
    // Возвращает URL статьи
    public function getArticleUrl($action = 'item')
    {
        return System::urlFor(array('controller' => 'body_focus',
            'article' => $this->getArticleName(), 'action' => $action));
    }
    
    // Возвращает статьи по товару
    public function getByProduct($product, $limit = null)
    {
        $records = Db::selectAll('
            select
                article.*
            from 
                article
                inner join product_article on product_article.article_id = article.article_id
            where
                product_article.product_id = :product_id
            order by
                article_order asc
            ' . ($limit ? ('limit ' . $limit) : ''),
            array('product_id' => $product->getId())
        );        
        return $this->getBatch($records);
    }
    
    // Возвращает связанные товары
    public function getProductList($limit = null)
    {
        return Model::factory('product')->getByArticle($this, $limit);
    }
}
