<?php
require_once 'util'.DIRECTORY_SEPARATOR.'Configuration.php';
require_once 'util'.DIRECTORY_SEPARATOR.'Comm.php';
require_once 'util'.DIRECTORY_SEPARATOR.'FormData.php';
require_once 'util'.DIRECTORY_SEPARATOR.'HttpMultiPartRequest.php';

// sample entry
function main(){
    // setup ocr api key
    $dicHeader = array('x-api-key' => Configuration::$strApiKey);
    
    // 1. upload file
    echo '-----------------------------------------------------------------------';
    Comm::echoLine('1. Upload file...');
    
    $formData = new FormData();
    $formData->append('method', Comm::$enumOcrFileMethod['upload']);
    $formData->append('file', Comm::getFileData('example.jpg'), 'example.jpg');
    
    try {
        $httpWebResponse = HttpMultiPartRequest::post(Configuration::$strOcrBaseUri, $dicHeader, $formData);
        $restfulApiResponse = Comm::parseHttpWebResponseToRestfulApiResult($httpWebResponse, Comm::$enumOcrFileMethod['upload']);
        $strFileName = Comm::handleRestfulApiResponse($restfulApiResponse, Comm::$enumOcrFileMethod['upload']);
    } catch (Exception $ex) {
        Comm::echoLine($ex->getMessage());
        return;
    }    
    
    if(is_null($strFileName)){
        return;
    }
    
    // 2. recognize the uploaded file
    Comm::echoLine('');
    Comm::echoLine('-----------------------------------------------------------------------');
    Comm::echoLine('2. Recognize the uploaded file...');
    
    $formData->clear();
    $formData->append("method", Comm::$enumOcrFileMethod['recognize']);
    $formData->append("file_name", $strFileName);
    $formData->append("language", "eng");
    $formData->append("output_format", "UFormattedTxt");
    $formData->append("page_range", "1-10");
    
    $strFileName = NULL;
    
    try {
        $httpWebResponse = HttpMultiPartRequest::post(Configuration::$strOcrBaseUri, $dicHeader, $formData);
        $restfulApiResponse = Comm::parseHttpWebResponseToRestfulApiResult($httpWebResponse, Comm::$enumOcrFileMethod['recognize']);
        $strFileName = Comm::handleRestfulApiResponse($restfulApiResponse, Comm::$enumOcrFileMethod['recognize']);
    } catch (Exception $ex) {
        Comm::echoLine($ex->getMessage());
        return;
    }    
    
    if(is_null($strFileName)){
        return;
    }
    
    // 3. download the recognized file
    Comm::echoLine('');
    Comm::echoLine('-----------------------------------------------------------------------');
    Comm::echoLine('3. Download the recognized file...');   
    
    $formData->clear();
    $formData->append("method", Comm::$enumOcrFileMethod['download']);
    $formData->append("file_name", $strFileName);
    
    try {
        $httpWebResponse = HttpMultiPartRequest::post(Configuration::$strOcrBaseUri, $dicHeader, $formData);
        $restfulApiResponse = Comm::parseHttpWebResponseToRestfulApiResult($httpWebResponse, Comm::$enumOcrFileMethod['download']);
        Comm::handleRestfulApiResponse($restfulApiResponse, Comm::$enumOcrFileMethod['download']);
    } catch (Exception $ex) {
        Comm::echoLine($ex->getMessage());
    }    
}

main();

?>