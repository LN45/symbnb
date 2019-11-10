<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class Pagination
{
    private $entityClass;

    private $limit = 10;

    private $currentPage = 1;

    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getPages()
    {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié d'entité sur laquelle nous devons paginer, utiliser la méthode setEntityClass !");
        }
        // 1) connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);
        return $pages;
    }

    public function getData()
    {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié d'entité sur laquelle nous devons paginer, utiliser la méthode setEntityClass !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2) Demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // 3) Renvoyer les éléments en question
        return $data;
    }


    public function getPage()
    {
        return $this->currentPage;
    }

    public function setPage(int $page)
    {
        $this->currentPage = $page;
        return $this;
    }


    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }


}