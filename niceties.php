<?php
/**
 * Nice non-oo helper functions.
 * Try not to add to this if you can help it.
 * Chance are if you need to add a helper function,
 * there's a nicer way to do the thing
 * 
 * @author Skye
 */

 /**
  * Tokenize a string for easy pascal case slugs.
  * Turns TokenizableString into tokenizable_string
  * @param string $input The string to transform
  * @return string The input string in Pascal case
  * @author Skye
  */
 function str_tokenize($input){
    return \ltrim(\strtolower(\preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input)), '_');
 }