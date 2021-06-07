<?php
namespace common\components;
/**
 *  Custom build array helper methods.
 */
class ArrayHelpers
{

    /**
     *  Checks if key contains a certain value.
     *
     *  @param   array    $arr    The array to search.
     *  @param   string   $key    The key to search.
     *  @param   string   $mtch   The value to match.
     *  @return  bool             Returns bool.
     */
    public function multiKeyExists($arr, $key = '', $mtch = '')
    {
        if (is_array($arr)) {
            // is in base array?
            if (array_key_exists($key , $arr) && $arr[$key] == $mtch || empty($mtch)) {
                return;
            }

            // check arrays contained in this array
            foreach ($arr as $element) {
                if (is_array( $element )) {
                    if (self::multiKeyExists( $element , $key , $mtch ) ) {
                        return;
                    }
                }
            }
        }
        return false;
    }

    /**
     *  Set new value by key and index.
     *
     *  @param    array    $arr   The array to search.
     *  @param    string   $key   The key to search.
     *  @param    string   $ind   The key by index.
     *  @param    string   $val   The value to set.
     *  @return   array           Returns the new array.
     */
    public function setValueByKey($arr , $key , $ind , $val)
    {
        if (is_array($arr) && $ind < count($arr)) {
            $arr[$ind][$key] = $val;
        }
        return $arr;
    }

    /**
     *  Set new value by empty keys.
     *
     *  @param    array    $arr   The array to search.
     *  @param    string   $key   The key to search.
     *  @param    string   $val   The value to set.
     *  @return   array           Returns the new array.
     */
    public function setValueByEmptyKey($arr , $key , $val)
    {
        if (is_array($arr)) {
            foreach ($arr as &$item) {
                if ($item[$key] === "" || $item[$key] === " " || $item[$key] === false || $item[$key] === null) {
                    $item[$key] = $val;
                }
            }
        }
        return $arr;
    }

    /**
     *  Set a default value if there is no value present
     *
     *  @param    array    $arr    The array to search.
     *  @param    string   $key    The key to search.
     *  @param    string   $match   The value to match.
     *  @param    string   $val    The value to use.
     *  @return   array            Returns the new array.
     */
    public function setDefaultValue($arr , $key , $ind , $match , $val) 
    {
        if ($ind === false || $mtch == false) {
            $arr = self::setValueByEmptyKey($arr , $key , $val);
        } elseif (!self::multiKeyExists($arr , $key , $match)) {
            $arr = self::setValueByKey($arr, $key, $ind, $val);
        }
        return $arr;
    }

    /**
     *  Multidimensional array strip tags cleaning.
     *
     *  @source   https://stackoverflow.com/questions/32614584/how-can-i-remove-all-html-tags-from-an-array
     *  @param    array     $input
     *  @param    bool      $easy                 einfache Konvertierung für 1-Dimensionale Arrays ohne Objecte
     *  @param    boolean   $throwByFoundObject
     *  @return   array
     *  @throws   Exception
     */
    public function cleanUpArray(array $input, $easy = false, $throwByFoundObject = true)
    {
        if ($easy) {
            $output = array_map(function($v) {
                return trim(strip_tags($v));
            }, $input);
        } else {
            $output = $input;

            foreach ($output as $key => $value) {
                if (is_string($value)) {
                    $output[$key] = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/', '', $value);
                    $output[$key] = trim(preg_replace('~[\r\n]+~', ' ', $value));
                    $output[$key] = html_entity_decode(mb_convert_encoding(stripslashes($value), "HTML-ENTITIES", 'UTF-8'));
                    $output[$key] = trim( strip_tags($value) );
                } elseif (is_array($value)) {
                    $output[$key] = self::cleanUpArray($value);
                } elseif (is_object($value) && $throwByFoundObject) {
                    throw new Exception('Object found in Array by key ' . $key);
                }
            }
        }
        return $output;
    }
}
