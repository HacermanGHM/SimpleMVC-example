<?php

namespace application\controllers;
use application\models\Note;

/**
 * Контроллер для домашней страницы
 */
class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    /**
     * @var string Название страницы
     */
    
    /**
     * @var string Пусть к файлу макета 
     */
    public string $layoutPath = 'main.php';
      
    /**
     * Выводит на экран страницу "Домашняя страница"
     */
    public function indexAction()
    {
        $noteModel = new Note();
        $articles = $noteModel->getList(10)['results'];
        
        // Используем методы модели Note
        foreach ($articles as $article) {
            $article->categoryName = $noteModel->getCategoryNameForId($article->categoryId);
            $article->subcategoryName = $noteModel->getSubcategoryNameForId($article->subcategoryId);
            $article->authors = $noteModel->getAuthorsForArticle($article->id);
        }
        
        $this->view->addVar('articles', $articles);
        $this->view->render('homepage/index.php');
    }
}

