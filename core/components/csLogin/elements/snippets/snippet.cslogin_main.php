<?php 

$params['tpl_login_user_no_active'] = $modx->getOption('tpl_login_user_no_active',$scriptProperties,'login_user_no_active');
$params['tpl_login_user_active'] = $modx->getOption('tpl_login_user_active',$scriptProperties,'login_user_active');
$params['tpl_registration_mail'] = $modx->getOption('tpl_registration_mail',$scriptProperties,'login_registration_mail');
$params['validated_fields'] = 'fullname,email,username,password,password_retry';
$params['mail_from'] = $modx->getOption('cslogin.mail_from');
$params['mail_from_name'] = $modx->getOption('cslogin.mail_from_name');
$params['mail_subject_registration'] = $modx->getOption('cslogin.mail_subject_registration');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'user_login') {
    $params['username'] = isset($_POST['username']) ?  $_POST['username'] : '';
    $params['password'] = isset($_POST['password']) ?  $_POST['password'] : '';
    $params['rememberme'] = isset($_POST['rememberme']) ?  $_POST['rememberme'] : '0';
    $data = array(
        'username' => $params['username']
        ,'password' => $params['password']
        ,'rememberme' => $params['rememberme']
        ,'login_context' => 'web'
    );    
    $processor_response = $modx->runProcessor('/security/login', $data);
    if ($processor_response->isError()) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'login error. Username: '.$username.', Message: '.$processor_response->getMessage());
    }
    else {
        $processor_response->response['message'] = 'Вход в систему успешно выполнен';
    }
    
    
    $response = $processor_response->response;

    echo json_encode($response);
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'user_registration') {
    
    $response = array ();
    
    $params['username'] = isset($_POST['username']) ?  $_POST['username'] : '';
    $params['password'] = isset($_POST['password']) ?  $_POST['password'] : '';
    $params['password_retry'] = isset($_POST['password_retry']) ?  $_POST['password_retry'] : '';
    $params['email'] = isset($_POST['email']) ?  $_POST['email'] : '';
    $params['fullname'] = isset($_POST['fullname']) ?  $_POST['fullname'] : '';
    $params['phone'] = isset($_POST['phone']) ?  $_POST['phone'] : '';
    $params['mobilephone'] = isset($_POST['mobilephone']) ?  $_POST['mobilephone'] : '';
    $params['country'] = isset($_POST['country']) ?  $_POST['country'] : '';
    $params['zip'] = isset($_POST['zip']) ?  $_POST['zip'] : '';
    $params['state'] = isset($_POST['state']) ?  $_POST['state'] : '';
    $params['city'] = isset($_POST['city']) ?  $_POST['city'] : '';
    $params['address'] = isset($_POST['address']) ?  $_POST['address'] : '';
    $params['password_notify_method'] = isset($_POST['password_notify_method']) ?  $_POST['password_notify_method'] : 's';
    

    $data = array(
        'username' => $params['username']
        ,'email' => $params['email']
        ,'fullname' => $params['fullname']
        ,'phone' => $params['phone']
        ,'mobilephone' => $params['mobilephone']
        ,'country' => $params['country']
        ,'zip' => $params['zip']
        ,'state' => $params['state']
        ,'city' => $params['city']
        ,'address' => $params['address']
        ,'login_context' => 'web'
        ,'active' => true
        ,'blocked' => false
        ,'newpassword' => TRUE
        ,'passwordnotifymethod' => $params['password_notify_method']
        ,'password' => $params['password']
        ,'password_retry' => $params['password_retry']
    );    
  
    $validated_fields = explode(',',$params['validated_fields']);
    $errors = array();
	foreach ($validated_fields as $v) {
        if ($v=='fullname') {
            if (empty($data[$v])) {
    			$errors[] = array(
                    '0' => $v
                    ,'1' => 'Введите корректное имя');
		    }
            else if (strlen($data[$v]) < 4) {
                $errors[] = array(
                    '0' => $v
                    ,'1' => 'Имя не может быть короче 4-х символов');
		    }
            
        }
        else if ($v=='username') {
            if (empty($data[$v])) {
    		    $errors[] = array(
                    '0' => $v
                    ,'1' => 'Введите корректное имя');
		    }
            else {
                $count = $modx->getCount('modUser', array ('username' => $data['username']));
                if ($count > 0){
                    $modx->log(modX::LOG_LEVEL_ERROR, 'User error. Username: '.$data['username'].', Message: Пользователь с таким именем уже существует');
            	    $errors[] = array(
                    '0' => $v
                    ,'1' => 'Пользователь с таким именем уже существует');
		        }   
            }
        }
        else if ($v=='password') {
            if (empty($data[$v])) {
        	    $errors[] = array(
                    '0' => $v
                    ,'1' => 'Введите пароль не менее 6-и символов');
		    }
            else if (strlen($data[$v]) < 6) {
                $errors[] = array(
                    '0' => $v
                    ,'1' => 'Пароль не может быть короче 6-и символов');
		    }
        }
        else if ($v=='password_retry') {
            if ($data['password_retry'] != $data['password']) {
                $errors[] = array(
                    '0' => $v
                    ,'1' => 'Пароли не совпадают');
		    }
        }
		else {
            if (empty($data[$v])) {
			    $errors[] = array(
                    '0' => $v
                    ,'1' => 'Заполните требуемые поля');
		    }
	    }
    
	}
	if (!empty($errors)) {
		$response = array(
            'success' => false
            ,'message' => 'Для продолжения регистрации вам необходимо исправить выделенные ошибки'
            ,'data' => $errors
        );
        echo json_encode($response);
        exit;
	}  
  
    $user = $modx->newObject('modUser');

    $user->set('username', $data['username']);
    $user->set('password', $data['password']);

    $user->save();

    $profile = $modx->newObject('modUserProfile');

    $profile->fromArray($data);
    $user->addOne($profile);
    $profile->save();
    $user->save();
    
    $groupsList = array('users');
    $groups = array();
    
    foreach($groupsList as $groupName){
        $group = $modx->getObject('modUserGroup', array('name' => $groupName));

        $groupMember = $modx->newObject('modUserGroupMember');
        $groupMember->set('user_group', $group->get('id'));
        $groupMember->set('role', 1); 
        $groups[] = $groupMember;
    }

    $user->addMany($groups);
    $user->save();
    
    $message = $modx->getChunk($params['tpl_registration_mail'], $data);
 
    $modx->getService('mail', 'mail.modPHPMailer');
    $modx->mail->set(modMail::MAIL_BODY,$message);
    $modx->mail->set(modMail::MAIL_FROM,$params['mail_from']);
    $modx->mail->set(modMail::MAIL_FROM_NAME,$params['mail_from_name']);
    $modx->mail->set(modMail::MAIL_SUBJECT,$params['mail_subject_registration']);
    $modx->mail->address('to',$data['email']);
    
    $modx->mail->setHTML(true);
    if (!$modx->mail->send()) {
       $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
       $response = array(
        'success' => false
        ,'message' => 'Ошибка! не удалось послать сообщение на ваш адрес'
        ,'data' => $data
    );

    echo json_encode($response);
    exit;
    }
    $modx->mail->reset();
    
    $response = array(
        'success' => true
        ,'message' => '<p>Новый пользователь успешно создан</p>
        <p>На почтовый адрес: '.$data['email'].' послана информация об успешной регистрации</p>'
        ,'data' => $data
    );
    echo json_encode($response);
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'user_logout') {
    $processor_response = $modx->runProcessor('/security/logout');
    if ($processor_response->isError()) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Logout error. Username: '.$modx->user->get('username').', uid: '.$modx->user->get('id').'. Message: '.$response->getMessage());
    }
    else {
        $processor_response->response['message'] = 'Выход из системы успешно выполнен';
    }
    $response = $processor_response->response;

    echo json_encode($response);
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'user_status') {
    if ($modx->user->isAuthenticated()) {
        $user = $modx->user;
        $profile = $modx->user->Profile;
        $name = $user->get('username');
        $elementArray = array(
            'name' => $name);
        $data = $modx->getChunk($params['tpl_login_user_active'],$elementArray);
    }
    else {
        $elementArray = array();
        $data = $modx->getChunk($params['tpl_login_user_no_active'],$elementArray);
    }
    
    $response = array(
        'success' => true
        ,'message' => ''
        ,'data' => $data
    );

    echo json_encode($response);
    exit;
    
}

$login_assets_url = 'assets/'.'components/login/';
$modx->regClientScript($login_assets_url.'js/login.js');
$modx->regClientScript
('
<script type="text/javascript">
$(document).ready(function() {
login.config = {
    js_url: "'.$login_assets_url.'js/"
};
login.initialize();
});
</script>
');
    if ($modx->user->isAuthenticated()) {
        $user = $modx->user;
        $profile = $modx->user->Profile;
        $name = $user->get('username');
        $elementArray = array(
            'name' => $name);
        return $modx->getChunk($params['tpl_login_user_active'],$elementArray);
    }
    else {
        $elementArray = array();
        return $modx->getChunk($params['tpl_login_user_no_active'],$elementArray);
    }