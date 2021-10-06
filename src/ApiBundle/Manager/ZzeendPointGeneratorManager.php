<?php


namespace ApiBundle\Manager;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ZzeendPointGeneratorManager
{

    public function createZzeendPoint()
    {
        $chars = '0123456789abcdefghijklMNOPQRSTUVWXYZmnopqrstuvwxyzABCDEFGKL';
        srand((double)microtime()*1000000);
        $i = 0;
        $zZeendPoint = '';

        while($i <= 255){
            $num = rand() % 76;
            $tmp = substr($chars, $num, 1);

            $zZeendPoint = $zZeendPoint.$tmp;
            $i++;
        }


        return $zZeendPoint;

        return $result;
    }



}