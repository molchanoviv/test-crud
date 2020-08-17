<?php
/**
 * Copyright (c) Diffco US, Inc
 */

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use App\Manager\ArticleManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as OASecurity;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;

/**
 * App\Controller\Api\V1\ArticleController
 *
 * @author Diffco US, Inc. <support@diffco.us>
 */
class ArticleController extends AbstractFOSRestController
{
    private ArticleManager $articleManager;

    /**
     * ArticleController constructor.
     */
    public function __construct(ArticleManager $articleManager)
    {
        $this->articleManager = $articleManager;
    }

    /**
     * Get articles list
     *
     * @Rest\Get("/articles", name="get_articles_list")
     * @OA\Tag(name="Article")
     * @OA\Parameter(
     *     name="orderBy",
     *     in="query",
     *     description="Order param",
     *     @OA\Schema(
     *         type="string",
     *         enum={"id", "title", "body", "createdAt", "updatedAt"}
     *     )
     * )
     * @OA\Parameter(
     *     name="orderDestination",
     *     in="query",
     *     description="Order destination",
     *     @OA\Schema(
     *         type="string",
     *         enum={"ASC", "DESC"}
     *     )
     * )
     * @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer"), description="limit")
     * @OA\Parameter(name="offset", in="query", @OA\Schema(type="integer"), description="offset")
     * @OA\Response(
     *     response=200,
     *     description="Returns found articles",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Article::class, groups={"rest"}))
     *     )
     * )
     * @Rest\View(serializerGroups={"rest"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function getArticleList(Request $request): array
    {
        $orderByParams = [];
        if ($request->query->has('orderBy')) {
            $orderByParam = $request->get('orderBy', 'id');
            if (!in_array($orderByParam, Article::getFields(), true)) {
                throw new \InvalidArgumentException(sprintf('Field %s doesn\'t exist in the article', $orderByParam));
            }
            $destination = strtoupper($request->get('orderDestination', 'ACS'));
            if (!in_array($destination, ['ASC', 'DESC'])) {
                throw new \InvalidArgumentException('Unknown sorting destination');
            }
            $orderByParams[$orderByParam] = $destination;
        }

        return $this->articleManager->findBy(
            [],
            $orderByParams,
            $request->query->has('limit') ? (int) $request->query->get('limit') : null,
            $request->query->has('offset') ? (int) $request->query->get('offset') : null
        );
    }

    /**
     * Get article by id
     *
     * @Rest\Get("/articles/{id}", name="get_article")
     * @ParamConverter("Article", class="App\Entity\Article")
     * @OA\Tag(name="Article")
     * @OA\Response(
     *     response=200,
     *     description="Returns an article by id",
     *     @OA\JsonContent(
     *         ref=@Model(type=Article::class, groups={"rest"})
     *     )
     * )
     * @Rest\View(serializerGroups={"rest"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function getArticle(Article $article): Article
    {
        return $article;
    }

    /**
     * Create article
     *
     * @Rest\Post("/articles", name="add_article")
     * @OA\RequestBody(
     *     description="article body",
     *     required=true,
     *     @OA\JsonContent(
     *         ref=@Model(type=ArticleType::class)
     *     )
     * )
     * @OA\Tag(name="Article")
     * @OA\Response(
     *     response=200,
     *     description="Returns an article",
     *     @OA\JsonContent(
     *         ref=@Model(type=Article::class, groups={"rest"})
     *     )
     * )
     * @Rest\View(serializerGroups={"rest"})
     * @Security("is_granted('ROLE_USER')")
     * @OASecurity(name="Bearer")
     *
     * @return mixed
     *
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws \JsonException
     */
    public function postArticle(Request $request)
    {
        $article = $this->articleManager->createNew();
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit(json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR), false);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->save($article);

            return $article;
        }

        return $form;
    }

    /**
     * Edit article
     *
     * @Rest\Put("/articles/{id}", name="edit_article")
     * @ParamConverter("Article", class="App\Entity\Article")
     * @OA\Tag(name="Article")
     * @OA\RequestBody(
     *     description="article body",
     *     required=true,
     *     @OA\JsonContent(
     *         ref=@Model(type=ArticleType::class)
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns an article",
     *     @OA\JsonContent(
     *         ref=@Model(type=Article::class, groups={"rest"})
     *     )
     * )
     * @Rest\View(serializerGroups={"rest"})
     * @Security("is_granted('ROLE_USER')")
     * @OASecurity(name="Bearer")
     *
     * @return mixed
     *
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @throws AlreadySubmittedException
     * @throws LogicException
     */
    public function putArticle(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article, ['method' => Request::METHOD_PUT]);
        $form->submit(json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR), false);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManager->save($article);

            return $article;
        }

        return $form;
    }

    /**
     * Delete article
     *
     * @Rest\Delete("/articles/{id}", name="delete_article")
     * @ParamConverter("Article", class="App\Entity\Article")
     * @OA\Tag(name="Article")
     * @OA\Response(
     *     response=200,
     *     description="Returns nothing"
     * )
     * @Rest\View(serializerGroups={"rest"})
     * @Security("is_granted('ROLE_USER')")
     * @OASecurity(name="Bearer")
     *
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     */
    public function deleteArticle(Article $article): void
    {
        $this->articleManager->remove($article);
        $this->articleManager->flush();
    }
}
