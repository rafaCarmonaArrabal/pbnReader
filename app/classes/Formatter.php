<?php

namespace App\Classes;

class Formatter
{
    public function __construct()
    {

    }

    /**
     * @param string $line
     * @param bool $isUtf8
     * @param array|null $search
     * @return false|string[]
     */
    public function stringToArray(string $line, bool $isUtf8, array $search = null)
    {
        if (!$isUtf8)
            $line = utf8_encode($line);
        $line = is_null($search) ? \trim(\str_replace('"', '', $line)) : \trim(\str_replace($search, '', $line));
        $line = \preg_replace('/\s+ /', ' ', $line);
        $line = explode(' ', $line);

        return $line;
    }

    /**
     * @param $file
     * @return \SplFileObject
     * This function is the slowest.
     */
    public function getFile($file)
    {
        return new \SplFileObject($file);
    }
}
