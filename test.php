<?php 
die("SDss");
use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$UserFactory = $objectManager->get('\Magento\User\Model\UserFactory');

    try{
        $adminInfo = [
        'username'  => 'staging',
        'firstname' => 'magento',
        'lastname'    => 'magento',
        'email'     => 'magento@staging.com',
        'password'  =>'staging@123',       
        'interface_locale' => 'en_US',
        'is_active' => 1
    ];

    $userModel = $UserFactory->create();
    $userModel->setData($adminInfo);
    $userModel->setRoleId(1);
    $userModel->save(); 

    } catch (\Exception $ex) {
        echo $ex->getMessage();
         exit;
    }
    echo "User is sucessfully created!"
?>