<?php
namespace Adminko\Admin\Table;

class DownloadTable extends Table
{
    protected function actionAddSave($redirect = true)
    {
        if (isset($_FILES['download_file_file']['size']) && $_FILES['download_file_file']['size']) {
            $_REQUEST['download_size'] = $_FILES['download_file_file']['size'];
            unset($this->fields['download_size']['no_add']);
        }
        
        $primary_field = parent::actionAddSave(false);
        
        if ($redirect) {
            $this->redirect();
        }

        return $primary_field;
    }

    protected function actionEditSave($redirect = true)
    {
        if (isset($_FILES['download_file_file']['size']) && $_FILES['download_file_file']['size']) {
            $_REQUEST['download_size'] = $_FILES['download_file_file']['size'];
            unset($this->fields['download_size']['no_edit']);
        }
        
        parent::actionEditSave(false);

        if ($redirect) {
            $this->redirect();
        }
    }
}
