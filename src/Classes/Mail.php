<?php


namespace App\Classes;


use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $apiKey = '70de16c13cbfeefd700f88a7ad261ea3';
    private $apiKeySecret = '9ca29e7cc7b13b54a03accc5db033eed';

    public function send($toEmail, $toName, $subject, $content){

        $mj = new Client($this->apiKey, $this->apiKeySecret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "ninours971@gmail.com",
                        'Name' => "MyShop"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName
                        ]
                    ],
                    'TemplateID' => 2445594,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}