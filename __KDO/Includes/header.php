<?php
if (!defined('MAIN')) {
    require $_SERVER['ERROR_PATH'];
}

/*
 * Default Header File
 */

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
        $replace = ['', '<', '>', '\\1'];
        $content = preg_replace($search, $replace, $content);
        return trim($content);
    }

    ob_start('PAYLOAD_MINIFY');
}

$this->CALL_FUNCS('BEFORE_HEAD');
?>
<!DOCTYPE html<?php echo $this->TAG_ATTR['DOCTYPE']; ?>>

<html<?php echo $this->TAG_ATTR['HTML']; ?>>

<head<?php echo $this->TAG_ATTR['HEAD']; ?>>

    <meta charset="<?php echo $this->APP['CHARSET']; ?>"/>

    <title><?php echo $this->TRIMS($this->META['TITLE']); ?></title>

    <?php
    echo (strlen($this->META['DESCRIPTION']) > 0) ? '<meta name="description" content="' . $this->TRIMS($this->META['DESCRIPTION']) . '"/>' . PHP_EOL : null;
    echo PHP_EOL;

    echo (strlen($this->META['KEYWORDS']) > 0) ? '<meta name="keywords" content="' . $this->TRIMS($this->META['KEYWORDS']) . '"/>' . PHP_EOL . PHP_EOL : null;
    echo PHP_EOL;

    if (strlen($this->LOAD_TAGS['ICON']['URL']) > 1) {

        if (strlen($this->LOAD_TAGS['ICON']['TYPE']) > 1) {
            echo '<link rel="shortcut icon" type="' . $this->LOAD_TAGS['ICON']['TYPE'] . '" href="' . $this->LOAD_TAGS['ICON']['URL'] . '"/>' . PHP_EOL;
        } else {
            echo '<link rel="shortcut icon" href="' . $this->LOAD_TAGS['ICON']['URL'] . '"/>' . PHP_EOL;
        }
    }

    /*
     * Links to Stylesheet Tags
     */
    foreach ($this->LOAD_TAGS['CSS'] as $css_tags) {
        echo '<link rel="stylesheet" type="text/css" href="' . $css_tags . '"/>' . PHP_EOL;
    }
    unset($css_tags);

    $this->CALL_FUNCS('IN_HEAD');
    ?>

</head>

<body<?php echo $this->TAG_ATTR['BODY']; ?>>

<?php $this->CALL_FUNCS('IN_BODY'); ?>
