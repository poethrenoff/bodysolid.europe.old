<?php
namespace Adminko\Db;

use Adminko\Db\Driver\Driver;

abstract class Dbs extends Db
{
    protected static $db_driver = null;

    protected static function getDriver()
    {
        if (self::$db_driver == null) {
            self::$db_driver = Driver::factory(DB_TYPE, DB_HOST, '', 'bodysolid', 'bodysolid', 'bodysolid');
        }

        return self::$db_driver;
    }
}
