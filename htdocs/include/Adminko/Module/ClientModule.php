<?php
namespace Adminko\Module;

use Adminko\Cookie;
use Adminko\Mail;
use Adminko\Session;
use Adminko\System;
use Adminko\Paginator;
use Adminko\Model\Model;
use Adminko\Module\Module;
use Adminko\Valid\Valid;
use Adminko\View;

class ClientModule extends Module
{
    const SESSION_VAR = '__client__';
    
    /**
     * Текущий пользователь
     */
    protected $client = null;

    public function actionIndex()
    {
        if (self::isAuth()) {
            System::redirectTo(array('controller' => 'client/purchase'));
        } else {
            $error = !empty($_POST) ? $this->authFromRequest() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/client/form');
        }
    }

    /**
     * Регистрация
     */
    public function actionRegistration()
    {
        if (Session::flash('registration_complete')) {
            $this->content = $this->view->fetch('module/client/registration/complete');
        } else {
            $error = !empty($_POST) ? $this->addClient() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/client/registration/form');
        }
    }

    /**
     * Восстановление пароля
     */
    public function actionRecovery()
    {
        if (Session::flash('recovery_complete')) {
            $this->content = $this->view->fetch('module/client/recovery/complete');
        } else {
            $error = !empty($_POST) ? $this->recoveryPassword() : array();

            $this->view->assign('error', $error);
            $this->content = $this->view->fetch('module/client/recovery/form');
        }
    }

    /**
     * Ваши настройки
     */
    public function actionProfile()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } elseif (Session::flash('profile_complete')) {
            $this->content = $this->view->fetch('module/client/profile/complete');
        } else {
            $this->client = self::getInfo();
            $error = !empty($_POST) ? $this->saveClient() : array();

            $this->view->assign('error', $error);
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/profile/form');
        }
    }

    /**
     * Ваши заказы
     */
    public function actionPurchase()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();

            $total = $this->client->getPurchaseCount();
            $count = max(1, intval($this->getParam('count', 5)));
            $pages = Paginator::create($total, array('by_page' => $count));
            $purchase_list = $this->client->getPurchaseList($pages['by_page'], $pages['offset']);
            
            $this->view->assign('client', $this->client);
            $this->view->assign('purchase_list', $purchase_list);
            $this->view->assign('pages', Paginator::fetch($pages));
            $this->content = $this->view->fetch('module/client/purchase/index');
        }
    }
    
    /**
     * Ваши товары
     */
    public function actionProduct()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();
            $this->view->assign('client', $this->client);
            $this->content = $this->view->fetch('module/client/product/index');
        }
    }
    
    /**
     * Добавить или удалить товар из Избранного
     */
    public function actionLike()
    {
        if (!self::isAuth()) {
            System::redirectTo(array('controller' => 'client'));
        } else {
            $this->client = self::getInfo();   
            $product = $this->getProduct(System::id());
            
            $this->content = json_encode(
                $this->client->toggleLike($product)
            );
        }
    }
    
    /**
     * Выход с сайта
     */
    public function actionLogout()
    {
        if (self::isAuth()) {
            unset($_SESSION[self::SESSION_VAR]);
            self::clearClientCookie();
        }

        System::redirectBack();
    }

    /**
     * Панель пользователя
     */
    public function actionInfo()
    {
        $this->view->assign('client', self::getInfo());
        $this->content = $this->view->fetch('module/client/info');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * Добавление нового пользователя
     */
    public function addClient()
    {
        $error = array();

        $field_list = array(
            'client_title', 'client_email', 'client_phone', 'client_person', 'client_legal_address');
        foreach ($field_list as $field_name) {
            if (is_empty($$field_name = trim(init_string($field_name)))) {
                $error[$field_name] = 'Поле обязательно для заполнения';
            }
        }
        $client_actual_address = trim(init_string('client_actual_address'));

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }
        if (!isset($error['client_email']) && Model::factory('client')->getByEmail($client_email)) {
            $error['client_email'] = 'Пользователь с таким электронным адресом уже зарегистрирован';
        }

        if (count($error)) {
            return $error;
        }
        
        // Добавление пользователя
        $client = Model::factory('client')
            ->setClientTitle($client_title)
            ->setClientEmail($client_email)
            ->setClientPhone($client_phone)
            ->setClientPerson($client_person)
            ->setClientLegalAddress($client_legal_address)
            ->setClientActualAddress($client_actual_address)
            ->setClientStatus('new')
            ->save();

        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');
        $registration_subject = get_preference('registration_subject');
        $registration_email = get_preference('registration_email');

        $client_view = new View();
        $client_view->assign('client', $client);
        $manager_letter = $client_view->fetch('module/client/registration/manager_letter');
        
        Mail::send($registration_email, $from_email, $from_name, $registration_subject, $manager_letter);

        Session::flash('registration_complete', true);

        System::redirectBack();
    }

    /**
     * Изменение личных данных
     */
    public function saveClient()
    {
        $error = array();

        $field_list = array(
            'client_title', 'client_email', 'client_phone', 'client_person', 'client_legal_address');
        foreach ($field_list as $field_name) {
            if (is_empty($$field_name = trim(init_string($field_name)))) {
                $error[$field_name] = 'Поле обязательно для заполнения';
            }
        }
        $client_actual_address = trim(init_string('client_actual_address'));

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }
        if (!isset($error['client_email']) && ($client = Model::factory('client')->getByEmail($client_email)) && ($client->getId() != $this->client->getId())) {
            $error['client_email'] = 'Пользователь с таким электронным адресом уже зарегистрирован';
        }

        if (count($error)) {
            return $error;
        }
        $field_list = array(
            'client_password_old', 'client_password', 'client_password_confirm');
        $change_password = false;
        foreach ($field_list as $field_name) {
            $change_password |=!is_empty($$field_name = trim(init_string($field_name)));
        }

        if ($change_password) {
            foreach ($field_list as $field_name) {
                if (is_empty($$field_name)) {
                    $error[$field_name] = 'Поле обязательно для заполнения';
                }
            }

            if (!isset($error['client_password_old']) && strcmp(md5($client_password_old), $this->client->getClientPassword())) {
                $error['client_password_old'] = 'Неверное значение старого пароля';
            }
            if (!isset($error['client_password']) && !isset($error['client_password_confirm']) &&
                strcmp($client_password, $client_password_confirm)) {
                $error['client_password_confirm'] = 'Пароли не совпадают';
            }
        }

        if (count($error)) {
            return $error;
        }

        // Сохранение профиля
        $this->client
            ->setClientTitle($client_title)
            ->setClientEmail($client_email)
            ->setClientPhone($client_phone)
            ->setClientPerson($client_person)
            ->setClientLegalAddress($client_legal_address)
            ->setClientActualAddress($client_actual_address);
        if (!is_empty($client_password)) {
            $this->client->setClientPassword(md5($client_password));
        }
        $this->client->save();

        if (init_cookie('client')) {
            self::setClientCookie($this->client);
        }

        Session::flash('profile_complete', true);

        System::redirectBack();
    }

    /**
     * Отправка нового пароля
     */
    public function recoveryPassword()
    {
        $error = array();

        $field_list = array('client_email');
        foreach ($field_list as $field_name)
            if (is_empty($$field_name = trim(init_string($field_name))))
                $error[$field_name] = 'Поле обязательно для заполнения';

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }
        if (!isset($error['client_email']) && !($client = Model::factory('client')->getByEmail($client_email))) {
            $error['client_email'] = 'Пользователь с таким электронным адресом не зарегистрирован';
        }
        if (!isset($error['client_email']) && $client->getClientStatus() == 'new') {
            $error['client_email'] = 'Ваша регистрация ожидает подтверждения';
        }
        if (!isset($error['client_email']) && $client->getClientStatus() == 'reject') {
            $error['client_email'] = 'К сожалению, вам отказано в регистрации';
        }

        if (count($error)) {
            return $error;
        }

        $client_password = generate_key(8);
        $client->setClientPassword(md5($client_password))->save();

        $from_email = get_preference('from_email');
        $from_name = get_preference('from_name');
        $recovery_subject = get_preference('recovery_subject');

        $recovery_letter = TextModule::getByTag('recovery_letter');
        $recovery_letter = str_replace('{client_password}', $client_password, $recovery_letter);

        Mail::send($client_email, $from_email, $from_name, $recovery_subject, $recovery_letter);

        Session::flash('recovery_complete', true);

        System::redirectBack();
    }
    
    /**
     * Аутентификация из формы
     */
    public static function authFromRequest()
    {
        $error = array();

        $field_list = array(
            'client_email', 'client_password');
        foreach ($field_list as $field_name) {
            if (is_empty($$field_name = trim(init_string($field_name)))) {
                $error[$field_name] = 'Поле обязательно для заполнения';
            }
        }

        if (!isset($error['client_email']) && !Valid::factory('email')->check($client_email)) {
            $error['client_email'] = 'Поле заполнено некорректно';
        }

        if (count($error)) {
            return $error;
        }

        try {
            $client = self::auth($client_email, md5($client_password));
        } catch (\Exception $e) {
            return array(
                'client_email' => $e->getMessage(),
            );
        }

        if (init_string('client_remember')) {
            self::setClientCookie($client);
        }

        $_SESSION[self::SESSION_VAR] = $client->getId();

        System::redirectBack();
    }

    /**
     * Аутентификация по кукам
     */
    public static function authFromCookie()
    {
        @list($client_email, $client_password) = Cookie::getData('client');

        try {
            $client = self::auth($client_email, $client_password);
        } catch (\Exception $e) {
            return false;
        }

        $_SESSION[self::SESSION_VAR] = $client->getId();

        return true;
    }

    /**
     * Авторизован ли пользователь
     */
    public static function isAuth()
    {
        if (isset($_SESSION[self::SESSION_VAR])) {
            return true;
        }
        return self::authFromCookie();
    }

    /**
     * Возвращает информацию о текущем пользователе
     */
    public static function getInfo()
    {
        if (self::isAuth()) {
            try {
                return Model::factory('client')->get($_SESSION[self::SESSION_VAR]);
            } catch (\AlarmException $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Аутентификация пользователя по логину и паролю
     */
    public static function auth($client_email, $client_password)
    {
        try {
            $client = Model::factory('client')->getByEmail($client_email, true);
        } catch (\AlarmException $e) {
            throw new \Exception('Неверный email или пароль');
        }
        
        if ($client->getClientStatus() == 'new') {
            throw new \Exception('Ваша регистрация ожидает подтверждения');
        }
        if ($client->getClientStatus() == 'reject') {
            throw new \Exception('К сожалению, вам отказано в регистрации');
        }
        if (strcmp($client->getClientPassword(), $client_password)) {
            throw new \Exception('Неверный email или пароль');
        }

        return $client;
    }

    /**
     * Установка пользовательских кук
     */
    public static function setClientCookie($client)
    {
        cookie::setData(
            'client', array(
            $client->getClientEmail(),
            $client->getClientPassword(),
            ), time() + 60 * 60 * 24 * 30, '/'
        );
    }

    /**
     * Очистка пользовательских кук
     */
    public static function clearClientCookie()
    {
        cookie::setData(
            'client', null, time() - 60 * 60 * 24, '/'
        );
    }
    
    /**
     * Получение товара
     */
    public function getProduct($id)
    {
        try {
            $product = Model::factory('product')->get($id);
        } catch (\AlarmException $e) {
            System::notFound();
        }
        if (!$product->getProductActive()) {
            System::notFound();
        }
        return $product;
    }

    /**
     * Отключаем кеширование
     */
    protected function getCacheKey()
    {
        return false;
    }
}
