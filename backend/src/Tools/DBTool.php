<?php

namespace App\Tools;

/**
 * Class DBTool
 * @package App\Tools
 */
class DBTool{

    private $doctrine;
    private $em;

    /**
     * DBTool constructor.
     *
     * @param $doctrine
     */
    public function __construct($doctrine){
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
    }

    public function getEntityManager(){
        return $this->em;
    }

    /**
     *
     * Metoda która wywołuje presist dla podanego elementu w bazie i jeśli nie ma potrzeby to nie trzeba flushować
     *
     * @param $repository
     *
     * @param bool $flush
     */
    public function insertData($repository, bool $flush = true):void{
        $this->em->persist($repository);

        if($flush){
            $this->em->flush();
        }
    }

    /**
     * Metoda która usuwa podany element z bazy i jeśli nie ma potrzeby to nie trzeba flushować
     *
     * @param $repository
     *
     * @param bool $flush
     */
    public function removeData($repository, bool $flush = true):void{
        $this->em->remove($repository);

        if($flush){
            $this->em->flush();
        }
    }

    /**
     *
     * Metoda która otrzymuje repozytorium(Gdzie ma coś znaleźć) i parametry do specyfikacji targetu , limit to limit pobranych query a startRow to offset
     *
     * @param $repository
     *
     * @param array $params
     *
     * @param int|null $limit
     *
     * @param int|null $startRow
     *
     * @return array[Objects]
     */
    public function findBy($repository,array $params, int $limit = null, int $startRow = null): array
    {
        $result = $this->doctrine->getRepository($repository)
                        ->findBy($params,null,$limit,$startRow);

        return $result;
    }

    /**
     * Metoda ta od powyzszej różni się tym że używa NamedQuery(Na podstawie stworzonych w Entity) i jest wykorzystywana dla bardziej złożonych zapytań
     *
     * @param $repository
     *
     * @param string $nameQuery
     *
     * @param array $params
     *
     * @param int|null $limit
     *
     * @param int|null $startRow
     *
     * @return array[Objects]
     *
     */
    public function findBySQL($repository, string $nameQuery, array $params, int $limit = null, int $startRow = null): array
    {
        $result = $this->doctrine->getRepository($repository)
            ->createNamedQuery($nameQuery)
            ->setParameters($params);

        if($startRow != null && $limit != null){
            $result = $result->setFirstResult(($startRow-1)*$limit);
        }

        if($limit != null){
            $result = $result->setMaxResults($limit);
        }

        $end = $result->getResult();

        return $end;
    }

}
