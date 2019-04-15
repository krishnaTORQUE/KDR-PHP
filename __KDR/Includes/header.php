<?php

if(!defined('ROOT')) require $_SERVER['ERROR_PATH'];

/*
 * Default Header File
 */

/*
 * Minify Payload
 */
if($this->APP['MIN_PAYLOAD'] === true) {

    function PAYLOAD_MINIFY($content) {
        $search = [
            '/\r\n|\r|\n|\t|<!--(.*?)-->/si',
            '/[^\S ]+\<| <|  <|   <|<\s+/si',
            '/\>[^\S ]+|> |>  |>   |\s+>/si',
            '/(\s)+/si',
        ];
        $replace = ['', ' <', '> ', '\\1'];
        $content = preg_replace($search, $replace, $content);
        return trim($content);
    }

    ob_start('PAYLOAD_MINIFY');
}
?>
<!DOCTYPE html{{ DOCTYPE }}>

<html{{ HTML }}>

<head{{ HEAD }}>

<meta charset="{{ CHARSET }}"/>

<meta http-equiv="Content-Type"
      content="{{ TYPE }}"/>

<title>{{ $this->TRIMS($this->MTTR['TITLE']) }}</title>

{( if strlen($this->TRIMS($this->MTTR['DESCRIPTION'])) > 0 )}
<meta name="description"
      content="{{ $this->TRIMS($this->MTTR['DESCRIPTION']) }}"/>
{( endif )}

{( if strlen($this->TRIMS($this->MTTR['KEYWORDS'])) > 0 )}
<meta name="keywords"
      content="{{ $this->TRIMS($this->MTTR['KEYWORDS']) }}"/>
{( endif )}

{( $this->CALL_FUNCS('IN_HEAD') )}

</head>

<body{{ BODY }}>

{( $this->CALL_FUNCS('IN_BODY') )}