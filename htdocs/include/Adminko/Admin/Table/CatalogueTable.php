<?php
namespace Adminko\Admin\Table;

class CatalogueTable extends Table
{
    protected function actionAddSave($redirect = true)
    {
        if (!init_string('catalogue_name')) {
            $_REQUEST['catalogue_name'] = to_file_name(init_string('catalogue_short_title'), true);
            unset($this->fields['catalogue_name']['no_add']);
        }

        $primary_field = parent::actionAddSave(false);

        if ($redirect) {
            $this->redirect();
        }

        return $primary_field;
    }
}
