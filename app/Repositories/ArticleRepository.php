<?php

namespace App\Repositorys;

use App\Contracts\Repositories\ArticleInterface;
use App\Models\Article;

/**
 * Class ArticleRepository
 * @package App\Repositorys
 */
class ArticleRepository implements ArticleInterface
{
    /**
     * @param $condition
     * @return static[]
     */
    public function all($condition = [])
    {
        $articles = Article::find()
            ->where(['is_open' => 1])
            ->orderBy([
                'add_time' => SORT_DESC,
                'article_id' => SORT_DESC,
            ])
            ->asArray()
            ->all();

        foreach ($articles as $key => $article) {
            $articles[$key]['add_time'] = date('Y-m-d', $article['add_time']);
        }

        return $articles;
    }

    /**
     * @param $data
     */
    public function create($data)
    {

    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function show($id)
    {
        return Article::find()
            ->asArray()
            ->one($id);
    }

    /**
     * @param $data
     */
    public function update($data)
    {

    }

    /**
     * @param $id
     */
    public function delete($id)
    {

    }

}