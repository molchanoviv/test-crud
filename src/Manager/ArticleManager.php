<?php
/**
 * Copyright (c) Diffco US, Inc
 */

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Article;

/**
 * App\Manager\articleManager
 */
class ArticleManager extends AbstractManager
{
    public function getEntityClassName(): string
    {
        return Article::class;
    }
}
