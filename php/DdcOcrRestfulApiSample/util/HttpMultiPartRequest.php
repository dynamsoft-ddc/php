<?php
require_once 'FormData.php';

class HttpMultiPartRequest{
    // post multi-part form data
    public static function post($strUrl, $dicHeader, $formData){
        if(is_null($strUrl) || strlen($strUrl) == 0){
            throw new Exception('Url is invalid.');
        }
        
        $strBoundary = 'DdcOrcRestfulApiSample_'.rand(100000, 999999);
        $strNewLine = "\r\n";
        $strBodyData = HttpMultiPartRequest::constructRequestBodyData($formData, $strBoundary, $strNewLine);        
        
        $strHeaders = 'Content-Type: multipart/form-data; boundary='.$strBoundary.$strNewLine.
            'Content-Length: '.((string)strlen($strBodyData)).$strNewLine;
        
        if(is_array($dicHeader)){
            foreach ($dicHeader as $headerKey => $headerValue){                
                $strHeaders .= $headerKey.': '.$headerValue.$strNewLine;
            } 
        }
        
        $arrRequestData = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => $strHeaders,
                'content' => $strBodyData,
            ),
        );
        
        $ctx  = stream_context_create($arrRequestData);
        
        $httpWebResponse = new HttpWebResponse();
        $httpWebResponse->responseData = file_get_contents($strUrl, FALSE, $ctx);
        $httpWebResponse->responseHeaders = $http_response_header;
        
        return $httpWebResponse;
    }
    
    // construct request body data
    private static function constructRequestBodyData($formData, $strBoundary, $strNewLine){
        if (is_null($formData) || !$formData->isValid()) {
            return null;
        }
        
        $bHasItemAdded = FALSE;
        
        $strBoundarySeparator = '--';
        
        $strBodyData = '';        
        
        foreach ($formData->getAll() as $formDataItem){
            if($bHasItemAdded){
                $strBodyData .= $strNewLine;
            }
            
            list($strKey, $value, $strFileName) = $formDataItem;            
            $strKey = is_null($strKey) ? '' : $strKey;
            $value = is_null($value) ? '' : $value;
            
            // write file data
            if(is_string($strFileName) && strlen($strFileName) > 0){
                $strBodyData .= $strBoundarySeparator.$strBoundary.
                    $strNewLine.
                    'Content-Disposition: form-data; name="'.$strKey.'"; filename="'.$strFileName.'"'.
                    $strNewLine.
                    'Content-Type: '.(is_string($value) ? 'text/plain' : 'application/octet-stream').
                    $strNewLine.$strNewLine;
                
                if(is_array($value)){
                    $value = call_user_func_array("pack", array_merge(array("C*"), $value));
                }
                
                $strBodyData .= $value;
            }
            // write key value pair
            else{
                $strBodyData .= $strBoundarySeparator.$strBoundary.
                    $strNewLine.
                    'Content-Disposition: form-data; name="'.$strKey.'"'.
                    $strNewLine.$strNewLine.
                    $value;
            }
            
            $bHasItemAdded = TRUE;
        }
        
        if($bHasItemAdded){
            $strBodyData .= $strNewLine.$strBoundarySeparator.$strBoundary.$strBoundarySeparator.$strNewLine;
        }
        
        return $strBodyData;
    }        
}

class HttpWebResponse{
    public $responseData;
    public $responseHeaders;
}
?>