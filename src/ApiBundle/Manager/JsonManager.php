<?php


namespace ApiBundle\Manager;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class JsonManager
{
    private $defaultCount;
    private $defaultOffset;

    public function __construct($defaultCount = 1000, $defaultOffset = 0)
    {
        $this->defaultCount = $defaultCount;
        $this->defaultOffset = $defaultOffset;

    }

    /**
     * function called to check and return the information from webapp
     * return [] json
     */
    public function getInclude($data)
    {
        $json = [];

        $json["include"] = [];
        $json["filters"] = [];
        $json["filters"]["include"] = [];
        $json["filters"]["exclude"] = [];
        $json["order"] = [];

        $json["include"] = array_key_exists("include", $data) ? $data["include"] : [];

        //check filters
        if (array_key_exists("filters", $data)) {
            if (array_key_exists("include", $data["filters"])) {
                $json["filters"]["include"] = $data["filters"]["include"];
            }
            if (array_key_exists("exclude", $data["filters"])) {
                $json["filters"]["exclude"] = $data["filters"]["exclude"];
            }
        }

        if (array_key_exists("order", $data)) {
            $json["order"] = $data["order"];
        }

        if (array_key_exists("return", $data)) {
            $json["return"] = $data["return"];
        }

        return $json;

    }

    //function the set the queryLimit for the count and pagination
    public function setQueryLimit(QueryBuilder $qb, $filtersInclude)
    {

        if (array_key_exists("offset", $filtersInclude)) {
            $qb = $qb->setFirstResult(intval($filtersInclude["offset"]));
        } else {
            $qb = $qb->setFirstResult($this->defaultOffset);
        }

        //set maximun return transaction
        if (array_key_exists("count", $filtersInclude)) {
            $qb = $qb->setMaxResults(intval($filtersInclude["count"]));
        } else {
            $qb = $qb->setMaxResults($this->defaultCount);
        }
        //$qb->getQuery()->getResult();
        $paginator = new Paginator($qb, $fetchJoinCollection = true);

        $entities = [];

        foreach ($paginator as $post) {
            $entities[] = $post;
        }


        return $entities;

    }

}