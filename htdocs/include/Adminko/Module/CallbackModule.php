<?php
namespace Adminko\Module;

use Adminko\Mail;
use Adminko\View;
use Adminko\System;
use Adminko\Session;

class CallbackModule extends Module
{
    protected function actionIndex()
    {
        if (!System::isAjax()) {
            System::notFound();
        }
        
        if (Session::flash('callback_complete')) {
            $this->content = $this->view->fetch('module/callback/complete');
        } else {
            $error = !empty($_POST) ? $this->sendRequest() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/callback/form');
        }
    }

    protected function sendRequest()
    {
        $error = array();

        $callback_person = init_string('callback_person');
        $callback_phone = init_string('callback_phone');

        if (is_empty($callback_person)) {
            $error['callback_person'] = 'Не заполнено обязательное поле';
        }
        if (is_empty($callback_phone)) {
            $error['callback_phone'] = 'Не заполнено обязательное поле';
        }         

        if (count($error)) {
            return $error;
        }

        // Отправка сообщения
        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');

        $callback_email = get_preference('callback_email');
        $callback_subject = get_preference('callback_subject');

        $callback_view = new View();
        $callback_message = $callback_view->fetch('module/callback/message');

        Mail::send($callback_email, $from_email, $from_name, $callback_subject, $callback_message);

        Session::flash('callback_complete', true);

        System::redirectTo(array('controller' => 'callback'));
    }

    // Отключаем кеширование
    protected function getCacheKey()
    {
        return false;
    }
}
