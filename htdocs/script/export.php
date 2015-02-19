<?php
namespace Adminko;

use Adminko\Db\Db;
use Adminko\Db\Dbs;

include_once '../config/config.php';

$picture_big_path = 'c:/Users/poethrenoff/Project/bodysolid/www/images/products/big/';
$alt_picture_big_path = 'c:/Users/poethrenoff/Project/bodysolid/www/images/products/additional/big/';

$picture_upload_path = 'c:/Users/poethrenoff/Project/bodysolid.europe/htdocs/upload/product/';
$picture_upload_link = '/upload/product/';

$categories = Dbs::selectAll("SELECT * FROM categories WHERE group_id = 5 ORDER BY category_order");

$catalogue_order = 0;
foreach ($categories as $category) {
    Db::insert('catalogue', array(
        'catalogue_parent' => 4,
        'catalogue_title' => $category['category_name'],
        'catalogue_short_title' => $category['category_short_name'],
        'catalogue_name' => to_file_name($category['category_short_name'], true),
        'catalogue_order' => ++$catalogue_order,
        'catalogue_active' => 1,
    ));
    $catalogue_id = Db::lastInsertId();

    $products = Dbs::selectAll("SELECT * FROM products WHERE category_id = '{$category['category_id']}' ORDER BY product_id");

    $product_order = 0;
    foreach ($products as $product) {
        Db::insert('product', array(
            'product_catalogue' => $catalogue_id,
            'product_title' => $product['product_name'],
            'product_article' => '',
            'product_description' => $product['product_full_desc'],
            'product_short_description' => $product['product_short_desc'],
            'product_price_usd' => $product['product_price'],
            'product_price_rub' => 0,
            'product_best' => 0,
            'product_state' => 'stock',
            'product_rating' => 0,
            'product_voters' => 0,
            'product_order' => ++$product_order,
            'product_active' => $product['product_active'],
        ));
        $product_id = Db::lastInsertId();
        
        if ($product['product_video']) {
            Db::insert('video', array(
                'video_product' => $product_id,
                'video_code' => $product['product_video'],
                'video_order' => 1,
            ));
        }
        
        if ($product['product_picture_big'] && file_exists($picture_big_path . $product['product_picture_big'])) {
            copy($picture_big_path . $product['product_picture_big'], $picture_upload_path . $product['product_picture_big']);
            
            $picture_order = 0;
            Db::insert('picture', array(
                'picture_product' => $product_id,
                'picture_image' => $picture_upload_link . $product['product_picture_big'],
                'picture_order' => ++$picture_order,
            ));
            
            $product_pictures = Dbs::selectAll("SELECT * FROM product_pictures WHERE product_id = '{$product['product_id']}' ORDER BY picture_id");
            foreach ($product_pictures as $product_picture) {
                if ($product_picture['picture_name_big'] && file_exists($alt_picture_big_path . $product_picture['picture_name_big'])) {
                    copy($alt_picture_big_path . $product_picture['picture_name_big'], $picture_upload_path . $product_picture['picture_name_big']);
                    Db::insert('picture', array(
                        'picture_product' => $product_id,
                        'picture_image' => $picture_upload_link . $product_picture['picture_name_big'],
                        'picture_order' => ++$picture_order,
                    ));
                }
            }
        }
    }    
}
