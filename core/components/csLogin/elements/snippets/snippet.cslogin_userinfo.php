<?php

$params['tpl_main'] = $modx->getOption('tpl_main',$scriptProperties,'tpl_cslogin_userinfo_main');
$params['tpl_error'] = $modx->getOption('tpl_error',$scriptProperties,'tpl_cslogin_error');
$params['tpl_registration_update_mail'] = $modx->getOption('tpl_registration_update_mail',$scriptProperties,'login_registration_update_mail');
$params['validated_fields'] = 'fullname,email';//,password';
$params['mail_from'] = $modx->getOption('cslogin.mail_from');
$params['mail_from_name'] = $modx->getOption('cslogin.mail_from_name');
$params['mail_subject_registration'] = $modx->getOption('cslogin.mail_subject_registration');


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'user_update') {
    
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
    if (!$modx->user->isAuthenticated()) {
    	$response = array(
            'success' => false
            ,'message' => 'Ошибка! Необходимо войти в систему'
            ,'data' => array()
        );
        echo json_encode($response);
        exit;
    }
    $user = $modx->user;
    $profile = $user->getOne('Profile');
    

    $profile->fromArray($data);
    $profile->save();
    
    $response = array(
        'success' => true
        ,'message' => 'Данные успешно обновлены'
        ,'data' => $data
    );
    echo json_encode($response);
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'user_update_password') {
    
    $response = array ();
    
    $params['password'] = isset($_POST['password']) ?  $_POST['password'] : '';
    $params['password_retry'] = isset($_POST['password_retry']) ?  $_POST['password_retry'] : '';
    $params['password_notify_method'] = isset($_POST['password_notify_method']) ?  $_POST['password_notify_method'] : 's';
    

    $data = array(
        'newpassword' => TRUE
        ,'passwordnotifymethod' => $params['password_notify_method']
        ,'password' => $params['password']
        ,'password_retry' => $params['password_retry']
    );    
  
    $validated_fields = explode(',','password,password_retry');
    $errors = array();
    foreach ($validated_fields as $v) {
         if ($v=='password') {
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
            ,'message' => 'Вам необходимо исправить выделенные ошибки'
            ,'data' => $errors
        );
        echo json_encode($response);
        exit;
	}
    if (!$modx->user->isAuthenticated()) {
    	$response = array(
            'success' => false
            ,'message' => 'Ошибка! Необходимо войти в систему'
            ,'data' => array()
        );
        echo json_encode($response);
        exit;
    }
    $user = $modx->user;
    $profile = $user->getOne('Profile');
    
    $user->set('password', $data['password']);
    $user->save();
      
    $data['username'] = $user->get('username');  
    $data = array_merge($data, $profile->toArray());

    
    $message = $modx->getChunk($params['tpl_registration_update_mail'], $data);
 
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
        ,'message' => '<p>Ваш пароль успешно обновлен</p>
        <p>На почтовый адрес: '.$data['email'].' послана информация об успешной операции</p>'
        ,'data' => $data
    );
    echo json_encode($response);
    exit;

}

if (!$modx->user->isAuthenticated()) {
    $elementArray = array(
        'message' => 'Ошибка! Необходимо войти в систему'
    );
    
    return $modx->getChunk($params['tpl_error'],$elementArray);
} else {
    
    $user = $modx->user;
    $profile = $user->getOne('Profile');

    $elementArray = $profile->toArray();
    $elementArray['username'] = $user->get('username');
    $elementArray['password'] = $user->get('password');
    
    return $modx->getChunk($params['tpl_main'],$elementArray);
}