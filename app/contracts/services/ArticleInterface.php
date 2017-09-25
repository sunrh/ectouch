<?php

namespace app\contracts\services;

/**
 * Interface ArticleInterface
 * @package app\contracts\services
 */
interface ArticleInterface
{
    /**
     * @param $condition
     * @return mixed
     */
    public function all($condition);

    /**
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * @param $id
     * @return mixed
     */
    public function detail($id);

    /**
     * @param $data
     * @return mixed
     */
    public function update($data);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);
}