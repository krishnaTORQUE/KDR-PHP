<?php
/*
 * Default Header File
 */
if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

/*
 * Minify Payload
 */
if ($this->APP['MIN_PAYLOAD'] === true) {

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
<!DOCTYPE html<?php echo $this->TAG_ATTR['DOCTYPE']; ?>>

<html<?php echo $this->TAG_ATTR['HTML']; ?>>

<head<?php echo $this->TAG_ATTR['HEAD']; ?>>

    <meta charset="<?php echo $this->APP['CHARSET']; ?>"/>

    <meta http-equiv="Content-Type"
          content="<?php echo $this->META['CONTENT_TYPE']; ?>"/>

    <title><?php echo $this->TRIMS($this->META['TITLE']); ?></title>

    <?php
    echo (strlen($this->META['DESCRIPTION']) > 0) ? '<meta name="description" content="' . $this->TRIMS($this->META['DESCRIPTION']) . '"/>' . PHP_EOL . ' ' : null;

    echo (strlen($this->META['KEYWORDS']) > 0) ? '<meta name="keywords" content="' . $this->TRIMS($this->META['KEYWORDS']) . '"/>' . PHP_EOL . PHP_EOL : null;

    $this->CALL_FUNCS('IN_HEAD');
    ?>

</head>

<body<?php echo $this->TAG_ATTR['BODY']; ?>>

<?php $this->CALL_FUNCS('IN_BODY'); ?>
