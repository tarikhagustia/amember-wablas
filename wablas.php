<?php
require_once 'libs/Wablas.php';

class Am_Plugin_Wablas extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '5.5.4';
    const TOKEN = 'VZ1nhgIQOiTGZznUsSNZAE0qkGEJITykdeRhtZLttp0d8N66A3PKXEgpYOxGCxjO';
    const ALLOWED_TEMPLATE = [
        'send_payment_mail', 'expire', 'payment', 'invoice_pay_link'
    ];

    protected $_configPrefix = 'misc.';
    /**
     * @var Am_Mail_Template $_template
     */
    protected $_template;


    public function onMailTemplateBeforeSend(Am_Event $event)
    {
        $client = new Wablas(self::TOKEN);
        $this->_template = $event->getTemplate();
        $recepient = $event->getRecepient();

        if ($recepient instanceof User) {
            $phone = $recepient->phone;
        } else {
            $phone = $this->getDi()->userTable->findFirstByEmail($recepient)->phone;
        }

        if ($phone && in_array($this->_template->getConfig()['name'], self::ALLOWED_TEMPLATE)) {
            $content = $this->_template->getMail()->getBodyText()->getRawContent();
            $resposne = $client->sendMessage($phone, $content);

            if ($resposne->status) {
                $this->logDebug(sprintf("SUCCESS SEND WA TO %s WITH MESSAGE %s", $phone, $content));
            } else {
                $this->logDebug(sprintf("FAILED SEND WA TO %s WITH MESSAGE %s", $phone, $content));
            }
        }
    }
}