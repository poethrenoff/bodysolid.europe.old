<?php
namespace Adminko\Admin\Table;

use Adminko\Mail;
use Adminko\Module\TextModule;

class ClientTable extends Table
{
    protected function actionEditSave($redirect = true)
    {
        $record = $this->getRecord();
        $primary_field = $record[$this->primary_field];
        
        if ($record['client_status'] == 'new' && init_string('client_status') == 'confirm') {
            $client_email = init_string('client_email');
            $client_password = $_REQUEST['client_password'] = generate_key(8);
            
            $from_email = get_preference('from_email');
            $from_name = get_preference('from_name');
            $confirm_subject = get_preference('confirm_subject');

            $confirm_letter = TextModule::getByTag('confirm_letter');
            $confirm_letter = str_replace('{client_email}', $client_email, $confirm_letter);
            $confirm_letter = str_replace('{client_password}', $client_password, $confirm_letter);
            
            Mail::send($client_email, $from_email, $from_name, $confirm_subject, $confirm_letter);
        }
        
        parent::actionEditSave(false);

        if ($redirect) {
            $this->redirect();
        }
    }
}
