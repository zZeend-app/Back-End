<?php


namespace ApiBundle\Manager;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileUploader
{

    public function upload($file, $uploadDir, $dataType)
    {

        $filename = uniqid() . '_' . $file->getClientOriginalName();

        $file->move($uploadDir . '/'.$dataType, $filename);

        return $filename;
    }


}