<?php

namespace App\Traits;

trait PaginationResponseTrait
{
    public function formatPaginatedResponse($paginator)
    {

        $startPage = max(1, $paginator->currentPage() - 2);
        $endPage   = min($paginator->lastPage(), $startPage + 4);
        $startPage = max(1, $endPage - 4);

        $pageLinks = [];
        for ($i = $startPage; $i <= $endPage; $i++) {
            $pageLinks[] = [
                'page'   => $i,
                'url'    => $paginator->url($i),
                'active' => ($i === $paginator->currentPage())
            ];
        }

        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'total_items'  => $paginator->total(),
            'prev_page'    => $paginator->previousPageUrl(),
            'next_page'    => $paginator->nextPageUrl(),
            'pages'        => $pageLinks,
        ];
    }

}
