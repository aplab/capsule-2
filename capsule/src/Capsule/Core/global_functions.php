<?php
/* implementation of my_strtok() in PHP */

/*
 * Description: string my_strtok(string $string, string $delimiter).
 *
 * Function my_strtok() splits a string ($string) into smaller
 * strings (tokens), with each token being delimited by the delimiter
 * string ($delimiter), considering string variables and comments
 * in the $string argument. Note that the comparision is case-insensitive.
 *
 * Returns FALSE if there are no tokens left.
 * Does not return empty tokens.
 * Does not return the "delimiter" command as a token.
 *
 * Usage:
 * The first call to my_strtok() uses the $string and $delimiter arguments.
 * Every subsequent call to my_strtok() needs no arguments at all, or only
 * the $delimiter argument to use, as it keeps track of where it is in the
 * current string. To start over, or to tokenize a new string you simply
 * call my_strtok() with the both arguments again to initialize it.
 * The delimiter can be changed by the command "delimiter new_delimiter" in
 * the $string argument (the command is case-insensitive).
 *
 * Example:
 *  $res = my_strtok($query, $delimiter);
 *  while ($res) {
 *      echo "token = $res<br>";
 *      $res = my_strtok();
 *  }
 *
 * Author: Andrey Adaikin, IVA Team, <IVATeam@gmail.com>
 * @version $Revision: 1.3 $, $Date: 2005/09/28 $
 */

function my_strtok($string = NULL, $delimiter = NULL)
{

    static $str;            // lower case $string (equals to strtolower($string))
    static $str_original;   // stores $string argument
    static $len;            // length of the $string
    static $curr_pos;       // current position in the $string
    static $match_pos;      // position where the $delimiter is a substring of the $string
    static $delim;          // lower case $delimiter (equals to strtolower($delimiter))

    if (NULL === $delimiter) {
        if (NULL !== $string) {
            $delim = strtolower($string);
            $match_pos = -1;
        }
    } else {
        if (!is_string($string) || !is_string($delimiter)) {
            return FALSE;
        }
        $str_original = $string;
        $str = strtolower($str_original);
        $len = strlen($str);
        $curr_pos = 0;
        $match_pos = -1;
        $delim = strtolower($delimiter);
    }

    if ($curr_pos >= $len) {
        return FALSE;
    }

    if ("" == $delim) {
        $delim = ";";
        $match_pos = -1;
    }

    $dlen = strlen($delim);
    $result = FALSE;

    for ($i = $curr_pos; $i < $len; ++$i) {
        if ($match_pos < $i) {
            $match_pos = strpos($str, $delim, $i);
            if (FALSE === $match_pos) {
                $match_pos = $len;
            }
        }

        if ($i == $match_pos) {
            if ($i != $curr_pos) {
                $result = trim(substr($str_original, $curr_pos, $i - $curr_pos));
                if (strncasecmp($result, 'delimiter', 9) == 0 && (strlen($result) == 9 || FALSE !== strpos(" \t", $result{9}))) {
                    $delim = trim(strtolower(substr($result, 10)));
                    if ("" == $delim) {
                        $delim = ";";
                    }
                    $match_pos = -1;
                    $result = FALSE;
                }
            }
            $i += $dlen;
            if ($match_pos < 0) {
                $dlen = strlen($delim);
            }
            $curr_pos = $i--;
            if ("" === $result) {
                $result = FALSE;
            }
            if (FALSE !== $result) {
                break;
            }
        } else if ($str{$i} == "'") {
            for ($j = $i + 1; $j < $len; ++$j) {
                if ($str{$j} == "\\") ++$j;
                else if ($str{$j} == "'") break;
            }
            $i = $j;
        } else if ($str{$i} == "\"") {
            for ($j = $i + 1; $j < $len; ++$j) {
                if ($str{$j} == "\\") ++$j;
                else if ($str{$j} == "\"") break;
            }
            $i = $j;
        } else if ($i < $len - 1 && $str{$i} == "/" && $str{$i + 1} == "*") {
            $j = $i + 2;
            while ($j) {
                $j = strpos($str, "*/", $j);
                if (!$j || $str{$j - 1} != "\\") {
                    break;
                }
                ++$j;
            }
            if (!$j) {
                break;
            }
            $i = $j + 1;
        } else if ($str{$i} == "#") {
            $j = strpos($str, "\n", $i + 1) or strpos($str, "\r", $i + 1);
            if (!$j) {
                break;
            }
            $i = $j;
        } else if ($i < $len - 2 && $str{$i} == "-" && $str{$i + 1} == "-" && FALSE !== strpos(" \t", $str{$i + 2})) {
            $j = strpos($str, "\n", $i + 3) or strpos($str, "\r", $i + 1);
            if (!$j) {
                break;
            }
            $i = $j;
        } else if ($str{$i} == "\\") {
            ++$i;
        }
    }

    if (FALSE === $result && $curr_pos < $len) {
        $result = trim(substr($str_original, $curr_pos));
        if (strncasecmp($result, 'delimiter', 9) == 0 && (strlen($result) == 9 || FALSE !== strpos(" \t", $result{9}))) {
            $delim = trim(strtolower(substr($result, 10)));
            if ("" == $delim) {
                $delim = ";";
            }
            $match_pos = -1;
            $dlen = strlen($delim);
            $result = FALSE;
        }
        $curr_pos = $len;
        if ("" === $result) {
            $result = FALSE;
        }
    }

    return $result;
}

/**
 * Needed a method to normalize a virtual path that could handle .. references
 * that go beyond the initial folder reference. So I created the following.
 * http://php.net/manual/ru/function.realpath.php#112367
 *
 * Will convert /path/to/test/.././..//..///..///../one/two/../three/filename
 * to ../../one/three/filename
 *
 * Paths returned by normalize_path() contain no
 * (back)slash at position -1 (ending)
 *
 * @param string $path
 * @return string
 */
function normalize_path($path)
{
    $parts = array();// Array to build a new path from the good parts
    $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
    $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
    $segments = explode('/', $path);// Collect path segments
    foreach ($segments as $segment) {
        if ($segment != '.') {
            $test = array_pop($parts);
            if (is_null($test)) {
                $parts[] = $segment;
            } else if ($segment == '..') {
                if ($test == '..') {
                    $parts[] = $test;
                }
                if ($test == '..' || $test == '') {
                    $parts[] = $segment;
                }
            } else {
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    return rtrim(join('/', $parts), '/');
}

/**
 * http://php.net/manual/ru/function.realpath.php#84012
 * Because realpath() does not work on files that do not
 * exist, I wrote a function that does.
 * It replaces (consecutive) occurences of / and \\ with
 * whatever is in DIRECTORY_SEPARATOR, and processes /. and /.. fine.
 *
 * Paths returned by absolute_path() contain no
 * (back)slash at position -1 (ending)
 *
 * @param string $path
 * @return string
 */
function absolute_path($path)
{
    $path = normalize_path($path);
    $tmp = explode('/', $path);
    $parts = array();
    array_walk($tmp, function ($v, $k) use (& $parts) {
        if (strlen($v) || !$k) {
            $parts[] = $v;
        }
    });
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return join('/', $absolutes);
}