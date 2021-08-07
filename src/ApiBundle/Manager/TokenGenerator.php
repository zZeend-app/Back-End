<?php


namespace ApiBundle\Manager;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TokenGenerator
{

    public function createToken($length = 20)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }



}