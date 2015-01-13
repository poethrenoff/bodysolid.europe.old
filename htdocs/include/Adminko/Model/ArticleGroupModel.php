<?php
namespace Adminko\Model;

class ArticleGroupModel extends Model
{
    // Возвращает связанные статьи
    public function getArticleList()
    {
        return Model::factory('article')->getByGroup($this);
    }
}
