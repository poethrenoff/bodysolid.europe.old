<?php
namespace Adminko;

class Metadata
{
    public static $objects = array(
        /**
         * Таблица "Тексты"
         */
        'text' => array(
            'title' => 'Тексты',
            'fields' => array(
                'text_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'text_tag' => array('title' => 'Метка', 'type' => 'string', 'show' => 1, 'sort' => 'asc', 'errors' => array('require', 'alpha'), 'group' => array()),
                'text_title' => array('title' => 'Заголовок', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'text_content' => array('title' => 'Текст', 'type' => 'text', 'editor' => 1, 'errors' => array('require')),
            ),
        ),
        
        /**
         * Таблица "Меню"
         */
        'menu' => array(
            'title' => 'Меню',
            'fields' => array(
                'menu_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'menu_parent' => array('title' => 'Родительский элемент', 'type' => 'parent'),
                'menu_title' => array('title' => 'Заголовок', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'menu_page' => array('title' => 'Раздел', 'type' => 'table', 'table' => 'page'),
                'menu_url' => array('title' => 'URL', 'type' => 'string'),
                'menu_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('menu_parent')),
                'menu_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
        ),
        
        /**
         * Таблица "Тизеры"
         */
        'teaser' => array(
            'title' => 'Тизеры',
            'fields' => array(
                'teaser_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'teaser_title' => array('title' => 'Заголовок', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'teaser_image' => array('title' => 'Изображение', 'type' => 'image', 'upload_dir' => 'teaser', 'errors' => array('require')),
                'teaser_url' => array('title' => 'URL', 'type' => 'string', 'errors' => array('require')),
                'teaser_order' => array('title' => 'Порядок', 'type' => 'order'),
                'teaser_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
        ),
        
        /**
         * Таблица "Каталог"
         */
        'catalogue' => array(
            'title' => 'Каталог',
            'fields' => array(
                'catalogue_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'catalogue_parent' => array('title' => 'Родительский раздел', 'type' => 'parent'),
                'catalogue_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'catalogue_short_title' => array('title' => 'Короткое название', 'type' => 'string', 'errors' => array('require')),
                'catalogue_subtitle' => array('title' => 'Подзаголовок', 'type' => 'string'),
                'catalogue_name' => array( 'title' => 'Ссылка', 'type' => 'string', 'no_add' => 1, 'group' => array(), 'errors' => array('require')),
                'catalogue_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('catalogue_parent')),
                'catalogue_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
            'links' => array(
                'product' => array('table' => 'product', 'field' => 'product_catalogue'),
            ),
        ),
        
        /**
         * Таблица "Товары"
         */
        'product' => array(
            'title' => 'Товары',
            'fields' => array(
                'product_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'product_catalogue' => array('title' => 'Каталог', 'type' => 'table', 'table' => 'catalogue', 'errors' => array('require')),
                'product_title' => array('title' => 'Название', 'type' => 'string', 'errors' => array('require')),
                'product_article' => array('title' => 'Артикул', 'type' => 'string', 'main' => 1),
                'product_description' => array('title' => 'Описание', 'type' => 'text', 'editor' => 1),
                'product_short_description' => array('title' => 'Краткое описание', 'type' => 'text'),
                'product_price_usd' => array('title' => 'Цена в долларах', 'type' => 'float'),
                'product_price_rub' => array('title' => 'Цена в рублях', 'type' => 'float', 'errors' => array('require')),
                'product_best' => array('title' => 'Лучший товар', 'type' => 'boolean', 'filter' => 1),
                'product_state' => array('title' => 'Наличие', 'type' => 'select', 'filter' => 1, 'values' => array(
                        array('value' => 'stock', 'title' => 'В наличии'),
                        array('value' => 'absence', 'title' => 'Отсутствует'),
                        array('value' => 'order', 'title' => 'Под заказ')), 'show' => 1, 'errors' => array('require')),
                'product_rating' => array('title' => 'Рейтинг', 'type' => 'float', 'no_add' => true, 'no_edit' => true),
                'product_voters' => array('title' => 'Количество голосов', 'type' => 'int', 'no_add' => true, 'no_edit' => true),
                'product_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('product_catalogue')),
                'product_active' => array('title' => 'Видимость', 'type' => 'active'),
            ),
            'links' => array(
                'picture' => array('table' => 'picture', 'field' => 'picture_product'),
                'video' => array('table' => 'video', 'field' => 'video_product'),
                'download' => array('table' => 'download', 'field' => 'download_product'),
            ),
            'relations' => array(
                'article' => array( 'secondary_table' => 'article', 'relation_table' => 'product_article',
                    'primary_field' => 'product_id', 'secondary_field' => 'article_id', 'title' => 'Статьи' ),
            ),
        ),
        
        /**
         * Таблица "Изображения"
         */
        'picture' => array(
            'title' => 'Изображения',
            'fields' => array(
                'picture_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'picture_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'picture_image' => array('title' => 'Изображение', 'type' => 'image', 'upload_dir' => 'product', 'main' => 1, 'errors' => array('require')),
                'picture_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('picture_product')),
            )
        ),
        
        /**
         * Таблица "Видео"
         */
        'video' => array(
            'title' => 'Видео',
            'fields' => array(
                'video_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'video_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'video_code' => array('title' => 'Видео', 'type' => 'text', 'main' => 1, 'errors' => array('require')),
                'video_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('video_product')),
            )
        ),
        
        /**
         * Таблица "Файлы"
         */
        'download' => array(
            'title' => 'Файлы',
            'fields' => array(
                'download_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'download_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'download_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'download_file' => array('title' => 'Файл', 'type' => 'file', 'upload_dir' => 'download', 'errors' => array('require')),
                'download_size' => array('title' => 'Размер', 'type' => 'float', 'no_add' => 1, 'no_edit' => 1),
                'download_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('download_product')),
            )
        ),
        
        /**
         * Таблица "Связь товаров со статьями"
         */
        'product_article' => array(
            'title' => 'Связь товаров со статьями',
            'internal' => true,
            'fields' => array(
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product'),
                'article_id' => array('title' => 'Статья', 'type' => 'table', 'table' => 'article'),
            ),
        ),
        
        /**
         * Таблица "Свойства"
         */
        'property' => array(
            'title' => 'Свойства',
            'fields' => array(
                'property_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'property_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'property_kind' => array('title' => 'Тип свойства', 'type' => 'select', 'show' => 1, 'filter' => 1, 'values' => array(
                        array('value' => 'number', 'title' => 'Число'),
                        array('value' => 'string', 'title' => 'Строка'),
                        array('value' => 'select', 'title' => 'Список')), 'errors' => array('require')),
                'property_unit' => array('title' => 'Единица измерения', 'type' => 'string'),
                'property_order' => array('title' => 'Порядок', 'type' => 'order'),
                'property_active' => array('title' => 'Видимость', 'type' => 'active')
            ),
            'links' => array(
                'property_value' => array('table' => 'property_value', 'field' => 'value_property', 'show' => array('property_kind' => array('select')), 'ondelete' => 'cascade'),
            ),
        ),
        
        /**
         * Таблица "Значения свойств"
         */
        'property_value' => array(
            'title' => 'Значения свойств',
            'fields' => array(
                'value_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'value_property' => array('title' => 'Свойство', 'type' => 'table', 'table' => 'property', 'errors' => array('require')),
                'value_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
            ),
        ),
        
        /**
         * Таблица "Свойства товара"
         */
        'product_property' => array(
            'title' => 'Свойства товара',
            'internal' => 1,
            'fields' => array(
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'errors' => array('require')),
                'property_id' => array('title' => 'Свойство', 'type' => 'table', 'table' => 'property', 'errors' => array('require')),
                'value' => array('title' => 'Значение', 'type' => 'string', 'errors' => array('require')),
            ),
        ),
                
        /**
         * Таблица "Группы мышц"
         */
        'article_group' => array(
            'title' => 'Группы мышц',
            'fields' => array(
                'group_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'group_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'group_name' => array( 'title' => 'Ссылка', 'type' => 'string', 'group' => array(), 'errors' => array('require')),
                'group_description' => array('title' => 'Описание', 'type' => 'text', 'editor' => 1),
            ),
        ),
        
        /**
         * Таблица "Упражнения"
         */
        'article' => array(
            'title' => 'Упражнения',
            'fields' => array(
                'article_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'article_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'article_name' => array( 'title' => 'Ссылка', 'type' => 'string', 'no_add' => 1, 'group' => array(), 'errors' => array('require')),
                'article_text' => array('title' => 'Статья', 'type' => 'text', 'editor' => 1, 'errors' => array('require')),
                'article_order' => array('title' => 'Порядок', 'type' => 'order'),
            ),
            'relations' => array(
                'product' => array( 'secondary_table' => 'product', 'relation_table' => 'product_article',
                    'primary_field' => 'article_id', 'secondary_field' => 'product_id', 'title' => 'Товары' ),
                'article_group' => array( 'secondary_table' => 'article_group', 'relation_table' => 'article_group_link',
                    'primary_field' => 'article_id', 'secondary_field' => 'group_id', 'title' => 'Группы мыщц' ),
            ),
        ),
        
        /**
         * Таблица "Связь упражнений с группами мышц"
         */
        'article_group_link' => array(
            'title' => 'Связь упражнений с группами мышц',
            'internal' => true,
            'fields' => array(
                'group_id' => array('title' => 'Группа мышц', 'type' => 'table', 'table' => 'article_group'),
                'article_id' => array('title' => 'Упражнение', 'type' => 'table', 'table' => 'article'),
            ),
        ),
        
        /**
         * Таблица "Пользователи"
         */
        'client' => array(
            'title' => 'Пользователи',
            'no_add' => true, 'no_delete' => true,            
            'fields' => array(
                'client_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'client_title' => array('title' => 'Название компании', 'type' => 'string', 'main' => 1, 'errors' => 'require'),
                'client_email' => array('title' => 'Email', 'type' => 'string',  'errors' => 'require|email'),
                'client_password' => array('title' => 'Пароль', 'type' => 'password'),
                'client_phone' => array('title' => 'Телефон', 'type' => 'string', 'errors' => 'require'),
                'client_person' => array('title' => 'Контактное лицо', 'type' => 'string', 'errors' => 'require'),
                'client_legal_address' => array('title' => 'Юридический адрес', 'type' => 'text', 'errors' => 'require'),
                'client_actual_address' => array('title' => 'Фактический адрес', 'type' => 'text'),
                'client_info' => array('title' => 'Дополнительная информация', 'type' => 'text'),
                'client_status' => array('title' => 'Статус', 'type' => 'select', 'show' => 1, 'filter' => 1, 'values' => array(
                        array('value' => 'new', 'title' => 'Новый'),
                        array('value' => 'confirm', 'title' => 'Подтвержден'),
                        array('value' => 'reject', 'title' => 'Отклонен')), 'show' => 1, 'errors' => array('require')),
            ),
        ),
        
        /**
         * Таблица "Заказы"
         */
        'purchase' => array(
            'title' => 'Заказы',
            'no_add' => true, 'no_delete' => true,
            'fields' => array(
                'purchase_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'purchase_client' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client', 'main' => 1),
                'purchase_person' => array('title' => 'Контактное лицо', 'type' => 'string'),
                'purchase_email' => array('title' => 'Email', 'type' => 'string'),
                'purchase_phone' => array('title' => 'Телефон', 'type' => 'string'),
                'purchase_address' => array('title' => 'Адрес', 'type' => 'text'),
                'purchase_shipping' => array('title' => 'Способ отгрузки', 'type' => 'text'),
                'purchase_comment' => array('title' => 'Комментарий', 'type' => 'text'),
                'purchase_date' => array('title' => 'Дата заказа', 'type' => 'datetime', 'show' => 1, 'sort' => 'desc', 'errors' => 'require'),
                'purchase_sum' => array('title' => 'Сумма заказа', 'type' => 'float', 'show' => 1, 'errors' => 'require'),
                'purchase_status' => array('title' => 'Статус заказа', 'type' => 'select', 'filter' => 1, 'values' => array(
                        array('value' => 'new', 'title' => 'Новый'),
                        array('value' => 'confirm', 'title' => 'Подтвержден'),
                        array('value' => 'deliver', 'title' => 'В доставке'),
                        array('value' => 'complete', 'title' => 'Выполнен'),
                        array('value' => 'cancel', 'title' => 'Отменен')), 'show' => 1, 'errors' => 'require'),
            ),
            'links' => array(
                'purchase_item' => array('table' => 'purchase_item', 'field' => 'item_purchase'),
            )
        ),
        
        /**
         * Таблица "Позиции заказа"
         */
        'purchase_item' => array(
            'title' => 'Позиции заказа',
            'no_add' => true,
            'fields' => array(
                'item_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'item_purchase' => array('title' => 'Заказ', 'type' => 'table', 'table' => 'purchase', 'errors' => array('require'), 'no_edit' => 1),
                'item_product' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product', 'main' => 1, 'errors' => array('require')),
                'item_price' => array('title' => 'Цена', 'type' => 'float', 'show' => 1, 'errors' => array('require')),
                'item_quantity' => array('title' => 'Количество', 'type' => 'int', 'show' => 1, 'errors' => array('require'))
            )
        ),
        
        /**
         * Таблица "Избранное пользователей"
         */
        'client_product' => array(
            'title' => 'Избранное пользователей',
            'internal' => true,
            'fields' => array(
                'client_id' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client'),
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product'),
            ),
        ),
        
        /**
         * Таблица "Голоса пользователей"
         */
        'client_vote' => array(
            'title' => 'Голоса пользователей',
            'internal' => true,
            'fields' => array(
                'client_id' => array('title' => 'Пользователь', 'type' => 'table', 'table' => 'client'),
                'product_id' => array('title' => 'Товар', 'type' => 'table', 'table' => 'product'),
            ),
        ),
        
        ////////////////////////////////////////////////////////////////////////////////////////
        
        /**
         * Таблица "Настройки"
         */
        'preference' => array(
            'title' => 'Настройки',
            'class' => 'Builder',
            'fields' => array(
                'preference_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'preference_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'preference_name' => array('title' => 'Имя', 'type' => 'string', 'show' => 1, 'filter' => 1, 'errors' => array('require', 'alpha'), 'group' => array()),
                'preference_value' => array('title' => 'Значение', 'type' => 'string', 'show' => 1),
            ),
        ),
        
        /**
         * Таблица "Разделы"
         */
        'page' => array(
            'title' => 'Разделы',
            'fields' => array(
                'page_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'page_parent' => array('title' => 'Родительский раздел', 'type' => 'parent'),
                'page_layout' => array('title' => 'Шаблон', 'type' => 'table', 'table' => 'layout', 'errors' => array('require')),
                'page_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'page_name' => array('title' => 'Каталог', 'type' => 'string', 'show' => 1, 'errors' => array('alpha'), 'group' => array('page_parent')),
                'page_folder' => array('title' => 'Папка', 'type' => 'boolean'),
                'meta_title' => array('title' => 'Заголовок', 'type' => 'text'),
                'meta_keywords' => array('title' => 'Ключевые слова', 'type' => 'text'),
                'meta_description' => array('title' => 'Описание', 'type' => 'text'),
                'page_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('page_parent')),
                'page_active' => array('title' => 'Видимость', 'type' => 'active'),
             ),
            'links' => array(
                'block' => array('table' => 'block', 'field' => 'block_page', 'ondelete' => 'cascade'),
             ),
        ),
        
        /**
         * Таблица "Блоки"
         */
        'block' => array(
            'title' => 'Блоки',
            'fields' => array(
                'block_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'block_page' => array('title' => 'Раздел', 'type' => 'table', 'table' => 'page', 'errors' => array('require')),
                'block_module' => array('title' => 'Модуль', 'type' => 'table', 'table' => 'module', 'errors' => array('require')),
                'block_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'block_area' => array('title' => 'Область шаблона', 'type' => 'table', 'table' => 'layout_area', 'errors' => array('require')),
             ),
            'links' => array(
                'block_param' => array('table' => 'block_param', 'field' => 'block', 'ondelete' => 'cascade'),
             ),
        ),
        
        /**
         * Таблица "Шаблоны"
         */
        'layout' => array(
            'title' => 'Шаблоны',
            'fields' => array(
                'layout_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'layout_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'layout_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'errors' => array('require', 'alpha')),
             ),
            'links' => array(
                'page' => array('table' => 'page', 'field' => 'page_layout', 'hidden' => 1),
                'area' => array('table' => 'layout_area', 'field' => 'area_layout', 'title' => 'Области'),
             ),
        ),
        
        /**
         * Таблица "Области шаблона"
         */
        'layout_area' => array(
            'title' => 'Области шаблона',
            'class' => 'Builder',
            'fields' => array(
                'area_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'area_layout' => array('title' => 'Шаблон', 'type' => 'table', 'table' => 'layout', 'errors' => array('require')),
                'area_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'area_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'errors' => array('require', 'alpha')),
                'area_main' => array('title' => 'Главная область', 'type' => 'default', 'show' => 1, 'group' => array('area_layout')),
                'area_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('area_layout')),
             ),
            'links' => array(
                'bloсk' => array('table' => 'block', 'field' => 'block_area'),
             ),
        ),
        
        /**
         * Таблица "Модули"
         */
        'module' => array(
            'title' => 'Модули',
            'fields' => array(
                'module_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'module_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'module_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'group' => array(), 'errors' => array('require', 'alpha')),
             ),
            'links' => array(
                'block' => array('table' => 'block', 'field' => 'block_module'),
                'module_param' => array('table' => 'module_param', 'field' => 'param_module', 'title' => 'Параметры', 'ondelete' => 'cascade'),
             ),
        ),
        
        /**
         * Таблица "Параметры модулей"
         */
        'module_param' => array(
            'title' => 'Параметры модулей',
            'fields' => array(
                'param_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'param_module' => array('title' => 'Модуль', 'type' => 'table', 'table' => 'module', 'errors' => array('require')),
                'param_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'param_type' => array('title' => 'Тип параметра', 'type' => 'select', 'filter' => 1, 'values' => array(
                        array('value' => 'string', 'title' => 'Строка'),
                        array('value' => 'int', 'title' => 'Число'),
                        array('value' => 'text', 'title' => 'Текст'),
                        array('value' => 'select', 'title' => 'Список'),
                        array('value' => 'table', 'title' => 'Таблица'),
                        array('value' => 'boolean', 'title' => 'Флаг')), 'show' => 1, 'errors' => array('require')),
                'param_name' => array('title' => 'Системное имя', 'type' => 'string', 'show' => 1, 'group' => array('param_module'), 'errors' => array('require', 'alpha')),
                'param_table' => array('title' => 'Имя таблицы', 'type' => 'select', 'values' => '__OBJECT__', 'show' => 1),
                'param_default' => array('title' => 'Значение по умолчанию', 'type' => 'string'),
                'param_require' => array('title' => 'Обязательное', 'type' => 'boolean'),
                'param_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('param_module')),
             ),
            'links' => array(
                'param_value' => array('table' => 'param_value', 'field' => 'value_param', 'show' => array('param_type' => array('select')), 'title' => 'Значения', 'ondelete' => 'cascade'),
                'block_param' => array('table' => 'block_param', 'field' => 'param', 'ondelete' => 'cascade'),
             ),
        ),
        
        /**
         * Таблица "Значения параметров модулей"
         */
        'param_value' => array(
            'title' => 'Значения параметров модулей',
            'fields' => array(
                'value_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'value_param' => array('title' => 'Параметр', 'type' => 'table', 'table' => 'module_param', 'errors' => array('require')),
                'value_title' => array('title' => 'Название', 'type' => 'string', 'main' => 1, 'errors' => array('require')),
                'value_content' => array('title' => 'Значение', 'type' => 'string', 'show' => 1, 'group' => array('value_param'), 'errors' => array('require')),
                'value_default' => array('title' => 'По умолчанию', 'type' => 'default', 'show' => 1, 'group' => array('value_param')),
             ),
        ),
        
        /**
         * Таблица "Параметры блоков"
         */
        'block_param' => array(
            'title' => 'Параметры блоков',
            'internal' => true,
            'fields' => array(
                'block' => array('title' => 'Блок', 'type' => 'table', 'table' => 'block'),
                'param' => array('title' => 'Параметр', 'type' => 'table', 'table' => 'module_param'),
                'value' => array('title' => 'Значение', 'type' => 'text'),
             ),
        ),
        
        /**
         * Таблицы управления правами доступа
         */
        
        'admin' => array(
            'title' => 'Администраторы',
            'fields' => array(
                'admin_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'admin_title' => array('title' => 'Имя', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'admin_login' => array('title' => 'Логин', 'type' => 'string', 'show' => 1, 'errors' => array('require', 'alpha'), 'group' => array()),
                'admin_password' => array('title' => 'Пароль', 'type' => 'password'),
                'admin_email' => array('title' => 'Email', 'type' => 'string', 'errors' => array('email')),
                'admin_active' => array('title' => 'Активный', 'type' => 'active'),
             ),
            'relations' => array(
                'admin_role' => array('secondary_table' => 'role', 'relation_table' => 'admin_role',
                    'primary_field' => 'admin_id', 'secondary_field' => 'role_id'),
             ),
        ),
        
        'admin_role' => array(
            'title' => 'Роли администраторов',
            'internal' => true,
            'fields' => array(
                'admin_id' => array('title' => 'Администратор', 'type' => 'table', 'table' => 'admin', 'errors' => array('require')),
                'role_id' => array('title' => 'Роль', 'type' => 'table', 'table' => 'role', 'errors' => array('require')),
             ),
        ),
        
        'role' => array(
            'title' => 'Роли',
            'fields' => array(
                'role_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'role_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'role_default' => array('title' => 'Главный администратор', 'type' => 'default', 'show' => 1),
             ),
            'relations' => array(
                'role_object' => array('secondary_table' => 'object', 'relation_table' => 'role_object',
                    'primary_field' => 'role_id', 'secondary_field' => 'object_id'),
             ),
        ),
        
        'role_object' => array(
            'title' => 'Права на системные разделы',
            'internal' => true,
            'fields' => array(
                'role_id' => array('title' => 'Роль', 'type' => 'table', 'table' => 'role', 'errors' => array('require')),
                'object_id' => array('title' => 'Системный раздел', 'type' => 'table', 'table' => 'object', 'errors' => array('require')),
             ),
        ),
        
        'object' => array(
            'title' => 'Системные разделы',
            'fields' => array(
                'object_id' => array('title' => 'Идентификатор', 'type' => 'pk'),
                'object_parent' => array('title' => 'Родительский раздел', 'type' => 'parent'),
                'object_title' => array('title' => 'Название', 'type' => 'string', 'show' => 1, 'main' => 1, 'errors' => array('require')),
                'object_name' => array('title' => 'Объект', 'type' => 'select', 'values' => '__OBJECT__'),
                'object_order' => array('title' => 'Порядок', 'type' => 'order', 'group' => array('object_parent')),
                'object_active' => array('title' => 'Видимость', 'type' => 'active'),
            )
        ),

        /**
         * Утилита "Файл-менеджер"
         */
        'fm' => array(
            'title' => 'Файл-менеджер',
            'class' => 'Fm',
        ),
   );
}

//\Adminko\Db\Db::create();
