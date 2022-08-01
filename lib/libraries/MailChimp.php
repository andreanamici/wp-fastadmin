<?php

namespace FastAdmin\lib\libraries;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use MailchimpMarketing\ApiClient     as MarkeringApiClient;
use MailchimpTransactional\ApiClient as TransactionalApiClient;
use Throwable;

require_once WP_FA_BASE_PATH_CONFIGS .'/mailchimp.php';

class MailChimp
{
    /**
     * @var MarkeringApiClient
     */
    public $marketingApiClient;

    /**
     * @var TransactionlApiClient
     */
    public $transactionalApiClient;

    public function __construct()
    {
        $this->marketingApiClient = new MarkeringApiClient();
        $this->marketingApiClient->setConfig([
            'apiKey' => WP_FA_MAILCHIMP_API_KEY,
            'server' => WP_FA_MAILCHIMP_SERVER_PREFIX
        ]);

        $this->transactionalApiClient = new TransactionalApiClient();
        $this->transactionalApiClient->setApiKey(WP_FA_MAILCHIMP_TRANSACTIONAL_API_KEY);
    }

    public function ping()
    {
        try{
            return $this->marketingApiClient->ping->get();
        }catch(\Exception $e){
            return false;
        }
    }

    public function getListMember($email, $listId = WP_FA_MAILCHIMP_DEFAULT_LIST_ID)
    {
        try{
            return $this->marketingApiClient->lists->getListMember($listId, $email);
        }catch(\GuzzleHttp\Exception\ClientException $e){
            return $this->throwException($e);
        }
    }

    public function addListMember($email, $listId = WP_FA_MAILCHIMP_DEFAULT_LIST_ID, $status = WP_FA_MAILCHIMP_SUBSCRIBER_DEFAULT_STATUS, array $options = [])
    {
        try{

            $data = array(
                "email_address"         => $email, 
                "status"                => $status
            );

            $options = array_merge(array(
                "ip_signup"             => fa_get_ip(),
                "timestamp_signup"      => fa_date_now()
            ), $options);

            $data = array_merge($options, $data);


            return $this->marketingApiClient->lists->addListMember($listId, $data);
        }catch(\GuzzleHttp\Exception\ClientException $e){
            return $this->throwException($e);
        }
    }

    public function updateMember($email, $listId = WP_FA_MAILCHIMP_DEFAULT_LIST_ID, $status = null, $merge_fields = [])
    {
        try{

            $data = [];

            if($status){
                $data['status'] = $status;
            }

            if(!empty($merge_fields)){
                $data['merge_fields'] = $merge_fields;
            }

            $response = $this->marketingApiClient->lists->setListMember($listId,$email, $data);
            return $response;
            
        }catch(\Exception $e){
            return $this->throwException($e);
        }
    }

    public function updateMergeFields($email, array $merge_fields, $listId = WP_FA_MAILCHIMP_DEFAULT_LIST_ID){
        try{
            $response = $this->updateMember($email,$listId,null, $merge_fields);
            return $response;
            
        }catch(\Exception $e){
            return $this->throwException($e);
        }
    }

    public function getAllLists()
    {
        try{
            return $this->marketingApiClient->lists->getAllLists();
        }catch(\Exception $e){
            return $this->throwException($e);
        }
    }

    public function sendTransactionalMail($email, $subject, $html)
    {
        try{
            $message = [
                "from_email" => WP_FA_EMAIL_SENDER_EMAIL,
                "from_name" => WP_FA_EMAIL_SENDER_NAME,
                "subject" => $subject,
                "html" => $html,
                "auto_text" => true,
                "track_opens" => true,
                "track_clicks" => true,
                "to" => [
                    [
                        "email" => $email,
                        "type" => "to"
                    ]
                ]
            ];
            $response = $this->transactionalApiClient->messages->send(["message" => $message]);
            return !empty($response) && $response[0]->status == 'sent';
        }catch(\Exception $e){
            return $this->throwException($e);
        }
        return false;
    }

    public function getDefaultList()
    {
        try{
            return $this->marketingApiClient->lists->getList("asdasd");
        }catch(\Exception $e){
            return $this->throwException($e);
        }
    }


    protected function throwException(\GuzzleHttp\Exception\ClientException $e)
    {
        $response             = $e->getResponse();
        
        if(!$response){
            $e = new MailChimpException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        try{
            $responseBodyAsString = '';
            if($body = $response->getBody()){
                $responseBodyAsString = $body->getContents();
                $data = json_decode($responseBodyAsString, true);
                $e = new MailChimpException($data['title']. ': '.$data['detail'], $e->getCode(), $e->getPrevious(), $data);
            }
        }catch(\Exception $e){
            $e = new MailChimpException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        throw $e;
    }
}

class MailChimpException extends Exception
{
    protected $data = [];

    public function __construct($message = '', $code = 0, Throwable $previous = null, $data = null){
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }
    
    public function getData(){
        return $this->data;
    }
}