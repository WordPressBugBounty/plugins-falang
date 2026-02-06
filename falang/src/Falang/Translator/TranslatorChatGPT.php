<?php
/**
 * The translator for ChatGPT
 *
 * @link       www.faboba.com
 * @since      1.4.0
 *
 * @package    Falang
 */
namespace Falang\Translator;

class TranslatorChatGPT extends TranslatorDefault {

    function __construct()
    {
        parent::__construct();

        $this->token= Falang()->get_model()->get_option('chatgpt_key');

        $this->script = 'translatorChatGPT.js';
    }

    public function installScripts ($from,$to)
    {
        parent::installScripts($from,$to);
        //the from-to is not in lower case for ChatGPT to get the language name in the language array
        $inline_script = "var translator = {'from' : '".$from. "','to' : '".$to. "'};\n";
        $inline_script .= "var chatgptKey = '".$this->token."';\n";
        wp_add_inline_script('translatorService',$inline_script,'before');
    }

    /*
     * @from 1.4.0
     *
     */
    public function translate($text,$targetLocale){
        //isocode are put in the js and get here
        $targetLanguageCode = $this->languageCodeToISO($targetLocale);
        $targetLanguageName = $this->languageCodeToName($targetLocale);
        $sourceLanguageCode = $this->languageCodeToISO(Falang()->get_model()->get_default_language()->locale);

        //$this->token don't work here
        $token= Falang()->get_model()->get_option('chatgpt_key');
        $chat_model= Falang()->get_model()->get_option('chatgpt_model','gpt-4.1-mini');//gpt-5 //gpt-4o-mini

        $prompt=[];
        $prompt[0] = [
            "role" => "system",
           "content" => "You are a professional translation assistant. Detect the source language automatically. Translate the user's text into the language ".$targetLanguageName.". Preserve tone, meaning, punctuation, emoji, and inline formatting. Return only the translated text without commentary, labels, or quotes."
        ];
        $prompt[1] = [
          "role" => "user",
            "content_type" => "text",
            "content" => $text
        ];

        //not translate url yet
        $url = "https://api.openai.com/v1/chat/completions";

        $postfields = array();
        $postfields['messages'] = $prompt;//an array of text is necessary
        $postfields['model'] = $chat_model;

        $header = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Authorization: Bearer '.$token;

        $data = json_encode($postfields);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //Set curl options relating to SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        if( ! $result = curl_exec($ch))
        {
            $error = curl_error($ch);
            $response          = new \stdClass();
            $response->success = false;
            $response->data = 'error unknown';
            return $response;
        }
        curl_close($ch);

        //decode en tableau pour une meilleur récupération du résultat
        $resultDecoded = json_decode($result,true);

        $response          = new \stdClass();

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response->success= false;
            $response->data = json_last_error_msg();
            return $response;
        }

        //cas ou plus de crédit
        if (isset($resultDecoded['error'])){
            $response->success= false;
            $response->data = $resultDecoded['error']['message'];
            return $response;
        }

        //decode the translation returned
        try {
            $response->success = true;
            // Récupération du texte traduit
            $response->data = $resultDecoded['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $response->success= false;
            $response->data  = $e->getMessage();
        }

        return $response;
    }
}