<?php
namespace Adminko\Model;

use Adminko\System;
use Adminko\Metadata;
use Adminko\Db\Db;

class ProductModel extends Model
{
    public static $course = null;
    
    // Возвращает объект товара по артиклу
    public function getByName($product_article)
    {
        $record = Db::selectRow('select * from product where product_article = :product_article',
            array('product_article' => $product_article));
        if (!$record){
            throw new \AlarmException("Ошибка. Запись {$this->object}({$product_article}) не найдена.");
        }
        return $this->get($record['product_id'], $record);
    }
    
    // Возвращает каталог товара
    public function getCatalogue()
    {
        return Model::factory('catalogue')->get($this->getProductCatalogue());
    }

    // Возвращает URL товара
    public function getProductUrl()
    {
        return System::urlFor(array('controller' => 'product',
            'catalogue' => $this->getCatalogue()->getCatalogueName(),
            'action' => 'item', 'id' => $this->getProductId()));
    }

    // Возвращает цену товара
    public function getProductPrice()
    {
        if (is_null(self::$course)) {
            self::$course = get_preference('course');
        }
        
        if ($this->getProductPriceUsd()) {
            return round($this->getProductPriceUsd() * self::$course);
        } else {
            return $this->getProductPriceRub();
        }
    }
    
    // Возвращает список лучших товаров
    public function getBestProductList($limit = 4)
    {
        return Model::factory('product')->getList(
            array('product_active' => 1, 'product_best' => 1),
            array('rand()' => 'asc'), $limit
        );
    }
    
    // Возвращает изображения товара
    public function getPictureList()
    {
        return Model::factory('picture')->getList(
            array('picture_product' => $this->getId()), array('picture_order' => 'asc')
        );
    }
        
    // Возвращает изображение по умолчанию
    public function getProductImage()
    {
        $picture_list = Model::factory('picture')->getList(
            array('picture_product' => $this->getId()), array('picture_order' => 'asc'), 1
        );
        if (empty($picture_list)) {
            return get_preference('default_image');
        }
        $default_image = current($picture_list);
        return $default_image->getPictureImage();
    }
    
    // Возвращает видео товара
    public function getVideoList()
    {
        return Model::factory('video')->getList(
            array('video_product' => $this->getId()), array('video_order' => 'asc')
        );
    }
    
    // Возвращает файлы товара
    public function getDownloadList()
    {
        return Model::factory('download')->getList(
            array('download_product' => $this->getId()), array('download_order' => 'asc')
        );
    }
    
    // Возвращает список опций
    public function getOptionsList()
    {
        $records = Db::selectAll('
            select
                product.*
            from
                product
                inner join catalogue on product.product_catalogue = catalogue.catalogue_id
                inner join product_link on product_link.link_product_id = product.product_id
            where
                product_link.product_id = :product_id and
                product_active = :product_active and catalogue_active = :catalogue_active
            order by
                product.product_order',
            array('product_id' => $this->getId(), 'product_active' => 1, 'catalogue_active' => 1)
        );
        return $this->getBatch($records);
    }
    
    // Возвращает товары по статье
    public function getByArticle($article, $limit = null)
    {
        $records = Db::selectAll('
            select
                product.*
            from 
                product
                inner join catalogue on product.product_catalogue = catalogue.catalogue_id
                inner join product_article on product_article.product_id = product.product_id
            where
                product_article.article_id = :article_id and
                product_active = :product_active and catalogue_active = :catalogue_active
            order by
                product_order asc
            ' . ($limit ? ('limit ' . $limit) : ''),
            array('article_id' => $article->getId(), 'product_active' => 1, 'catalogue_active' => 1)
        );        
        return $this->getBatch($records);
    }
    
    // Возвращает список товаров пользователя
    public function getByClient($client)
    {
        $records = Db::selectAll('
            select
                product.*
            from 
                product
                inner join catalogue on product.product_catalogue = catalogue.catalogue_id
                inner join client_product on client_product.product_id = product.product_id
            where
                client_product.client_id = :client_id and
                product_active = :product_active and catalogue_active = :catalogue_active
            order by
                product_order asc',
            array('client_id' => $client->getId(), 'product_active' => 1, 'catalogue_active' => 1)
        );
        return $this->getBatch($records);
    }
    
    // Поисковый запрос
    public function getSearchResult($search_value)
    {
        $search_words = preg_split('/\s+/', $search_value);
            
        $filter_clause = array();
        foreach (array('product_article', 'product_title', 'product_description') as $field_name) {
            $field_filter_clause = array();
            foreach ($search_words as $search_index => $search_word) {
                $field_prefix = $field_name . '_' . $search_index;
                $field_filter_clause[] = 'lower(' . $field_name . ') like :' . $field_prefix;
                $filter_binds[$field_prefix] = '%' . mb_strtolower($search_word , 'utf-8') . '%';
            }
            $filter_clause[] = join(' and ', $field_filter_clause);
        }
        
        $records = Db::selectAll('
            select
                product.*
            from
                product
                inner join catalogue on product.product_catalogue = catalogue.catalogue_id
            where (' . join(' or ', $filter_clause) . ') and
                product_active = :product_active and catalogue_active = :catalogue_active
            order by
                product_order asc',
            $filter_binds + array('product_active' => 1, 'catalogue_active' => 1)
        );
        
        return $this->getBatch($records);
    }
        
    // Возвращает свойства товара
    public function getPropertyList()
    {
        $product_property_list = Db::selectAll('
                select
                    property.*, ifnull(property_value.value_title, product_property.value) as property_value
                from
                    property
                    left join product_property on product_property.property_id = property.property_id
                    left join property_value on property_value.value_property = property.property_id and
                        property_value.value_id = product_property.value
                where
                    product_property.product_id = :product_id and property.property_active = :property_active
                order by
                    property.property_order',
            array('product_id' => $this->getId(), 'property_active' => 1)
        );
        
        $property_list = array();
        foreach ($product_property_list as $product_property) {
            $property = Model::factory('property')->get($product_property['property_id'], $product_property)
                ->setPropertyValue($product_property['property_value']);
            $property_list[$property->getId()] = $property;
        }
        return $property_list;
    }
    
    // Возвращает название статуса
    public function getProductStateTitle()
    {
        $state_list = array_reindex(
            Metadata::$objects['product']['fields']['product_state']['values'], 'value'
        );
        return $state_list[$this->getProductState()]['title'];
    }
    
    // Добавляет оценку товару
    public function addMark($mark)
    {
        $voters = $this->getProductVoters();
        $rating = $this->getProductRating();
        
        $this->setProductVoters($voters + 1);
        $this->setProductRating(($rating * $voters + $mark) / ($voters + 1));
        
        return $this;
    }
    
    // Возвращает связанные упражнения
    public function getArticleList($limit = null)
    {
        return Model::factory('article')->getByProduct($this, $limit);
    }

}
