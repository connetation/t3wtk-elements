<?php

declare(strict_types=1);

namespace Connetation\Elements\Controller;

use Doctrine\DBAL\ParameterType;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsController]
final class WikiModuleController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly UriBuilder $backendUriBuilder,
    ) {}

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wiki');
        $qb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $wikis = $qb->select('*')
            ->from('tx_conn_elements_domain_model_wiki')
            ->orderBy('title', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $wikis = array_values(array_filter(
            $wikis,
            fn(array $wiki): bool => $this->isPidAccessible((int)$wiki['pid'])
        ));

        $currentUrl = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki');
        $newWikiUri = (string)$this->backendUriBuilder->buildUriFromRoute('record_edit', [
            'edit'      => ['tx_conn_elements_domain_model_wiki' => [0 => 'new']],
            'returnUrl' => $currentUrl,
        ]);

        foreach ($wikis as &$wiki) {
            $uid = (int)$wiki['uid'];
            $wiki['editUri'] = (string)$this->backendUriBuilder->buildUriFromRoute('record_edit', [
                'edit'      => ['tx_conn_elements_domain_model_wiki' => [$uid => 'edit']],
                'returnUrl' => $currentUrl,
            ]);
            $wiki['pagesUri'] = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki.pages', [
                'wikiUid' => $uid,
            ]);
            $wiki['categoriesUri'] = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki.categories', [
                'wikiUid' => $uid,
            ]);
        }
        unset($wiki);

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->assignMultiple([
            'wikis'      => $wikis,
            'newWikiUri' => $newWikiUri,
        ]);
        return $moduleTemplate->renderResponse('Backend/WikiModule');
    }

    public function categoriesAction(ServerRequestInterface $request): ResponseInterface
    {
        $wikiUid  = (int)($request->getQueryParams()['wikiUid'] ?? 0);
        $indexUrl = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki');
        $catsUrl  = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki.categories', ['wikiUid' => $wikiUid]);

        $wikiQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wiki');
        $wikiQb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $wiki = $wikiQb->select('*')
            ->from('tx_conn_elements_domain_model_wiki')
            ->where($wikiQb->expr()->eq('uid', $wikiQb->createNamedParameter($wikiUid, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchAssociative();

        if (is_array($wiki) && !$this->isPidAccessible((int)$wiki['pid'])) {
            $wiki = false;
        }

        $catQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wikicategory');
        $catQb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $categories = $catQb->select('*')
            ->from('tx_conn_elements_domain_model_wikicategory')
            ->where($catQb->expr()->eq('wiki', $catQb->createNamedParameter($wikiUid, ParameterType::INTEGER)))
            ->orderBy('title', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $categories = array_values(array_filter(
            $categories,
            fn(array $category): bool => $this->isPidAccessible((int)$category['pid'])
        ));

        $pid           = $wiki['pid'] ?? 0;
        $newCategoryUri = (string)$this->backendUriBuilder->buildUriFromRoute('record_edit', [
            'edit'    => ['tx_conn_elements_domain_model_wikicategory' => [-$pid => 'new']],
            'defVals' => ['tx_conn_elements_domain_model_wikicategory' => ['wiki' => $wikiUid]],
            'returnUrl' => $catsUrl,
        ]);

        foreach ($categories as &$cat) {
            $cat['editUri'] = (string)$this->backendUriBuilder->buildUriFromRoute('record_edit', [
                'edit'      => ['tx_conn_elements_domain_model_wikicategory' => [(int)$cat['uid'] => 'edit']],
                'returnUrl' => $catsUrl,
            ]);
        }
        unset($cat);

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->assignMultiple([
            'wiki'           => $wiki,
            'categories'     => $categories,
            'newCategoryUri' => $newCategoryUri,
            'indexUrl'       => $indexUrl,
        ]);
        return $moduleTemplate->renderResponse('Backend/WikiCategories');
    }

    public function pagesAction(ServerRequestInterface $request): ResponseInterface
    {
        $wikiUid  = (int)($request->getQueryParams()['wikiUid'] ?? 0);
        $indexUrl = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki');
        $pagesUrl = (string)$this->backendUriBuilder->buildUriFromRoute('conn_elements_wiki.pages', ['wikiUid' => $wikiUid]);

        // ─── Wiki directory record ────────────────────────────────
        $wikiQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wiki');
        $wikiQb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $wiki = $wikiQb->select('*')
            ->from('tx_conn_elements_domain_model_wiki')
            ->where($wikiQb->expr()->eq('uid', $wikiQb->createNamedParameter($wikiUid, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchAssociative();

        if (is_array($wiki) && !$this->isPidAccessible((int)$wiki['pid'])) {
            $wiki = false;
        }

        // ─── Pages (including hidden) ─────────────────────────────
        $pageQb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_wikipage');
        $pageQb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $pages = $pageQb
            ->select('p.*', 'c.title as category_title')
            ->from('tx_conn_elements_domain_model_wikipage', 'p')
            ->leftJoin('p', 'tx_conn_elements_domain_model_wikicategory', 'c', 'c.uid = p.category AND c.deleted = 0')
            ->where($pageQb->expr()->eq('p.wiki', $pageQb->createNamedParameter($wikiUid, ParameterType::INTEGER)))
            ->orderBy('p.sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $pages = array_values(array_filter(
            $pages,
            fn(array $page): bool => $this->isPidAccessible((int)$page['pid'])
        ));

        $pid        = $wiki['pid'] ?? 0;
        $newPageUri = (string)$this->backendUriBuilder->buildUriFromRoute('record_edit', [
            'edit'      => ['tx_conn_elements_domain_model_wikipage' => [-$pid => 'new']],
            'defVals'   => ['tx_conn_elements_domain_model_wikipage' => ['wiki' => $wikiUid]],
            'returnUrl' => $pagesUrl,
        ]);

        foreach ($pages as &$page) {
            $page['editUri'] = (string)$this->backendUriBuilder->buildUriFromRoute('record_edit', [
                'edit'      => ['tx_conn_elements_domain_model_wikipage' => [(int)$page['uid'] => 'edit']],
                'returnUrl' => $pagesUrl,
            ]);
        }
        unset($page);

        $moduleTemplate = $this->moduleTemplateFactory->create($request);
        $moduleTemplate->assignMultiple([
            'wiki'       => $wiki,
            'pages'      => $pages,
            'newPageUri' => $newPageUri,
            'indexUrl'   => $indexUrl,
        ]);
        return $moduleTemplate->renderResponse('Backend/WikiPages');
    }

    /**
     * Whether the given pid lies inside the current backend user's webmounts.
     * Admins always have access.
     */
    private function isPidAccessible(int $pid): bool
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if ($backendUser === null || $backendUser->isAdmin()) {
            return true;
        }
        return $backendUser->isInWebMount($pid) !== null;
    }
}
