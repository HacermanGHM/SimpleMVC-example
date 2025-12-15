<?php
namespace application\models;
/* 
 * class Note
 * 
 * 
 */

class Note extends BaseExampleModel {
    
    public string $tableName = "notes";
    
    public string $orderBy = 'publicationDate ASC';
    
    public ?int $id = null;
    
    public $title = null;
    
    public $content = null;
    
    public $publicationDate = null;

    public $categoryId = null;

    public $subcategoryId = null;
    
    
    public function insert()
    {
        $sql = "INSERT INTO $this->tableName (title, content, publicationDate) VALUES (:title, :content, :publicationDate)"; 
        $st = $this->pdo->prepare ( $sql );
        $st->bindValue( ":publicationDate", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
        $st->bindValue( ":title", $this->title, \PDO::PARAM_STR );

        $st->bindValue( ":content", $this->content, \PDO::PARAM_STR );
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
{
    $sql = "UPDATE $this->tableName SET 
            publicationDate = :publicationDate, 
            title = :title, 
            content = :content,
            categoryId = :categoryId,
            subcategoryId = :subcategoryId 
            WHERE id = :id";  
    
    $st = $this->pdo->prepare($sql);
    $st->bindValue(":publicationDate", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
    $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
    $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
    $st->bindValue(":categoryId", $this->categoryId, \PDO::PARAM_INT);
    $st->bindValue(":subcategoryId", $this->subcategoryId, \PDO::PARAM_INT);
    $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
    $st->execute();
}

    // Добавляем метод для получения названия категории
    public function getCategoryName()
    {
        if ($this->categoryId) {
            $sql = "SELECT name FROM categories WHERE id = :id";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":id", $this->categoryId, \PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch();
            return $row ? $row['name'] : 'Без категории';
        }
        return 'Без категории';
    }

    public function getSubategoryName()
    {
        if ($this->categoryId) {
            $sql = "SELECT name FROM subcategories WHERE id = :id";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":id", $this->subcategoryId, \PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch();
            return $row ? $row['name'] : 'Без подкатегории';
        }
        return 'Без подкатегории';
    }

    public function getCategoryNameForId($categoryId)
{
    if ($categoryId) {
        $sql = "SELECT name FROM categories WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $categoryId, \PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        return $row ? $row['name'] : null;
    }
    return null;
}

public function getSubcategoryNameForId($subcategoryId)
{
    if ($subcategoryId) {
        $sql = "SELECT name FROM subcategories WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $subcategoryId, \PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        return $row ? $row['name'] : null;
    }
    return null;
}

// Получить авторов статьи (пользователей)
public function getAuthors($articleId = null)
{
    $articleId = $articleId ?? $this->id;
    if (!$articleId) return [];
    
    $sql = "SELECT u.* FROM users u 
            JOIN article_authors aa ON u.id = aa.user_id 
            WHERE aa.article_id = :article_id 
            ORDER BY u.login";
    $st = $this->pdo->prepare($sql);
    $st->bindValue(":article_id", $articleId, \PDO::PARAM_INT);
    $st->execute();
    return $st->fetchAll(\PDO::FETCH_OBJ);
}

// Добавить автора (пользователя)
public function addAuthor($userId)
{
    $sql = "INSERT IGNORE INTO article_authors (article_id, user_id) VALUES (:article_id, :user_id)";
    $st = $this->pdo->prepare($sql);
    $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
    $st->bindValue(":user_id", $userId, \PDO::PARAM_INT);
    return $st->execute();
}

// Удалить автора
public function removeAuthor($userId)
{
    $sql = "DELETE FROM article_authors WHERE article_id = :article_id AND user_id = :user_id";
    $st = $this->pdo->prepare($sql);
    $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
    $st->bindValue(":user_id", $userId, \PDO::PARAM_INT);
    return $st->execute();
}

// Получить авторов статьи (только имена)
public function getAuthorNames()
{
    $authors = $this->getAuthors();
    $names = [];
    foreach ($authors as $author) {
        $names[] = $author->login;
    }
    return implode(', ', $names);
}

public function getAuthorsForArticle($articleId)
{
    $sql = "SELECT u.* FROM users u 
            JOIN article_authors aa ON u.id = aa.user_id 
            WHERE aa.article_id = :article_id 
            ORDER BY u.login";
    $st = $this->pdo->prepare($sql);
    $st->bindValue(":article_id", $articleId, \PDO::PARAM_INT);
    $st->execute();
    return $st->fetchAll(\PDO::FETCH_OBJ);
}

}

