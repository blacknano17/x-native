<?php
/**
 * Class Security & String Sanitization
 */
class safeString
{
    public function __construct()
    {
    }

    public function safeSql($string)
    {
        return addslashes((string)$string);
    }

    public function safeSqlParam($string)
    {
        return "'" . addslashes((string)$string) . "'";
    }

    public function safeXss($string)
    {
        $safestring = $this->AlNum((string)$string);
        return $this->safeHtmlEntiti($safestring);
    }

    public function cleanSpaceString($string)
    {
        return preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/", "_", (string)$string);
    }

    public function safeHtmlEntiti($string)
    {
        return htmlentities((string)$string, ENT_QUOTES, 'UTF-8');
    }

    public function safeString($string)
    {
        return htmlspecialchars(strip_tags((string)$string), ENT_QUOTES, 'UTF-8');
    }

    public function trimString($string)
    {
        return trim((string)$string);
    }

    public function AlNum($string)
    {
        return preg_replace('~[^\p{L}\p{N} !?@#$%*().,"\[\] ]++~u', ' ', (string)$string);
    }
}
