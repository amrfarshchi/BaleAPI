<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

/**
 * Bale.ai Bot V8 Class .
 *
 * @farshchi Amirreza Farshchi <arfiran@gmail.com>
 * first editation: 2023/10/20
 * 8 editaton: 2025/12/10
 */

#region Metods
class User{
    public $id;
    public $username;
    public $first_name;
    //public $last_name;
    public $is_bot;
    //public $Data;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;
    public function __construct($user, $Bot=Null)
    {
        $this->id = array_key_exists('id', $user) ? $user['id'] : null;
        $this->username = array_key_exists('username', $user) ? $user['username'] : null;
        $this->first_name = array_key_exists('first_name', $user) ? $user['first_name'] : null;
        //$this->last_name=$user['id'];
        $this->is_bot = array_key_exists('is_bot', $user) ? $user['is_bot'] : null;
        /*$this->Data=array(
                    'Id' => $this->id,
                    'Un' => $this->username,
                    'Fn' => $this->first_name,
                    'Ib' => $this->is_bot
                );*/

        $this->Bot=$Bot;
    }

    public function status($chat_id)
    {
        if ($this->Bot) {
            return $this->Bot->getChatMember($chat_id, $this->id)->status;
        }else{return Null;}
    }

    public function invite ($chat_id)
    {
        if ($this->Bot) {
            return $this->Bot->inviteUser($chat_id,$this->id);
        }else{return Null;}
    }

    public function banMember ($chat_id)
    {
        if ($this->Bot) {
            return $this->Bot->banChatMember($chat_id,$this->id);
        }else{return Null;}
    }

    public function unbanMember ($chat_id)
    {
        if ($this->Bot) {
            return $this->Bot->unbanChatMember($chat_id,$this->id);
        }else{return Null;}
    }

    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }
}

class Chat{
    public $id;
    public $type; //نوع گفتگو که می‌تواند یکی از موارد private، group یا channel باشد.
    public $title;
    public $username;
    public $first_name;
    //public $last_name;
    /** @var ChatPhoto $photo  */
    public $photo;
    public $bio;
    public $description;
    public $link;
    public $linkedGroup;  //چت آیدی گروه کامنت ها
    //public $Data;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;
    public function __construct($chat, $Bot=Null)
    {
        $this->id = array_key_exists('id', $chat) ? $chat['id'] : null;
        $this->type = array_key_exists('type', $chat) ? $chat['type'] : null;
        $this->title = array_key_exists('title', $chat) ? $chat['title'] : null;
        $this->username = array_key_exists('username', $chat) ? $chat['username'] : null;
        $this->first_name = array_key_exists('first_name', $chat) ? $chat['first_name'] : null;
        $this->bio = array_key_exists('bio', $chat) ? $chat['bio'] : null;
        $this->description = array_key_exists('description', $chat) ? $chat['description'] : null;
        $this->link = array_key_exists('invite_link', $chat) ? $chat['invite_link'] : null;
        $this->linkedGroup = array_key_exists('linked_chat_id', $chat) ? $chat['linked_chat_id'] : null;
        //$this->last_name=$chat['id'];
        $this->photo= (array_key_exists('photo',$chat) && is_array($chat['photo'])) ? new ChatPhoto($chat['photo']) : null;
        /*$this->Data=array(
                    'Id' => $this->id,
                    'Ty' => $this->type,
                    'Ti' => $this->title,
                    'Un' => $this->username,
                    'Fn' => $this->first_name,
                    'Photo' => $this->photo,
                    'Link' => $this->link

              );*/

        $this->Bot=$Bot;
    }

    public function admins()
    {
        if ($this->Bot) {
            return $this->Bot->getChatAdmins($this->id);
        }else{return Null;}
    }

    public function memberCount ()
    {
        if ($this->Bot) {
            return $this->Bot->getChatMembersCount($this->id);
        }else{return Null;}
    }

    public function leave ()
    {
        if ($this->Bot) {
            return $this->Bot->leaveChat($this->id);
        }else{return Null;}
    }

    public function getChatByUsername ($username=null)
    {
        if (!$username){
            $username=$this->username;
        }else{
            $username=str_replace("@","",$username);
            $this->username=$username;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ble.ir/".$username,
            CURLOPT_RETURNTRANSFER => true,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        preg_match('/({.*})/', $response, $matches);
        $jsonInfo = (isset($matches[0])) ? $matches[0] : null;
        $infoArray = json_decode($jsonInfo, true);

        $pageProps = (is_array($infoArray) && array_key_exists('props', $infoArray) && is_array($infoArray['props']) && array_key_exists('pageProps', $infoArray['props']))
            ? $infoArray['props']['pageProps']
            : null;

        $peer = (is_array($pageProps) && array_key_exists('peer', $pageProps))
            ? $pageProps['peer']
            : null;

        $peerType = (is_array($peer) && array_key_exists('type', $peer)) ? $peer['type'] : null;

        if ($peerType == 1) {
            $this->id = (is_array($peer) && array_key_exists('id', $peer)) ? $peer['id'] : null;
            $user = (is_array($pageProps) && array_key_exists('user', $pageProps)) ? $pageProps['user'] : null;
            $this->first_name = (is_array($user) && array_key_exists('title', $user)) ? $user['title'] : null;
            $this->type='private';
        }else{
            $this->id = (is_array($peer) && array_key_exists('id', $peer)) ? $peer['id'] : null;
            $group = (is_array($pageProps) && array_key_exists('group', $pageProps)) ? $pageProps['group'] : null;
            $this->title = (is_array($group) && array_key_exists('title', $group)) ? $group['title'] : null;
            $this->type='channel';
        }
        return $this;
    }

    public function banMember ($user_id)
    {
        if ($this->Bot) {
            return $this->Bot->banChatMember($this->id,$user_id);
        }else{return Null;}
    }

    public function unbanMember ($user_id)
    {
        if ($this->Bot) {
            return $this->Bot->unbanChatMember($this->id,$user_id);
        }else{return Null;}
    }

    public function pinMessage ($message_id)
    {
        if ($this->Bot) {
            return $this->Bot->pinChatMessage($this->id,$message_id);
        }else{return Null;}
    }

    public function unPinMessage ($message_id)
    {
        if ($this->Bot) {
            return $this->Bot->unPinChatMessage($this->id,$message_id);
        }else{return Null;}
    }

    public function unpinAllMessages ()
    {
        if ($this->Bot) {
            return $this->Bot->unpinAllMessages($this->id);
        }else{return Null;}
    }

    public function setTitle($title){
        if ($this->Bot) {
            return $this->Bot->setChatTitle($this->id,$title);
        }else{return Null;}
    }

    public function deletePhoto ($photo)
    {
        if ($this->Bot) {
            return $this->Bot->deleteChatPhoto($this->id,$photo);
        }else{return Null;}
    }

    public function setDescription ($photo)
    {
        if ($this->Bot) {
            return $this->Bot->setChatDescription($this->id,$photo);
        }else{return Null;}
    }

    public function createInviteLink ($photo)
    {
        if ($this->Bot) {
            return $this->Bot->createChatInviteLink($this->id,$photo);
        }else{return Null;}
    }

    public function exportInviteLink ($photo)
    {
        if ($this->Bot) {
            return $this->Bot->exportChatInviteLink($this->id,$photo);
        }else{return Null;}
    }

    public function revokeInviteLink ($photo)
    {
        if ($this->Bot) {
            return $this->Bot->revokeChatInviteLink($this->id,$photo);
        }else{return Null;}
    }



    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }
}

class ChatMember extends User{
    public $status;

    public $permissions;
    public function __construct($user, $Bot=Null)
    {
        parent::__construct(array_key_exists('user', $user) ? $user['user'] : [],$Bot);

        $this->status = array_key_exists('status', $user) ? $user['status'] : null;
        //creator, administrator, member, restricted

        if (array_key_exists('user', $user)) { unset($user['user']); }
        if (array_key_exists('status', $user)) { unset($user['status']); }
        $this->permissions=$user;
    }

    public function can($permission)
    {
        if ($this->status=="creator"){
            return true;
        }else{
            $permission="can_".$permission;
            return $this->permissions[$permission];
        }


        /** creator:
         * همه دسترسی ها را دارد
         *
         * member:
         * هیچ دسترسی یا محدودیت خاصی ندارد
         *
         * restricted: (محدود شده ها)
         * is_member : true
         * can_send_messages": true,
         * can_be_edited": true,
         * can_send_audios: true,
         * can_send_documents: true,
         * can_send_photos: true,
         * can_send_videos: true,
         * can_change_info: true,
         * can_invite_users: true,
         * can_pin_messages: true,
         *
         *  administrator:
         * can_delete_messages: true
         * can_manage_video_chats: true
         * can_restrict_members: true //بیرون کردن اعضا
         * can_promote_members: true //محدود کردن اعضا
         * can_change_info: true,
         * can_invite_users: true
         * can_post_stories: true,
         * can_post_messages: true,
         * can_edit_messages: true,
         * can_pin_messages: true
         */

    }
}

class File{
    public $id;
    public $type;
    public $size;
    public $name;
    public $title;
    public $mime_type;
    public $W;
    public $H;
    public $duration;
    public $thumbnail;
    public $path;
    //public $Data;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;
    public function __construct($document,$type, $Bot=Null)
    {
        $this->id = array_key_exists('file_id', $document) ? $document['file_id'] : null;
        $this->size = array_key_exists('file_size', $document) ? intval($document['file_size']) : null;
        /*$this->Data=array(
                    'Id' => $this->id,
                    'FS' => $this->size,
                );*/
        $this->newFile($document,$type);
        $this->Bot=$Bot;
    }

    public function newFile($document,$type)
    {
        $this->type=$type;//$this->Data['Type']=$this->type;
        if (array_key_exists('file_name',$document) and !is_null($document['file_name'])) {
            $this->name = $document['file_name'];
//$this->Data['FN'] = $this->name;
        }
        if (array_key_exists('mime_type',$document) and !is_null($document['mime_type'])) {
            $this->mime_type = $document['mime_type'];
//$this->Data['FMT'] = $this->mime_type;
        }
        if (array_key_exists('title',$document) and !is_null($document['title'])){
            $this->title=$document['title'];
//$this->Data['FT']=$this->title;
        }
        if (array_key_exists('duration',$document) and !is_null($document['duration'])) {
            $this->duration = $document['duration'];
//$this->Data['FD'] = $this->duration;
        }
        if (array_key_exists('width',$document) and !is_null($document['width'])) {
            $this->W = $document['width'];
//$this->Data['FW'] = $this->W;
        }
        if (array_key_exists('height',$document) and !is_null($document['height'])){
            $this->H=$document['height'];
//$this->Data['FH']=$this->H;
        }
        if (array_key_exists('thumbnail',$document) and is_array($document['thumbnail'])){
            $this->thumbnail=new File($document['thumbnail'],'photo');
//$this->Data['FH']=$this->H;
        }
    }
    public function filePath ()
    {
        if ($this->Bot) {
            $arr = array('file_id' => $this->id);
            $result = json_decode($this->Bot->sendrequest('getFile', $arr), true);
            if ($result !== null && is_array($result)) {
                if (array_key_exists('ok', $result) && $result['ok']) {
                    $this->path = (array_key_exists('result', $result) && is_array($result['result']) && array_key_exists('file_path', $result['result'])) ? $result['result']['file_path'] : null;
                    $return = $this->path;
                } else {
                    $this->error = array(
                        'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                        'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                    );
                    $return = $this->error;
                }
            } else {
                $return = null;
            }
            return $return;
        }else{return Null;}
    }

    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }
}

class Contact{
    public $phone;
    public $first_name;
    //public $last_name;
    public $id;
    //public $Data;
    public function __construct($contact)
    {
        $this->phone = array_key_exists('phone_number', $contact) ? $contact['phone_number'] : null;
        $this->first_name = array_key_exists('first_name', $contact) ? $contact['first_name'] : null;
        //$this->last_name=$contact['last_name'];
        $this->id = array_key_exists('user_id', $contact) ? $contact['user_id'] : null;
        /*$this->Data=array(
                    'Ph' => $this->phone,
                    'Fn' => $this->first_name,
                    //'Ln' => $this->last_name,
                    'Id' => $this->id,
                );*/
    }
}

class Location{
    public $tool;
    public $arz;
    //public $Data;

    public function __construct($location)
    {
        $this->tool = array_key_exists('longitude', $location) ? $location['longitude'] : null;
        $this->arz = array_key_exists('latitude', $location) ? $location['latitude'] : null;
        /*$this->Data=array(
                    'Tool' => $this->tool,
                    'Arz' => $this->arz,
                );*/
    }
}

class ChatPhoto{
    public $smallFile_id; //شناسه فایل برای تصویر کوچک گفتگو (۱۶۰×۱۶۰). این شناسه را فقط برای دانلود تصویر و فقط تا زمانی می‌توان استفاده کرد که تصویر مورد نظر تغییر نکرده باشد
    public $smallFile_uniqueId; //شناسه منحصربه‌فرد برای تصویر کوچک گفتگو (۱۶۰×۱۶۰)، که در طول زمان و برای بازوهای مختلف یکسان است. نمی‌توان آن را برای دانلود یا استفاده مجدد از فایل به کار برد.
    //public $last_name;
    public $bigFile_id; //شناسه فایل برای تصویر بزرگ گفتگو (۶۴۰×۶۴۰). این شناسه را می‌توان برای دانلود تصویر و فقط تا زمانی استفاده کرد که تصویر تغییر نکرده باشد.
    public $bigFile_uniqueId; //شناسه منحصربه‌فرد برای تصویر بزرگ گفتگو (۶۴۰×۶۴۰)، که در طول زمان و برای بازوهای مختلف یکسان است. نمی‌توان آن را برای دانلود یا استفاده مجدد از فایل به کار برد.

    //public $Data;
    public function __construct($photo)
    {
        $this->smallFile_id = array_key_exists('small_file_id', $photo) ? $photo['small_file_id'] : null;
        $this->smallFile_uniqueId = array_key_exists('small_file_unique_id', $photo) ? $photo['small_file_unique_id'] : null;
        $this->bigFile_id = array_key_exists('big_file_id', $photo) ? $photo['big_file_id'] : null;
        $this->bigFile_uniqueId = array_key_exists('big_file_unique_id', $photo) ? $photo['big_file_unique_id'] : null;
    }
}

class Invoice{
    public $title;
    public $description;
    public $total_amount;
    public $currency;
    public $payload; //شناسه ای که موقع ارسال پرداخت تنظیم میشه تا معلوم بشه کدوم مورد پرداخت شده
    public $paymentCharge_id; //شناسه یکتای پرداخت. در پرداخت‌های کیف‌پولی معادل فیلد id در PreCheckoutQuery است
    public $providerPaymentCharge_id; //شماره پیگیری تراکنش در صورتی که پرداخت از طریق کیف پول بله بوده باشد.
    public $successfulPayment;
    //public $Data;
    public function __construct($invoice,$successful=false)
    {
        $this->title = array_key_exists('title', $invoice) ? $invoice['title'] : null;
        $this->description = array_key_exists('description', $invoice) ? $invoice['description'] : null;
        $this->total_amount = array_key_exists('total_amount', $invoice) ? $invoice['total_amount'] : null;
        $this->currency = array_key_exists('currency', $invoice) ? $invoice['currency'] : null;
        $this->payload = array_key_exists('invoice_payload', $invoice) ? $invoice['invoice_payload'] : null;
        $this->paymentCharge_id = array_key_exists('telegram_payment_charge_id', $invoice) ? $invoice['telegram_payment_charge_id'] : null;
        $this->providerPaymentCharge_id = array_key_exists('provider_payment_charge_id', $invoice) ? $invoice['provider_payment_charge_id'] : null;
        $this->successfulPayment =$successful;
        /*$this->Data=array(
                    'Ti' => $this->title,
                    'De' => $this->description,
                    'Sp' => $this->start_parameter,
                    'Vahed' => $this->currency,
                    'Pr' => $this->total_amount,
                    'Sh' => $this->shenase,
                );*/
    }
}

class Transaction{
    public $id;
    public $status;
    public $perStatus;
    public $userID;
    public $amount; //قیمت به ریال است
    public $createdAt; //زمان ایجاد تراکنش (به‌صورت Unix timestamp یا فرمت مشابه).

    //public $Data;
    public function __construct($transaction)
    {
        $this->id = array_key_exists('id', $transaction) ? $transaction['id'] : null;
        $this->status = array_key_exists('status', $transaction) ? $transaction['status'] : null;
        $this->userID = array_key_exists('userID', $transaction) ? $transaction['userID'] : null;
        $this->amount = array_key_exists('amount', $transaction) ? $transaction['amount'] : null;
        $this->createdAt = array_key_exists('createdAt', $transaction) ? $transaction['createdAt'] : null;
        /*$this->Data=array(
                    'Ti' => $this->title,
                    'De' => $this->description,
                    'Sp' => $this->start_parameter,
                    'Vahed' => $this->currency,
                    'Pr' => $this->total_amount,
                    'Sh' => $this->shenase,
                );*/

        switch ($this->status){
            case "pending":
                $this->perStatus="تراکنش در حال پردازش است.";
                break;
            case "paid":
                $this->perStatus="تراکنش با موفقیت انجام شده است.";
                break;
            case "failed":
                $this->perStatus="تراکنش ناموفق بوده است.";
                break;
            case "rejected":
                $this->perStatus="تراکنش از سمت بازو رد شده است.";
                break;
        }
    }
}

class Keyboard{
    public $keyboardMarkup;
    public $keyboard;
    public $inlineKeyboardMarkup;
    public $inlineKeyboard;
    public $text;
    public $url;
    public $callback_data;
    public $copy_text;
    public $request_contact;
    public $request_location;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;

    public function __construct($keyboards=null, $Bot=Null)
    {
        if (is_array($keyboards)) {
            $this->inlineKeyboard =$keyboards;
            $this->text = array_key_exists('text', $keyboards) ? $keyboards['text'] : null;
            $this->url = array_key_exists('url', $keyboards) ? $keyboards['url'] : null;
            $this->callback_data = array_key_exists('callback_data', $keyboards) ? $keyboards['callback_data'] : null;
            $this->copy_text = array_key_exists('copy_text', $keyboards) ? $keyboards['copy_text']['text'] : null;
            $this->request_contact = array_key_exists('request_contact', $keyboards) ? $keyboards['request_contact'] : null;
            $this->request_location = array_key_exists('request_location', $keyboards) ? $keyboards['request_location'] : null;
        }

        if($Bot){
            $this->Bot=$Bot;
        }
    }

    public function markupKeyboard($buttons)
    {
        $option=[];
        foreach ($buttons as $radif){
            if (is_array($radif)){
                foreach ($radif as $button){
                    $option[] = $this->buildKeyboardButton($button);
                }
            }else {
                $option[] = $this->buildKeyboardButton($radif);
            }
            $this->keyboard[]=$option;
            $option=[];
        }
        $this->keyboardMarkup=array('keyboard' => $this->keyboard);
        return $this->keyboardMarkup;
    }

    public function markupInlineKeyboard($buttons = null)
    {
        if ($buttons) {
            $option = [];
            $option2 = [];
            $tedad = count($buttons);
            for ($i = 0; $i <= $tedad - 1; $i++) {
                if (is_array($buttons[$i])) {
                    for ($j = 0; $j <= count($buttons[$i]) - 1; $j++) {
                        if ($j % 2 == 0) {
                            $button_name = $buttons[$i][$j];
                        } else {
                            $option2[] = $this->buildInlineKeyboardButton($button_name, $buttons[$i][$j]);
                        }
                    }
                    $i++;
                    $radif = ($i + 1) / 2 - 1;
                    $option[$radif] = $option2;
                    $option2 = array();
                } else {
                    if ($i % 2 == 0) {
                        $button_name = $buttons[$i];
                    } else {
                        $radif = ($i + 1) / 2 - 1;
                        $option[$radif] = array($this->buildInlineKeyboardButton($button_name, $buttons[$i]));
                    }
                }
            }

            $this->inlineKeyboard = $option;
        }

        $this->inlineKeyboardMarkup=array('inline_keyboard' =>  $this->inlineKeyboard);
        return $this->inlineKeyboardMarkup;
    }

    private function buildKeyboardButton($button)
    {
        if (!is_array($button)){
            $replyMarkup = [
                'text' => strval($button)
            ];
        }else{
            if ($button[1]=="contact"){
                $replyMarkup = [
                    'text'             => strval($button[0]),
                    'request_contact'  => true,
                    'request_location' => false
                ];
            }else if ($button[1]=="location"){
                $replyMarkup = [
                    'text'             => strval($button[0]),
                    'request_contact'  => false,
                    'request_location' => true
                ];
            }else if ($button[1]=="webApp"){
                if (!array_key_exists(2,$button)){
                    $username=$this->Bot->getMe()->username;
                    //$this->Bot->sendText("1261102725","https://ble.ir/$username?startapp");
                    $replyMarkup = [
                        'text'             => strval($button[0]),
                        'request_contact'  => false,
                        'request_location' => false,
                        'web_app' => array('url' => "https://ble.ir/$username?startapp")
                    ];
                }elseif (array_key_exists(3,$button)){
                    $username=$this->Bot->getMe()->username;
                    //$this->Bot->sendText("1261102725",$username."hdello");
                    $replyMarkup = [
                        'text'             => strval($button[0]),
                        'request_contact'  => false,
                        'request_location' => false,
                        'web_app' => array('url' => "https://ble.ir/$username?startapp=".$button[2])
                    ];
                }else{
                    $replyMarkup = [
                        'text'             => strval($button[0]),
                        'request_contact'  => false,
                        'request_location' => false,
                        'web_app' => array('url' => $button[2])
                    ];
                }

            }
        }
        return $replyMarkup;
    }

    public function buildInlineKeyboardButton($button_name,$button)
    {
        $replyMarkup = [
            'text' => $button_name,
        ];
        if (!is_array($button)){
            $replyMarkup['callback_data'] = $button;
        }else{
            if ($button[1]=="url"){
                $replyMarkup['url'] = $button[0];

            }else if ($button[1]=="copy"){
                $replyMarkup['copy_text'] = array('text' => $button[0]);

            }else if ($button[1]=="webApp"){
                $replyMarkup['web_app'] = array('url' => $button[0]);

            }
        }
        return $replyMarkup;
    }

    public function sansur($buttons, $words) {
        foreach ($buttons as $key => $value) {

            // اگر مقدار خودش آرایه است، بازگشتی صداش کن
            if (is_array($value)) {
                $buttons[$key] = $this->sansur($value, $words);
                continue;
            }

            // اگر مقدار در لیست کلمات ممنوعه بود، حذفش کن
            if (in_array($value, $words, true)) {
                unset($buttons[$key]);

                // در کد اصلی‌ات گفتی بعد از حذف یک مقدار، آیتم بعدی هم حذف بشه:
                if (isset($buttons[$key + 1])) {
                    unset($buttons[$key + 1]);
                }
            }
        }

        // reindex آرایه بعد از unset
        return array_values($buttons);
    }

    public function replyKeyboardRemove()
    {
        return array('remove_keyboard' => true);
    }

    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }

}

class Message{

    public $id;
    /** @var User $user */
    public $user;
    /** @var Chat $chat */
    public $chat;

    /** @var Chat $senderChat */
    public $senderChat; //زمانی مقداردهی می‌شود که پیام از طرف یک گفتگو ارسال شده و فرستندهٔ واقعی برای کاربران پنهان باشد (مانند پیام‌های کانال)؛ در این حالت برای حفظ سازگاری، فیلد from دارای یک مقدار ساختگی است.
    public $date;
    /** @var User $forwardFrom */
    public $forwardFrom;
    /** @var Chat $forwardFromChat */
    public $forwardFromChat;
    public $forwardChannel_Mid; //شناسه پیام اصلی در کانال، برای پیام‌های باز ارسال شده از کانال‌ها
    /** @var Message $reply_message */
    public $reply_message;
    public $text;
    public $caption;
    public $entities;
    public $captionEntities;
    public $mentions;
    public $botCommands;
    /** @var File $file */
    public $file;
    /** @var Contact $contact */
    public $contact;
    /** @var Location $location */
    public $location;
    /** @var Invoice $invoice */
    public $invoice;
    /** @var User $new_member */
    public $new_member;
    /** @var User $left_member */
    public $left_member;
    public $type;
    public $reply_markup;
    /** @var Keyboard[][] $inlineKeyboardButtons */
    public $inlineKeyboardButtons;
    public $webAppData;

    //public $Data;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;
    public function __construct($message, $Bot=Null)
    {
        $this->id = array_key_exists('message_id', $message) ? $message['message_id'] : null;
        $this->user = (array_key_exists('from', $message) && is_array($message['from'])) ? new User($message['from'], $Bot) : null;
        $this->chat = (array_key_exists('chat', $message) && is_array($message['chat'])) ? new Chat($message['chat'], $Bot) : null;
        $this->date=array(
            'Date' => array_key_exists('date', $message) ? $message['date'] : null,
            'FDate' => array_key_exists('forward_date', $message) ? $message['forward_date'] : null,
            'EDate' => array_key_exists('edit_date', $message) ? $message['edit_date'] : null,
        );
        $this->reply_message = array_key_exists('reply_to_message', $message) ? $message['reply_to_message'] : null;
        $this->text = array_key_exists('text', $message) ? $message['text'] : null;
        $this->caption = array_key_exists('caption', $message) ? $message['caption'] : null;

        if (array_key_exists('forward_from',$message)) {
            $this->forwardFrom = new User($message['forward_from'], $Bot);
//$this->Data['FU'] = $this->forward_from->Data;
        }
        if (array_key_exists('forward_from_chat',$message)) {
            $this->forwardFromChat = new Chat($message['forward_from_chat'], $Bot);
//$this->Data['FC'] = $this->forward_chat->Data;
        }
        if (array_key_exists('forward_from_message_id',$message)) {
            $this->forwardChannel_Mid = $message['forward_from_message_id'];
//$this->Data['FId'] = $this->forward_id->Data;
        }

        if (array_key_exists('photo',$message)){
            /*foreach ($message['photo'] as $item => $value){
                $this->file[$item]=new File($value,'photo', $Bot);
//$this->Data['F'][$item] = $this->file[$item]->Data;
            }*/
            $this->file = (is_array($message['photo']) && isset($message['photo'][0]) && is_array($message['photo'][0])) ? new File($message['photo'][0],'photo', $Bot) : null;
//$this->Data['F'][$item] = $this->file[$item]->Data;
            $this->type='photo';
        }else if (array_key_exists('video',$message) && is_array($message['video'])){
            $this->file=new File($message['video'],'video', $Bot);
//$this->Data['F'] = $this->file->Data;
            $this->type='video';
        }else if (array_key_exists('voice',$message) && is_array($message['voice'])){
            $this->file=new File($message['voice'],'voice', $Bot);
//$this->Data['F'] = $this->file->Data;
            $this->type='voice';
        }else if (array_key_exists('audio',$message) && is_array($message['audio'])){
            $this->file=new File($message['audio'],'audio', $Bot);
//$this->Data['F'] = $this->file->Data;
            $this->type='audio';
        }else if (array_key_exists('animation',$message) && is_array($message['animation'])){
            $this->file=new File($message['animation'],'animation', $Bot);
//$this->Data['F'] = $this->file->Data;
            $this->type='animation';
        }else if (array_key_exists('sticker',$message) && is_array($message['sticker'])){
            $this->file=new File($message['sticker'],'sticker', $Bot);
//$this->Data['F'] = $this->file->Data;
            $this->type='animation';
        }else if (array_key_exists('contact',$message) && is_array($message['contact'])){
            $this->contact=new Contact($message['contact']);
//$this->Data['Co'] = $this->contact->Data;
            $this->type='contact';
        }else if (array_key_exists('location',$message) && is_array($message['location'])){
            $this->location=new Location($message['location']);
//$this->Data['Lo'] = $this->location->Data;
            $this->type='location';
        }else if (array_key_exists('invoice',$message) && is_array($message['invoice'])){
            $this->invoice=new Invoice($message['invoice']);
//$this->Data['Iv'] = $this->invoice->Data;
            $this->type='invoice';
        }else if (array_key_exists('successful_payment',$message) && is_array($message['successful_payment'])){
            $this->invoice=new Invoice($message['successful_payment'],true);
//$this->Data['Lm'] = $this->invoice->Data;
            $this->type='successful_payment';
        }else if (array_key_exists('new_chat_members',$message)){
            if (is_array($message['new_chat_members'])) {
                foreach ($message['new_chat_members'] as $item => $value){
                    $this->new_member[$item]= is_array($value) ? new User($value, $Bot) : null;
                }
            }
            $this->type='new_chat_members';
        }else if (array_key_exists('left_chat_member',$message) && is_array($message['left_chat_member'])){
            $this->left_member=new User($message['left_chat_member'], $Bot);
            //$this->Data['Lm'] = $this->left_member->Data;
            $this->type='left_chat_member';
        }else if (array_key_exists('web_app_data',$message) && is_array($message['web_app_data'])) {
            $this->webAppData = $message['web_app_data']['data'];
            $this->type = 'webApp';
        }else{
            $this->type='text';
        }

        if (array_key_exists('document',$message) && is_array($message['document'])){
            if (!is_null($this->file)){
                $this->file->newFile($message['document'],$this->file->type);
            }else{
                $this->file=new File($message['document'],'document', $Bot);
                $this->type='document';
            }
            //$this->Data['F'] = $this->file->Data;
        }

        if (array_key_exists('entities',$message) && is_array($message['entities'])){
            $this->entities = $this->entities_utf16_to_utf8($this->text,$message['entities']);
            foreach ($this->entities as $value){
                if ($value['type']=='mention'){
                    $this->mentions[]=array('value' => mb_substr($this->text, $value['utf8_offset'], $value['utf8_length'], 'UTF-8'),'offset' => $value['utf8_offset'],'length' => $value['utf8_length']);
                }else if ($value['type']=='bot_command'){
                    $this->botCommands[]=array('value' => mb_substr($this->text, $value['utf8_offset'], $value['utf8_length'], 'UTF-8'),'offset' => $value['utf8_offset'],'length' => $value['utf8_length']);
                }
            }
        }

        if (array_key_exists('caption_entities',$message) && is_array($message['caption_entities'])){
            $this->captionEntities = $this->entities_utf16_to_utf8($this->caption,$message['caption_entities']);
            foreach ($this->captionEntities as $value){
                if ($value['type']=='mention'){
                    $this->mentions[]=array('value' => mb_substr($this->caption, $value['utf8_offset'], $value['utf8_length'], 'UTF-8'),'offset' => $value['utf8_offset'],'length' => $value['utf8_length']);
                }else if ($value['type']=='bot_command'){
                    $this->botCommands[]=array('value' => mb_substr($this->caption, $value['utf8_offset'], $value['utf8_length'], 'UTF-8'),'offset' => $value['utf8_offset'],'length' => $value['utf8_length']);
                }
            }
        }

        if (array_key_exists('reply_markup', $message)){
            $this->reply_markup =$message['reply_markup'];

            if (array_key_exists('inline_keyboard', $message['reply_markup'])) {
                $i = 0;
                foreach ($message['reply_markup']['inline_keyboard'] as $radif) {
                    foreach ($radif as $button) {
                        $this->inlineKeyboardButtons[$i][] = new Keyboard($button);
                    }
                    $i++;
                }
            }
        }

        $this->Bot=$Bot;
    }

    private function setReplyMessage()
    {
        if (!is_null($this->reply_message) && is_array($this->reply_message)) {
            $this->reply_message = new Message($this->reply_message,$this->Bot);
        }
    }

    public function getReplyMessage()
    {
        $this->setReplyMessage();
        return $this->reply_message;
    }

    public function userChatMember()
    {
        if ($this->Bot) {
            return $this->Bot->getChatMember($this->chat->id,$this->user->id);
        }else{return Null;}
    }

    public function pinMessage ()
    {
        if ($this->Bot) {
            return $this->Bot->pinChatMessage($this->chat->id,$this->id);
        }else{return Null;}
    }

    public function unPinMessage ()
    {
        if ($this->Bot) {
            return $this->Bot->unPinChatMessage($this->chat->id,$this->id);
        }else{return Null;}
    }

    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }

    /**
     * دقت بالا: تبدیل offset و length بله از UTF-16 به UTF-8
     * پشتیبانی کامل از ایموجی‌ها و surrogate pairs
     *
     * @param string $text UTF-8 text received from bale
     * @param array $entities entities array from bale (offset/length = UTF-16 units)
     * @return array same entities with added utf8_offset / utf8_length
     *
     * به هر یک از entities دو پارامتر زیر را اضافه می‌کند:
     * [utf8_offset] => 3
     * [utf8_length] => 5
     * که بتوان به شکل زیر ازش استفاده کرد:
     * mb_substr($text, $e['utf8_offset'], $e['utf8_length'], 'UTF-8')
     */
    private function entities_utf16_to_utf8($text, $entities) {
        // رشته ورودی (UTF-8) را به UTF-16LE تبدیل می‌کنیم تا بتوانیم با واحدهای ۲ بایتی کار کنیم
        $utf16 = mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');

        // طول باینری رشته در UTF-16 (تعداد بایت‌ها، نه کاراکترها)
        $utf16_len = strlen($utf16);

        // آرایه‌ای که موقعیت بایت‌های UTF-8 را برای هر کاراکتر UTF-16 ذخیره می‌کند
        // اولین مقدار 0 یعنی شروع رشته
        $utf8_positions = array(0);

        // ایندکس در رشته UTF-16 (بر حسب بایت)
        $utf16_index = 0;
        // ایندکس در رشته UTF-8 (بر حسب بایت)
        $utf8_index = 0;

        // حلقه روی کل رشته UTF-16، به ازای هر ۲ بایت (یک واحد UTF-16)
        while ($utf16_index < $utf16_len) {
            // گرفتن ۲ بایت بعدی (یک واحد UTF-16)
            $unit1 = substr($utf16, $utf16_index, 2);
            // تبدیل آن ۲ بایت به عدد یونیکد (کد پوینت جزئی)
            $arr1 = unpack('v', $unit1);
            $code1 = $arr1[1];

            // بررسی اینکه آیا این کاراکتر بخش اول یک جفت سورروگیت است (یعنی ایموجی یا کاراکتر خاص)
            if ($code1 >= 0xD800 && $code1 <= 0xDBFF && ($utf16_index + 2 < $utf16_len)) {
                // گرفتن واحد دوم برای بررسی جفت سورروگیت
                $unit2 = substr($utf16, $utf16_index + 2, 2);
                $arr2 = unpack('v', $unit2);
                $code2 = $arr2[1];

                // اگر واحد دوم در بازه سورروگیت پایینی است (DC00–DFFF)، یعنی این دو واحد با هم یک کاراکتر تشکیل می‌دهند
                if ($code2 >= 0xDC00 && $code2 <= 0xDFFF) {
                    // تبدیل جفت سورروگیت به UTF-8 (مثلاً برای ایموجی‌ها)
                    $char = mb_convert_encoding($unit1 . $unit2, 'UTF-8', 'UTF-16LE');
                    // در UTF-16، هر سورروگیت pair چهار بایت دارد
                    $utf16_index += 4;
                    // در UTF-8، ممکن است این کاراکتر ۴ بایت طول بکشد (ایموجی)
                    $utf8_index += strlen($char);
                    // ذخیره موقعیت فعلی در UTF-8
                    $utf8_positions[] = $utf8_index;
                    continue;
                }
            }

            // در غیر این صورت، کاراکتر معمولی است (بدون سورروگیت)
            $char = mb_convert_encoding($unit1, 'UTF-8', 'UTF-16LE');
            // هر کاراکتر معمولی در UTF-16 دو بایت است
            $utf16_index += 2;
            // افزایش ایندکس UTF-8 بر اساس طول واقعی آن کاراکتر در UTF-8
            $utf8_index += strlen($char);
            // موقعیت جدید در آرایه ثبت می‌شود
            $utf8_positions[] = $utf8_index;
        }

        // حالا نوبت تبدیل offset/length هر entity است
        $converted = array();
        foreach ($entities as $entity) {
            // مقادیر اولیه از تلگرام (واحد UTF-16)
            $offset16 = isset($entity['offset']) ? $entity['offset'] : 0;
            $length16 = isset($entity['length']) ? $entity['length'] : 0;

            // شروع در UTF-8: از جدول نگاشت به‌دست می‌آید
            $start8 = isset($utf8_positions[$offset16]) ? $utf8_positions[$offset16] : 0;

            // محاسبه ایندکس انتهایی (offset + length در UTF-16)
            $end8_index = $offset16 + $length16;
            // اگر در جدول نگاشت موجود بود از آن استفاده می‌کنیم
            if (isset($utf8_positions[$end8_index])) {
                $end8 = $utf8_positions[$end8_index];
            } else {
                // در غیر این صورت، تا انتهای رشته حساب می‌کنیم
                $end8 = strlen($text);
            }

            // طول بخش در UTF-8 (بر حسب بایت)
            $length8_bytes = $end8 - $start8;

            // جدا کردن بخش مورد نظر از متن بر اساس بایت
            $prefix = substr($text, 0, $start8);
            $segment = substr($text, $start8, $length8_bytes);

            // محاسبه تعداد کاراکترهای UTF-8 قبل از entity
            $utf8_offset_chars = mb_strlen($prefix, 'UTF-8');
            // محاسبه تعداد کاراکترهای UTF-8 در داخل entity
            $utf8_length_chars = mb_strlen($segment, 'UTF-8');

            // افزودن مقادیر تبدیل‌شده به entity
            $entity['utf8_offset'] = $utf8_offset_chars;
            $entity['utf8_length'] = $utf8_length_chars;

            // ذخیره در خروجی نهایی
            $converted[] = $entity;
        }

        // برگرداندن لیست entityهای جدید با offset و length سازگار با UTF-8
        return $converted;
    }

}

class CallbackQuery{
    public $id;
    /** @var User $user */
    public $user;
    /** @var Message $message */
    public $message;
    //public $Data;
    public $data;
    public $chat_instance;
    //public $Data;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;
    public function __construct($callback, $Bot=Null)
    {
        $this->id = array_key_exists('id', $callback) ? $callback['id'] : null;
        $this->user = (array_key_exists('from', $callback) && is_array($callback['from'])) ? new User($callback['from'], $Bot) : null;
        $this->message = (array_key_exists('message', $callback) && is_array($callback['message'])) ? new Message($callback['message'], $Bot) : null;
        $this->data = array_key_exists('data', $callback) ? $callback['data'] : null;
        $this->chat_instance = array_key_exists('chat_instance', $callback) ? $callback['chat_instance'] : null;

        /*$this->Data=array(
            'Id' => $this->id,
            'U' => $this->user->Data,
            'M' => $this->message->Data,
            'TCb' => $this->data,
        );*/

        $this->Bot=$Bot;
    }

    public function answer($text="",$show_alert=false){
        $arr = array("callback_query_id" => $this->id, 'text' => $text,'show_alert'=>$show_alert);
        $result=json_decode($this->Bot->sendrequest('answerCallbackQuery',$arr),true);

        if ($result !== null) {
            if ($result['ok']){
                $return=$result['result'];
            }else{
                $this->error = array(
                    'Code' =>$result['error_code'],
                    'Description' => $result['description']
                );
                $return=$this->error;
            }
        }
        return $return;
    }

    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }
}

class Edited extends Message {
    function __construct($message, $Bot=Null)
    {
        parent::__construct($message, $Bot);
    }
}

class preCheckoutQuery{
    public $id;
    /** @var User $user */
    public $user;
    /** @var Message $message */
    public $currency;
    //public $Data;
    public $total_amount;
    public $invoice_payload;
    //public $Data;
    /** @var mixed|null|Balebot $Bot  */
    private $Bot;
    public function __construct($preCheckout, $Bot=Null)
    {
        $this->id = array_key_exists('id', $preCheckout) ? $preCheckout['id'] : null;
        $this->user = (array_key_exists('from', $preCheckout) && is_array($preCheckout['from'])) ? new User($preCheckout['from'], $Bot) : null;
        $this->currency = array_key_exists('currency', $preCheckout) ? $preCheckout['currency'] : "IRR";
        $this->total_amount = array_key_exists('total_amount', $preCheckout) ? intval($preCheckout['$total_amount']) : null;
        $this->invoice_payload = array_key_exists('invoice_payload', $preCheckout) ? $preCheckout['invoice_payload'] : null;

        /*$this->Data=array(
            'Id' => $this->id,
            'U' => $this->user->Data,
            'M' => $this->message->Data,
            'TCb' => $this->data,
        );*/

        $this->Bot=$Bot;
    }

    public function answer($ok=true,$error_message="با عرض معذرت مشکلی بوجود آمده است."){
        $arr = array("pre_checkout_query_id" => $this->id, 'ok' => $ok,'error_message'=>$error_message);
        $result=json_decode($this->Bot->sendrequest('answerPreCheckoutQuery',$arr),true);

        if ($result !== null) {
            if ($result['ok']){
                $return=$result['result']; //boolean
            }else{
                $this->error = array(
                    'Code' =>$result['error_code'],
                    'Description' => $result['description']
                );
                $return=$this->error;
            }
        }
        return $return;
    }

    public function inquireTransaction(){
        $arr = array("transaction_id" => $this->id);
        $result=json_decode($this->Bot->sendrequest('inquireTransaction',$arr),true);

        if ($result !== null) {
            if ($result['ok'] && array_key_exists('result',$result) && is_array($result['result'])){
                $return=new Transaction($result['result']); //boolean
            }else{
                $this->error = array(
                    'Code' =>$result['error_code'],
                    'Description' => $result['description']
                );
                $return=$this->error;
            }
        }
        return $return;
    }

    public function setBot (Balebot $bot) {
        $this->Bot=$bot;
    }
}
#endregion

//کلاسی برای مدیریت پیام های از قبل آماده به همراه دکمه‌هایشان در پوشه messages کنار فایل کتابخانه
class MessageService
{
    private $messages = [];
    public $states = [];
    public $positions = [];

    public function __construct($category=null,$address="messages")
    {

        //آدرس پوشه مسیج
        //نکته: در صورتی که کتابخانه را از گیت‌هاب دانلود کردید بهتر است این فولدر رو کنار پروژه قرار داده و آدرس دهید.
        //مثلا:
        //"../../messages"
        $base = __DIR__ . "/$address/";


        // پیام‌های مشترک
        $this->messages = include $base . "common.php";

        if (file_exists($base . "states.php")) {
            $this->states = include $base . "states.php";
        }
        if (file_exists($base . "positions.php")) {
            $this->positions = include $base . "positions.php";
        }

        // پیام‌های مخصوص نقش
        if ($category and file_exists($base . "$category.php")) {
            $roleMessages = include $base . "$category.php";
            $this->messages = array_merge($this->messages, $roleMessages);
        }
    }

    public function getState($state, $params = [])
    {
        if (count($this->states)==0){return "[$state]";}

        $key=$this->states[$state];
        $text = array_key_exists($key,$this->messages) ? $this->messages[$key] : "[$key]";

        foreach ($params as $k => $v) {
            $text = str_replace('{{' . $k . '}}', $v, $text);
        }

        return $text;
    }

    public function getPosition($position, $params = [])
    {
        if (count($this->positions)==0){return "[$position]";}

        $Positions= array_flip($this->positions);
        $key=$Positions[$position];
        $text = array_key_exists($key,$this->messages) ? $this->messages[$key] : "[$key]";

        foreach ($params as $k => $v) {
            $text = str_replace('{{' . $k . '}}', $v, $text);
        }

        return $text;
    }

    public function get($key, $params = [])
    {
        $text = array_key_exists($key,$this->messages) ? $this->messages[$key] : "[$key]";

        foreach ($params as $k => $v) {
            $text = str_replace('{{' . $k . '}}', $v, $text);
        }

        return $text;
    }

    public function readPosition($key)
    {
        if (count($this->positions)==0){return "[$key]";}

        $position = array_key_exists($key,$this->positions) ? $this->positions[$key] : "[$key]";

        return $position;
    }

    public function keyboard($key)
    {
        return array_key_exists($key,$this->messages['keyboards']) ? $this->messages['keyboards'][$key] : "";
    }

    public function inlineKeyboard($key)
    {
        return array_key_exists($key,$this->messages['inlineKeys']) ? $this->messages['inlineKeys'][$key] : "";
    }
}

class Balebot{
    public $token;
    public $updat_id;
    public $updat_type;
    /** @var Message $message */
    public $message;
    /** @var Message $messageAll */
    public $messageAll;
    /** @var CallbackQuery $callbackQuery */
    public $callbackQuery;
    /** @var Edited $edited */
    public $edited;

    /** @var preCheckoutQuery $preCheckoutQuery */
    public $preCheckoutQuery;
//public $Data;
    public $apiurl;
    public $downloadApiurl;
    public $error;

    public function __construct($token)
    {
        $this->token=$token;
//$this->Data['token']=$token;
        $this->apiurl="https://tapi.bale.ai/bot".$token;
        $this->downloadApiurl='https://tapi.bale.ai/file/bot'.$token;
    }

    //***********************************(Updates)***************************************
    public function getUpdate($Update_id=Null, $limit=1, $timeout=null)
    {
        /**
         * Update_id
         * -1 to -n:آپدیت های قبلی (دیگر آپدیت ها تایید و حذف می‌شوند)
         * 0: آپدیت های فعلی
         * 1: آپدیت های تایید نشده (آبدیت های با شماره کمتر تایید و حذف می‌شوند)
         * Null: وب‌هووک
         */

        if ($Update_id)
        {
            $rawData = file_get_contents($this->apiurl.'/getupdates?offset='.$Update_id.'&limit='.$limit.(!is_null($timeout) ? '&timeout='.$timeout : ''));
            $rawData =json_decode($rawData, true);
            if (is_array($rawData) && array_key_exists('ok', $rawData)) {
                if ($rawData['ok']) {
                    if (array_key_exists('result', $rawData) && is_array($rawData['result']) && !empty($rawData['result']) && isset($rawData['result'][0]) && is_array($rawData['result'][0])) {
                        return $this->setUpdate($rawData['result'][0]);
                    }else{
                        return null;
                    }
                } else {
                    $this->error = array(
                        'Code' => array_key_exists('error_code', $rawData) ? $rawData['error_code'] : 'Unknown',
                        'Description' => array_key_exists('description', $rawData) ? $rawData['description'] : 'Unknown'
                    );
                    return $this->error;
                }
            } else {
                // Handle JSON decode error or unexpected structure
                $this->error = array('Code' => 'JSON_DECODE_ERROR', 'Description' => 'Failed to decode API response');
                return $this->error;
            }

        }

        else
        {
            $rawData = file_get_contents('php://input');
            $rawData = json_decode($rawData, true);
            if (is_array($rawData)) {
                return $this->setUpdate($rawData);
            } else {
                return null;
            }
        }
    }
    private function setUpdate ($update)
    {
        $this->updat_id = array_key_exists('update_id', $update) ? $update['update_id'] : null;
//$this->Data['upId'] = $this->updat_id;
        if (array_key_exists('message',$update) && is_array($update['message'])) {
            $this->message=new Message($update['message'],$this);
            $this->messageAll=$this->message;
            $this->updat_type="M";
            //$this->Data['M'] = $this->message->Data;
            return $this->message;
        }else if (array_key_exists('callback_query',$update) && is_array($update['callback_query'])) {
            $this->callbackQuery=new CallbackQuery($update['callback_query'],$this);
            $this->messageAll=$this->callbackQuery->message;
            $this->updat_type="Cb";
            //$this->Data['Cb'] = $this->callbackQuery->Data;
            return $this->callbackQuery;
        }else if (array_key_exists('edited_message',$update) && is_array($update['edited_message'])) {
            $this->edited=new Edited($update['edited_message'],$this);
            $this->messageAll=$this->edited;
            $this->updat_type="E";
            //$this->Data['E'] = $this->edited->Data;
            return $this->edited;
        }else if (array_key_exists('pre_checkout_query',$update) && is_array($update['pre_checkout_query'])) {
            $this->preCheckoutQuery=new preCheckoutQuery($update['pre_checkout_query'],$this);
            $this->messageAll=$this->preCheckoutQuery;
            $this->updat_type="Pc";
            //$this->Data['Pc'] = $this->preCheckoutQuery->Data;
            return $this->preCheckoutQuery;
        }else{
            return null;
        }
    }
    public function setWebhook($url)
    {
        $rawData = file_get_contents($this->apiurl.'/setwebhook?url='.$url);
        $rawData =json_decode($rawData, true);
        if (is_array($rawData) && array_key_exists('ok', $rawData) && $rawData['ok']) {
            return (array_key_exists('result', $rawData)) ? $rawData['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($rawData) && array_key_exists('error_code', $rawData)) ? $rawData['error_code'] : 'Unknown',
                'Description' => (is_array($rawData) && array_key_exists('description', $rawData)) ? $rawData['description'] : 'Unknown'
            );
            return $this->error;
        }

    }
    public function deleteWebhook()
    {
        $rawData = file_get_contents($this->apiurl.'/deletewebhook');
        $rawData =json_decode($rawData, true);
        if (is_array($rawData) && array_key_exists('ok', $rawData) && $rawData['ok']) {
            return (array_key_exists('result', $rawData)) ? $rawData['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($rawData) && array_key_exists('error_code', $rawData)) ? $rawData['error_code'] : 'Unknown',
                'Description' => (is_array($rawData) && array_key_exists('description', $rawData)) ? $rawData['description'] : 'Unknown'
            );
            return $this->error;
        }

    }
    public function getWebhookInfo()
    {
        $rawData = file_get_contents($this->apiurl.'/getwebhookinfo');
        $rawData =json_decode($rawData, true);
        if (is_array($rawData) && array_key_exists('ok', $rawData) && $rawData['ok']) {
            $result = (array_key_exists('result', $rawData)) ? $rawData['result'] : null;
            return array(
                (is_array($result) && array_key_exists('url', $result)) ? $result['url'] : null,
                (is_array($result) && array_key_exists('pending_update_count', $result)) ? $result['pending_update_count'] : null
            );
        }else{
            $this->error = array(
                'Code' => (is_array($rawData) && array_key_exists('error_code', $rawData)) ? $rawData['error_code'] : 'Unknown',
                'Description' => (is_array($rawData) && array_key_exists('description', $rawData)) ? $rawData['description'] : 'Unknown'
            );
            return $this->error;
        }

    }

    //***************************************(Chat User)*******************************
    public function getMe(){
        $result=json_decode($this->sendrequest('getMe',""),true);
//$result=$this->sendrequest('getMe',"");
        //return $result;
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
            $return=new User($result['result'],$this);
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function getChat($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('getChat',$arr),true);

        if (is_array($result) && array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
            $return=new Chat($result['result'],$this);
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function getChatMember($chat_id,$user_id){
        $arr = array("chat_id" => $chat_id, 'user_id' => $user_id);
        $result=json_decode($this->sendrequest('getChatMember',$arr),true);
        //$result=$this->sendrequest('getChatMember',$arr);
        //return $result;
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
            $return=new ChatMember($result['result'],$this);
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function getChatMembersCount($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('getChatMembersCount',$arr),true);
//$result=$this->sendrequest('getChat',$arr);
        //return $result;
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = array_key_exists('result', $result) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function inviteUser($chat_id,$user_id){
        $arr = array("chat_id" => $chat_id, 'user_id' => $user_id);
        $result=json_decode($this->sendrequest('inviteUser',$arr),true);
        //$result=$this->sendrequest('inviteUser',$arr);
        //return $result;
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function leaveChat($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('leaveChat',$arr),true);
//$result=$this->sendrequest('getChat',$arr);
        //return $result;
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function promoteChatMember($chat_id,$user_id,$can_change_info =true,$can_post_messages =true,$can_edit_messages =true,$can_delete_messages =true,$can_manage_video_chats =true,$can_invite_users =true,$can_restrict_members =true){
        $arr = array("chat_id" => $chat_id, 'user_id'=> $user_id, 'can_change_info' => $can_change_info, 'can_post_messages' => $can_post_messages, 'can_edit_messages' => $can_edit_messages, 'can_delete_messages' => $can_delete_messages, 'can_manage_video_chats' => $can_manage_video_chats, 'can_invite_users' => $can_invite_users, 'can_restrict_members' => $can_restrict_members);
        $result=json_decode($this->sendrequest('promoteChatMember',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function banChatMember($chat_id,$user_id){
        $arr = array("chat_id" => $chat_id, 'user_id'=> $user_id);
        $result=json_decode($this->sendrequest('banChatMember',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function unbanChatMember($chat_id,$user_id,$only_if_banned=false){
        $arr = array("chat_id" => $chat_id, 'user_id'=> $user_id, 'only_if_banned' => $only_if_banned);
        $result=json_decode($this->sendrequest('unbanChatMember',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function pinChatMessage($chat_id,$message_id){
        $arr = array("chat_id" => $chat_id, 'message_id'=> $message_id);
        $result=json_decode($this->sendrequest('pinChatMessage',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function unPinChatMessage($chat_id,$message_id){
        $arr = array("chat_id" => $chat_id, 'message_id'=> $message_id);
        $result=json_decode($this->sendrequest('unPinChatMessage',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function unpinAllChatMessages($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('unpinAllChatMessages',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function setChatTitle($chat_id, $title){
        $arr = array("chat_id" => $chat_id, 'title'=> $title);
        $result=json_decode($this->sendrequest('setChatTitle',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function setChatPhoto($chat_id,$photo){
        $arr = array("chat_id" => $chat_id, 'photo' => $photo);
        $result=json_decode($this->sendrequest('setChatPhoto',$arr),true);

        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function deleteChatPhoto($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('deleteChatPhoto',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function setChatDescription($chat_id, $description){
        $arr = array("chat_id" => $chat_id, 'description'=> $description);
        $result=json_decode($this->sendrequest('setChatDescription',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function createChatInviteLink($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('createChatInviteLink',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function exportChatInviteLink($chat_id){
        $arr = array("chat_id" => $chat_id);
        $result=json_decode($this->sendrequest('exportChatInviteLink',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }
    public function revokeChatInviteLink($chat_id, $invite_link){
        $arr = array("chat_id" => $chat_id, 'invite_link' => $invite_link);
        $result=json_decode($this->sendrequest('revokeChatInviteLink',$arr),true);
        if (is_array($result) && array_key_exists('ok', $result) && $result['ok']){
            $return = (array_key_exists('result', $result)) ? $result['result'] : null;
        }else{
            $this->error = array(
                'Code' => (is_array($result) && array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                'Description' => (is_array($result) && array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
            );
            $return=$this->error;
        }
        return $return;
    }


    //******************************************(Keyboard)**********************************************
    //از کلاس Keyboard استفاده میشه

    //********************************************(SEND)*****************************************
    public function sendText($chat_id,$text='',$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, 'text' => $text);
            if ($keyboard) {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendMessage',$arr),true);
            //return $this->sendrequest('sendMessage',$arr);
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["text"])){
                $arr['text']=trim($arr['text']);
                $result=json_decode($this->sendrequest('sendMessage',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendPhoto($chat_id,$photo='',$caption="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "photo" => $photo ,'caption' => $caption);;
            if ($keyboard) {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendPhoto',$arr),true);
            //$result=$this->sendrequest('sendPhoto',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["photo"])){
                $arr['photo']=trim($arr['photo']);
                $result=json_decode($this->sendrequest('sendPhoto',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendVideo($chat_id,$video="",$caption="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "video" => $video ,'caption' => $caption);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendVideo',$arr),true);
//			$result=$this->sendrequest('sendVideo',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["video"])){
                $arr['video']=trim($arr['video']);
                $result=json_decode($this->sendrequest('sendVideo',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendAudio($chat_id,$audio="",$caption="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "audio" => $audio ,'caption' => $caption);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendAudio',$arr),true);
//			$result=$this->sendrequest('sendAudio',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["audio"])){
                $arr['audio']=trim($arr['audio']);
                $result=json_decode($this->sendrequest('sendAudio',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendVoice($chat_id,$voice="",$caption="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "voice" => $voice ,'caption' => $caption);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendVoice',$arr),true);
//			$result=$this->sendrequest('sendVoice',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["voice"])){
                $arr['voice']=trim($arr['voice']);
                $result=json_decode($this->sendrequest('sendVoice',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendDocument($chat_id,$document="",$caption="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "document" => $document ,'caption' => $caption);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendDocument',$arr),true);
//			$result=$this->sendrequest('sendDocument',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["document"])){
                $arr['document']=trim($arr['document']);
                $result=json_decode($this->sendrequest('sendDocument',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendAnimation($chat_id,$animation="",$caption="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "animation" => $animation ,'caption' => $caption);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendAnimation',$arr),true);
//			$result=$this->sendrequest('sendAnimation',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["animation"])){
                $arr['animation']=trim($arr['animation']);
                $result=json_decode($this->sendrequest('sendAnimation',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }

    public function sendMediaGroup($chat_id,$medias=[],$type=[],$caption="",$reply_to_message_id="")
    {
        /*
     * $type=
     * photo
     * video
     * audio
     * animation
     * document
     */
        $result=null;
        if(!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id);
            $arr['media']=array_map(function ($path,$type,$caption) {
                return ['type' => $type, 'media' => $path];
            }, $medias, $type);
            if ($caption!=""){
                $arr['media'][0]['caption']=$caption;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendMediaGroup',$arr),true);
            //$result=$this->sendrequest('sendMediaGroup',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["media"])){
                $result=json_decode($this->sendrequest('sendMediaGroup',$arr),true);
            }else{
                $return="undefined chat id or photo";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }

    public function sendContact($chat_id,$phone="",$first_name="",/*$last_name="",*/$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "phone_number" => $phone ,'first_name' => $first_name);//,'$last_name' => $last_name);
            if ($keyboard) {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendContact',$arr),true);
//			$result=$this->sendrequest('sendContact',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["photo"])){
                $arr['phone_number']=trim($arr['phone_number']);
                $arr['first_name']=trim($arr['first_name']);
                $result=json_decode($this->sendrequest('sendContact',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message( $result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendLocation($chat_id,$tool="",$arz="",$keyboard=null,$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, "longitude" => $tool ,'latitude' => $arz);
            if ($keyboard) {
                $arr['reply_markup'] = $keyboard;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendLocation',$arr),true);
//			$result=$this->sendrequest('sendLocation',$arr);
//			return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["latitude"]) and isset($arr["longitude"])){
                $arr['latitude']=trim($arr['latitude']);
                $arr['longitude']=trim($arr['longitude']);
                $result=json_decode($this->sendrequest('sendLocation',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }

    //**********************************(Edit Delete Forward Download)***************************************************
    public function editText($chat_id,$message_id="",$text="",$keyboard=null){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id,"message_id" => $message_id, 'text' => $text);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            //$result=json_decode($this->sendrequest('editMessageText',$arr),true);
            $result=$this->sendrequest('editMessageText',$arr);
            // Manually decode since the original was commented out
            $result = json_decode($result, true);
            // return $result; // Original return
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["message_id"]) and isset($arr["text"])){
                $arr['text']=trim($arr['text']);
                $result=json_decode($this->sendrequest('editMessageText',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function editCaption($chat_id,$message_id="",$caption="",$keyboard=null){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id,"message_id" => $message_id, 'caption' => $caption);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            //$result=json_decode($this->sendrequest('editMessageCaption',$arr),true);
            $result=$this->sendrequest('editMessageCaption',$arr);
            // Manually decode since the original was commented out
            $result = json_decode($result, true);
            // return $result; // Original return
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["message_id"]) and isset($arr["caption"])){
                $arr['caption']=trim($arr['caption']);
                $result=json_decode($this->sendrequest('editMessageCaption',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function editMessageReplyMarkup($chat_id,$message_id="",$keyboard=null){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id,"message_id" => $message_id);
            if ($keyboard != "") {
                $arr['reply_markup'] = $keyboard;
            }
            //$result=json_decode($this->sendrequest('editMessageReplyMarkup',$arr),true);
            $result=$this->sendrequest('editMessageReplyMarkup',$arr);
            // Manually decode since the original was commented out
            $result = json_decode($result, true);
            // return $result; // Original return
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["message_id"]) and isset($arr["reply_markup"])){
                $result=json_decode($this->sendrequest('editMessageReplyMarkup',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function delete($chat_id,$message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id,"message_id" => $message_id);
            $result=json_decode($this->sendrequest('deleteMessage',$arr),true);
            //$result=$this->sendrequest('deleteMessage',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["message_id"])){
                $result=json_decode($this->sendrequest('deleteMessage',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok']){
                $return = (array_key_exists('result', $result)) ? $result['result'] : null;
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function forward($chat_id,$fromChat_id,$message_id){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, 'from_chat_id' => $fromChat_id, 'message_id' => $message_id);
            $result=json_decode($this->sendrequest('forwardMessage',$arr),true);
            //$result=$this->sendrequest('forwardMessage',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["from_chat_id"]) and isset($arr["message_id"])){
                $arr['from_chat_id']=trim($arr['from_chat_id']);
                $arr['message_id']=trim($arr['message_id']);
                $result=json_decode($this->sendrequest('forwardMessage',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function copyM($chat_id,$fromChat_id,$message_id,$reply_to_message_id="",$caption=false){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, 'from_chat_id' => $fromChat_id, 'message_id' => $message_id);
            if ($caption===false){
                $arr = array("chat_id" => $chat_id, 'from_chat_id' => $fromChat_id, 'message_id' => $message_id);
            }else{
                $arr = array("chat_id" => $chat_id, 'from_chat_id' => $fromChat_id, 'message_id' => $message_id, 'caption' => $caption);
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('copyMessage',$arr),true);
            //$result=$this->sendrequest('forwardMessage',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["from_chat_id"]) and isset($arr["message_id"])){
                $arr['from_chat_id']=trim($arr['from_chat_id']);
                $arr['message_id']=trim($arr['message_id']);
                $result=json_decode($this->sendrequest('copyMessage',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function download(File $file, $url){
        $file_path = $file->filePath();
        $file_address=$this->downloadApiurl.'/'.$file_path;
        return copy($file_address, $url);
    }

    //********************************************(Pay)*************************************************
    public function Invoice($chat_id,$title='',$description='',$payload='',$price=0,$provider_token='WALLET-TEST-1111111111111111',$photo_url="",$reply_to_message_id=""){
        $result=null;
        if (!is_array($chat_id)){
            if (is_array($price)){
                $tedad=count($price);
                for ($i=0;$i<=$tedad-1;$i++){
                    if ($i%2==0){
                        $label=$price[$i];
                    }else{
                        $a=($i+1)/2-1;
                        $prices[$a] = array("label" => strval($label) , "amount" => is_string($price[$i]) ? intval($price[$i]) : $price[$i]);
                    }
                }
            }else{
                if(is_string($price) or is_int($price) or is_integer($price)  or is_double($price)  or is_float($price) )
                {$prices=array(array("label" => "قیمت", "amount" => is_string($price) ? intval($price) : $price));}
            }

//برای دادن لیست قیمت
            $arr = array("chat_id" =>$chat_id,"title" =>$title,"description" =>$description,"provider_token" =>$provider_token,"payload" => $payload, "prices" => $prices);
            if ($photo_url != "") {
                $arr['photo_url'] = $photo_url;
            }
            if ($reply_to_message_id != "") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            $result=json_decode($this->sendrequest('sendInvoice',$arr),true);
            //$result=$this->sendrequest('sendInvoice',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["title"]) and isset($arr["description"]) and isset($arr["provider_token"]) and isset($arr["prices"]) and isset($arr["payload"])){
                $arr['photo_url']=trim($arr['photo_url']);
                $arr['description']=trim($arr['description']);
                $arr['provider_token']=trim($arr['provider_token']);
                $arr['payload']=trim($arr['payload']);
                if(is_string($arr['prices']) or is_int($arr['prices']) or is_integer($arr['prices'])  or is_double($arr['prices'])  or is_float($arr['prices']) )
                {$arr['prices']=array(array("label" => "قیمت", "amount" => $arr['prices']));}//,array("label" => "قیمت", "amount" => "$arr['prices']"));
//برای دادن لیست قیمت}
                $result=json_decode($this->sendrequest('sendInvoice',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return=new Message($result['result'],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function createInvoiceLink($title,$description='',$payload='',$price=0,$provider_token='WALLET-TEST-1111111111111111'){ //برای استفاده در مینی اپ
        $result=null;
        if (!is_array($title)){
            if (is_array($price)){
                $tedad=count($price);
                for ($i=0;$i<=$tedad-1;$i++){
                    if ($i%2==0){
                        $label=$price[$i];
                    }else{
                        $a=($i+1)/2-1;
                        $prices[$a] = array("label" => strval($label) , "amount" => is_string($price[$i]) ? intval($price[$i]) : $price[$i]);
                    }
                }
            }else{
                if(is_string($price) or is_int($price) or is_integer($price)  or is_double($price)  or is_float($price) )
                {$prices=array(array("label" => "قیمت", "amount" => is_string($price) ? intval($price) : $price));}
            }

//برای دادن لیست قیمت
            $arr = array("title" =>$title,"description" =>$description,"provider_token" =>$provider_token,"payload" => $payload, "prices" => $prices);
            $result=json_decode($this->sendrequest('createInvoiceLink',$arr),true);
            //$result=$this->sendrequest('createInvoiceLink',$arr);
            //return $result;
        }else{
            $arr=$title;
            if(isset($arr["title"]) and isset($arr["description"]) and isset($arr["provider_token"]) and isset($arr["prices"]) and isset($arr["payload"])){
                $arr['description']=trim($arr['description']);
                $arr['provider_token']=trim($arr['provider_token']);
                $arr['payload']=trim($arr['payload']);
                if(is_string($arr['prices']) or is_int($arr['prices']) or is_integer($arr['prices'])  or is_double($arr['prices'])  or is_float($arr['prices']) )
                {$arr['prices']=array(array("label" => "قیمت", "amount" => $arr['prices']));}//,array("label" => "قیمت", "amount" => "$arr['prices']"));
//برای دادن لیست قیمت}
                $result=json_decode($this->sendrequest('createInvoiceLink',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok'] && array_key_exists('result', $result) && is_array($result['result'])){
                $return="invoice_id=".$result['result']['invoice_id'];
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }

    //**************************************(Actions)*************************************
    /**
     * typing: ربات در حال نوشتن یک پیام متنی است
     * upload_photo: ربات در حال بارگذاری (آپلود) یک عکس است
     * record_video: ربات در حال ضبط یک ویدئو است
     * upload_video: ربات در حال بارگذاری یک ویدئو است
     * record_voice: ربات در حال ضبط یک پیام صوتی است
     * upload_voice: ربات در حال بارگذاری یک پیام صوتی است
     *  choose_sticker: ربات در حال انتخاب یک استیکر برای ارسال است

     * ادامه فعلا در بله پشتیبانی نمی‌شود
     * upload_document: ربات در حال بارگذاری یک فایل (مانند PDF یا هر نوع فایل دیگر) است
     * find_location: ربات در حال جستجوی موقعیت مکانی(Location)
     * record_video_note: ربات در حال* ضبط یک ویدئو نوت (پیام تصویری) است
     * upload_video_note: ربات در حال بارگذاری یک ویدئو نوت است.
     */
    public function action($chat_id,$action){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, 'action' => $action);
            $result=json_decode($this->sendrequest('sendChatAction',$arr),true);
            //$result=$this->sendrequest('sendChatAction',$arr);
            //return $result;
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["action"])){
                $arr['action']=trim($arr['action']);
                $result=json_decode($this->sendrequest('action',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok']){
                $return = (array_key_exists('result', $result)) ? $result['result'] : null;
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }

    //**************************************(نظر سنجی)*************************************
    public function askReview($user_id){
        $result=null;
        $arr = array("user_id" => intval($user_id));
        $result=json_decode($this->sendrequest('askReview',$arr),true);

        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok']){
                $return = (array_key_exists('result', $result)) ? $result['result'] : null;
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }

    //********************************************(API Send Request)***********************************************************
    public function sendRequest($metod, $arr)
    {
        $command = $metod;
        $hasFile = false;
        $headers = [];
        $postFields = null;

        // اگر متد sendMediaGroup باشد
        if ($command === "sendMediaGroup" && isset($arr['media']) && is_array($arr['media'])) {

            $media = [];
            $fileIndex = 0;

            foreach ($arr['media'] as $item) {
                if (isset($item['media']) && file_exists($item['media'])) {
                    $fileName = "file" . $fileIndex;
                    $arr[$fileName] = new CURLFile(realpath($item['media']));
                    $item['media'] = "attach://$fileName";
                    $fileIndex++;
                    $hasFile = true;
                }
                $media[] = $item;
            }

            $arr['media'] = json_encode($media, JSON_UNESCAPED_UNICODE);
            $postFields = $arr;

        } elseif (is_array($arr)) {

            // سایر متدها
            foreach ($arr as $key => $value) {
                if (is_string($value) && file_exists($value)) {
                    $arr[$key] = new CURLFile(realpath($value));
                    $hasFile = true;
                }
            }

            if ($hasFile) {
                $postFields = $arr;
            } else {
                $postFields = json_encode($arr, JSON_UNESCAPED_UNICODE);
                $headers = ["Content-Type: application/json"];
            }
        }

        // تنظیم و اجرای curl
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiurl . "/$command",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #: " . $err;
        }

        return $response;
    }

    //****************************************(Handlers And Conditions)*************************************************
    public function MessageHandler($FunctionName, ...$Conditions)
    {
        if ($this->updat_type=="M"){
            $result=true;
            foreach ($Conditions as $Condition){
                if (!$this->ConditionCheck($Condition)){$result=false;}
            }
            if ($result){
                $FunctionName();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function CallbackHandler($FunctionName, ...$Conditions)
    {
        if ($this->updat_type=="Cb"){
            $result=true;
            foreach ($Conditions as $Condition){
                if (!$this->ConditionCheck($Condition)){$result=false;}
            }
            if ($result){
                $FunctionName();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function EditedHandler($FunctionName, ...$Conditions)
    {
        if ($this->updat_type=="E"){
            $result=true;
            foreach ($Conditions as $Condition){
                if (!$this->ConditionCheck($Condition)){$result=false;}
            }
            if ($result){
                $FunctionName();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function Precheckout($FunctionName, ...$Conditions)
    {
        if ($this->updat_type=="Pc"){
            $result=true;
            foreach ($Conditions as $Condition){
                if (!$this->ConditionCheck($Condition)){$result=false;}
            }
            if ($result){
                $FunctionName();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function CommandHandler($FunctionName, $CommandName, ...$Conditions)
    {
        if ($this->updat_type=="M" and substr_count($this->message->text, $CommandName) != 0){
            $result=true;
            foreach ($Conditions as $Condition){
                if (!$this->ConditionCheck($Condition)){$result=false;}
            }
            if ($result){
                $CommandName .= " ";
                $CommandData=str_replace($CommandName, "", $this->message->text);
                $FunctionName($CommandData);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function EventHandler($FunctionName, ...$Conditions)
    {
        $result=true;
        foreach ($Conditions as $Condition){
            if (!$this->ConditionCheck($Condition)){$result=false;}
        }
        if ($result){
            $FunctionName();
            return true;
        }else{
            return false;
        }

    }

    public function ConditionCheck($Condition){
        if ($Condition!='All') {
            $result = false;
            if (is_array($Condition)) {
                if ($Condition[1]=="="){
                    for ($i = 2; $i < count($Condition); $i++) {
                        if ($Condition[$i] == $Condition[0]) {
                            $result = true;
                        }
                    }
                }elseif ($Condition[1]=="!"){
                    for ($i = 2; $i < count($Condition); $i++) {
                        if ($Condition[$i] != $Condition[0]) {
                            $result = true;
                        }else{
                            $result = false;
                            break;
                        }
                    }
                }elseif ($Condition[1]==">"){
                    if ($Condition[2] < $Condition[0]) {
                        $result = true;
                    }
                }elseif ($Condition[1]=="<"){
                    if ($Condition[2] > $Condition[0]) {
                        $result = true;
                    }
                }else{
                    for ($i = 1; $i < count($Condition); $i++) {
                        if ($Condition[$i] == $Condition[0]) {
                            $result = true;
                        }
                    }
                }
            } else {
                if ($this->messageAll->chat->type == $Condition) {
                    $result = true;
                }
                if ($this->messageAll->type == $Condition) {
                    $result = true;
                }
                if ($Condition == 'forward') {
                    if (!is_null($this->messageAll->forward_from)) {
                        $result = true;
                    }
                } elseif ($Condition == 'reply') {
                    if (!is_null($this->messageAll->reply_message)) {
                        $result = true;
                    }
                } elseif ($Condition == 'pay_ok') {
                    if ($this->messageAll->type == 'successful_payment') {
                        $result = true;
                    }
                } elseif ($Condition == 'is_joined') {
                    if ($this->messageAll->type == 'new_chat_members') {
                        $result = true;
                    }
                } elseif ($Condition == 'is-lefted') {
                    if ($this->messageAll->type == 'left_chat_member') {
                        $result = true;
                    }
                }
            }
        }else{$result = true;}

        return $result;
    }

    //****************************************(Mini App)*************************************************
    public function verify($initData){
        parse_str($initData, $data_array);
        // بررسی وجود کلید 'hash' برای امضای داده‌ها
        if (!isset($data_array['hash'])) {
            return array('result'=>'error','message'=>'Missing hash');
        }

        // جدا کردن مقدار hash برای مقایسه نهایی
        $hash_from_data = $data_array['hash'];
        unset($data_array['hash']); // از بررسی حذف می‌شود

        // مرتب‌سازی پارامترها بر اساس حروف الفبا
        ksort($data_array);

        // بازسازی رشته داده‌ها برای محاسبه امضا
        $check_arr = array();
        foreach ($data_array as $key => $value) {
            $check_arr[] = $key . '=' . $value;
        }
        $data_check_string = implode("\n", $check_arr);

        // محاسبه کلید مخفی با استفاده از HMAC-SHA256 و توکن ربات
        $secret_key = hash_hmac('sha256', $this->token, "WebAppData", true);
        // تولید امضای نهایی از داده‌ها
        $computed_hash = hash_hmac('sha256', $data_check_string, $secret_key);

        // مقایسه امضاها به‌صورت امن
        if (hash_equals($computed_hash, $hash_from_data)) {
            // بررسی زمان اعتبار (۵ دقیقه = ۳۰۰ ثانیه)
            if (isset($data_array['auth_date']) && (time() - intval($data_array['auth_date']) > 300)) {
                return array('result'=>'false','message'=>'EXPIRED');
            }

            // ✅ داده معتبر است
            return array('result'=>'true','message'=>'VALID');
        } else {
            // ❌ داده تغییر کرده یا امضا مطابقت ندارد
            return array('result'=>'false','message'=>'INVALID');
        }
    }
}

class BaleOTP{
    private $client_id;
    private $client_secret;
    private $access_token;
    private $expires_in;

    /**
     * @param $otpData['username', 'password']
     * get username and password from: https://safir.bale.ai/gateway
     */
    public function __construct($otpData = ['username', 'password'])
    {
        // بررسی وجود کلیه فیلدهای ضروری
        $required_fields = ['username', 'password'];

        foreach ($required_fields as $field) {
            if (!isset($otpData[$field]) || empty(trim($otpData[$field]))) {
                throw new InvalidArgumentException("{$field} is required and cannot be empty");
            }
        }

        // صحت‌سنجی client_id و client_secret (حداقل طول)
        if (strlen($otpData['username']) < 10) {
            throw new InvalidArgumentException('client_id must be at least 10 characters long');
        }

        if (strlen($otpData['password']) < 10) {
            throw new InvalidArgumentException('client_secret must be at least 10 characters long');
        }

        $this->client_id = trim($otpData["username"]);
        $this->client_secret = trim($otpData["password"]);

        //$this->getAuthToken($this->client_id,$this->client_secret);
    }
    public function getAuthToken($client_id = null, $client_secret = null, $scope = 'read') {
        $url = "https://safir.bale.ai/api/v2/auth/token";

        $data = [
            'grant_type' => 'client_credentials',
            'client_secret' => !is_null($client_secret) ? $client_secret : $this->client_secret,
            'scope' => $scope,
            'client_id' => !is_null($client_id) ? $client_id : $this->client_id
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return ['error' => true, 'message' => $error];
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => true, 'message' => 'خطا در پردازش JSON پاسخ'];
        }

        $this->access_token=$result["access_token"];
        $this->expires_in=$result["expires_in"];

        return [
            'error' => false,
            'http_code' => $http_code,
            'data' => $result
        ];
    }

    public function sendOtp($phone, $otp, $options = [])
    {
        // صحت‌سنجی فرمت شماره تلفن (شروع با 98)
        if (!preg_match('/^98\d{10}$/', $phone)) {
            throw new InvalidArgumentException('Phone number must start with 98 and be 12 digits long');
        }
        // صحت‌سنجی OTP (عدد 6 رقمی)
        if (!preg_match('/^\d{3,8}$/', $otp)) {
            throw new InvalidArgumentException('OTP must be a 4-digit number');
        }


        $url = "https://safir.bale.ai/api/v2/send_otp";

        // داده‌های اصلی
        $data = [
            'phone' => $phone,
            'otp' => $otp
        ];

        //var_dump($data);
        $ch = curl_init();

        $curl_options = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->access_token,
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: PHP-Client/1.0'
            ],
            CURLOPT_TIMEOUT => isset($options['timeout']) ? $options['timeout'] : 30,
            CURLOPT_SSL_VERIFYPEER => isset($options['verify_ssl']) ? $options['verify_ssl'] : false
        ];

        curl_setopt_array($ch, $curl_options);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $curl_info = curl_getinfo($ch);

        curl_close($ch);

        // ساختار نتیجه
        $result = [
            'success' => false,
            'http_code' => $http_code,
            'error' => null,
            'data' => null,
            'response_time' => isset($curl_info['total_time']) ? $curl_info['total_time'] : 0,
            'request_info' => [
                'url' => $url,
                'method' => 'POST',
                'headers' => $curl_options[CURLOPT_HTTPHEADER]
            ]
        ];
        //var_dump($response);
        if ($error) {
            $result['error'] = [
                'type' => 'curl_error',
                'message' => $error
            ];
            return $result;
        }

        // پردازش پاسخ JSON
        $response_data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $result['error'] = [
                'type' => 'json_parse_error',
                'message' => 'خطا در پردازش JSON پاسخ: ' . json_last_error_msg(),
                'raw_response' => $response
            ];
            return $result;
        }

        // بررسی موفقیت آمیز بودن
        if ($http_code >= 200 && $http_code < 300) {
            $result['success'] = true;
            $result['data'] = $response_data;
        } else {
            $result['error'] = [
                'type' => 'api_error',
                'message' => isset($response_data['message']) ? $response_data['message'] : 'خطای ناشناخته از سرور',
                'code' => isset($response_data['code']) ? $response_data['code'] : null,
                'details' => $response_data
            ];
            $result['data'] = $response_data;
        }

        return $result;
    }
}

class Eitaabot{
    public $token;
    /** @var Message $message */
    public $message;
    /** @var Message $messageAll */
    public $apiurl;
    public $error;
    public function __construct($token)
    {
        $this->token=$token;
        $this->apiurl="https://eitaayar.ir/api/".$token;
    }

    public function sendText($chat_id,$text="",$title="",$reply_to_message_id="", $disable_notification=false,$pin=false,$date="",$viewCountForDelete=null){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, 'text' => $text);
            if ($title != "") {
                $arr['title'] = $title;
            }
            if ($reply_to_message_id !="") {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            if ($disable_notification){
                $arr['disable_notification'] = 1;
            }
            if ($pin){
                $arr['pin'] = 1;
            }
            if ($date) {
                $arr['date'] = $date;
            }
            if ($viewCountForDelete) {
                $arr['viewCountForDelete'] = $viewCountForDelete;
            }

            $result=json_decode($this->sendRequest('sendMessage',$arr),true);
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["text"])){
                $arr['text']=trim($arr['text']);
                $result=json_decode($this->sendRequest('sendMessage',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok']){
                $return=new Message((array_key_exists('result', $result)) ? $result['result'] : [],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendFile($chat_id,$file="",$caption="",$title="",$reply_to_message_id="", $disable_notification=false,$pin=false,$date="",$viewCountForDelete=null){
        $result=null;
        if (!is_array($chat_id)){
            $arr = array("chat_id" => $chat_id, 'file' => $file);
            if ($caption) {
                $arr['caption'] = $caption;
            }
            if ($title) {
                $arr['title'] = $title;
            }
            if ($reply_to_message_id) {
                $arr['reply_to_message_id'] = $reply_to_message_id;
            }
            if ($disable_notification){
                $arr['disable_notification'] = 1;
            }
            if ($pin){
                $arr['pin'] = 1;
            }
            if ($date) {
                $arr['date'] = $date;
            }
            if ($viewCountForDelete) {
                $arr['viewCountForDelete'] = $viewCountForDelete;
            }
            $result=json_decode($this->sendRequest('sendFile',$arr),true);
        }else{
            $arr=$chat_id;
            if(isset($arr["chat_id"]) and isset($arr["file"])){
                //$arr['file']=trim($arr['file']);
                $result=json_decode($this->sendRequest('sendFile',$arr),true);
            }else{
                $return="undefined chat_id or text";
            }
        }
        if ($result !== null && is_array($result)) {
            if (array_key_exists('ok', $result) && $result['ok']){
                $return=new Message((array_key_exists('result', $result)) ? $result['result'] : [],$this);
            }else{
                $this->error = array(
                    'Code' => (array_key_exists('error_code', $result)) ? $result['error_code'] : 'Unknown',
                    'Description' => (array_key_exists('description', $result)) ? $result['description'] : 'Unknown'
                );
                $return=$this->error;
            }
        } else {
            $return = isset($return) ? $return : null;
        }
        return $return;
    }
    public function sendRequest($metod,$arr)
    {
        $command=$metod;
        //$data_json=json_encode($arr);
        $curl = curl_init();
        if(is_array($arr) && array_key_exists('file',$arr)){
            $arr['file']=new CurlFile($arr['file']);
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiurl."/$command",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $arr,//$data_json, //***
            CURLOPT_SSL_VERIFYHOST => 0, //***
            CURLOPT_SSL_VERIFYPEER => false, //***

        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
?>