<?php

class ArrayUtils
{
    /**
     * Maps an array into XML.
     * @param array $data data to transform.
     * @param SimpleXMLElement $xml result of mapping.
     * @param string $idKey designated key of the array that will be added as an attribute, not a child.
     */
    public static function arrayToXml(array $data, SimpleXMLElement &$xml, $idKey = null) {
        foreach ($data as $key => $value) {
            $key = self::hasStringKeys($data) ? $key : 'row';
            if (is_array($value)) {
                $subnode = $xml->addChild("$key");
                self::arrayToXml($value, $subnode, $idKey);
            } else {
                if ($key === $idKey) {
                    $xml->addAttribute("$key", htmlentities($value));
                } else {
                    $xml->addChild("$key", htmlentities($value));
                }
            }
        }
    }

    /**
     * Checks if an array contains string keys.
     * @param array $array
     * @return bool
     */
    public static function hasStringKeys(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
