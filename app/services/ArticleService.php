<?php

namespace app\services;

use app\contracts\services\ArticleInterface;
use app\repositorys\ArticleRepository;

class ArticleService implements ArticleInterface
{
    private $article;

    public function __construct(ArticleRepository $article)
    {
        $this->article = $article;
    }

    /**
     * @param $condition
     * @return mixed
     */
    public function all($condition = [])
    {
        return $this->article->all($condition);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {

    }

    /**
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        return $this->article->show($id);

    }

    /**
     * @param $data
     * @return mixed
     */
    public function update($data)
    {

    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {

    }
}