<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\SortieRepository;

class Archives
{


    private SortieRepository $repository;

    /**
     * @param SortieRepository $repository
     */
    public function __construct(SortieRepository $repository)
    {
        $this->repository = $repository;
    }


    public function recupArchive (){
      $sorties = $this->repository ->findAll();

      foreach ($sorties){

      }


    }

}