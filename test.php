<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "BaleAPIv8.php";

$token="556882979:2nXIff3RTr8qeH2y5G3XHweEkfA4Z24AhPYGZPgD";
$bot= new Balebot($token);


if ($bot->getUpdate()){
    $bot->sendText("1261102725",$bot->message->text);

    if ($bot->preCheckoutQuery){
        $bot->preCheckoutQuery->answer();
    }

    if (is_array($bot->message->botCommands)){
        $bot->sendText("1261102725","op");
    }
    foreach ($bot->message->botCommands as $item){
        //$bot->sendText("1261102725", strval($item['value']));
        $bot->sendText("1261102725", implode('*',$item));
    }

    if($bot->message->invoice->successfulPayment){
        $bot->sendText("1261102725",$bot->preCheckoutQuery->inquireTransaction()->perStatus);
    }
}else{
    $bot->sendText("1261102725","hello");

    $msg = new MessageService('admin');
    $key=new Keyboard(null,$bot);

    //$bot->sendText("1261102725",$msg->get('back'));
    //$bot->sendText("1261102725",$msg->get('welcome',['name'=>"امیررضا"]),$key->markupKeyboard($msg->keyboard('welcome')));
    //$bot->sendText("1261102725",$msg->getState('START',['name'=>"امیررضا"]));
    //$bot->sendText("1261102725",json_encode($msg->positions));
    //$bot->sendText("1261102725",$msg->getPosition(12),$key->markupInlineKeyboard($msg->inlineKeyboard($msg->positions[12])));
    $bot->sendText("1261102725",$msg->get('password'));

    //echo $bot->askReview("1261102725");
    //$otpData=array('username'=>'ZFYPDKfclQtcAlvxizKQRJURdWoIzFGm','password'=>'oOLUkXKkUzszrjZzmesTAFbHWtvRIqlr');

    //$otp=new BaleOTP($otpData);

    //$otp->getAuthToken();
    //if ($otp->sendOtp("989350504026",254690)['success']){echo "done";}

//    $key=new Keyboard(null,$bot);
////    $keyb=array(array("سلام","خوبی"),array(array("salam","webApp","https://amirrezafarshchi.ir/server/miniapp")));
//    $keyb=[
//        ["سلام","خوبی"],
//        [["salam","webApp","https://amirrezafarshchi.ir/server/miniapp"]]
//    ];
//    $keyb=array(array("سلام","11","خوبی","22"),"radif1",array("salam",array("https://amirrezafarshchi.ir/server/miniapp","webApp")));
//    $keyb=[
//        [
//            "سلام", "11",
//            "خوبی", "22"
//        ], "radif1",
//        'salam', ["https://amirrezafarshchi.ir/server/miniapp", "webApp"]
//    ];
//    $keyboard=$key->markupInlineKeyboard($keyb);
//
//    $bot->sendText("1261102725",json_encode($keyboard));
////
//    $e=$bot->sendText("1261102725","asdf dasd fasdf afd ",$keyboard)['Description'];
//    $bot->sendText("1261102725",$e." 55");
    //$bot->sendText("1261102725",json_encode($e->reply_markup));
    //$bot->sendText("1261102725",$e->inlineKeyboardButtons[0][0]->text);

    //$e=$bot->Invoice("1261102725","TEST","This is for test","55far",array("کالای دوم",500000,"کالای اول",20000));
}

//$bot->sendText("1261102725",$e['Code']);
//$bot->sendText("1261102725",$e['Description']);
//$bot->sendText("1261102725",$e['check']);
?>