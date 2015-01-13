<?php
namespace Adminko\Admin\Table;

class ArticleTable extends Table
{
    protected function actionAddSave($redirect = true)
    {
        if (!init_string('article_name')) {
            $_REQUEST['article_name'] = to_file_name(init_string('article_title'), true);
            unset($this->fields['article_name']['no_add']);
        }

        $primary_field = parent::actionAddSave(false);

        if ($redirect) {
            $this->redirect();
        }

        return $primary_field;
    }
}
