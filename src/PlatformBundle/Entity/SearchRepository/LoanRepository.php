<?php

namespace PlatformBundle\Entity\SearchRepository;

use FOS\ElasticaBundle\Repository;

class LoanRepository extends Repository
{
    public function search($q = '')
    {
        if ($q == '') {
            $baseQuery = new \Elastica\Query\MatchAll();
        } else {
            $baseQuery = new \Elastica\Query\Bool;
            $baseQuery->addShould(new \Elastica\Query\Term(array('id' => intval($q))));
            $baseQuery->addShould(new \Elastica\Query\Term(array('amount' => floatval($q))));
            $baseQuery->addShould(new \Elastica\Query\MatchPhrasePrefix('description', $q));
        }

        $query = \Elastica\Query::create($baseQuery);
        $count = $this->finder->createPaginatorAdapter($query, array('search_type' => 'count'))->getTotalHits();

        $query->addSort(array('id' => array('order' => 'asc')));
        $query->setSize($count);

        return $this->find($query);
    }

    public function searchAdvanced($q = '')
    {
        if ($q == '') {
            $baseQuery = new \Elastica\Query\MatchAll();
        } else {
            $baseQuery = new \Elastica\Query\Match();
            $baseQuery->setFieldQuery('description', $q);
            $baseQuery->setFieldFuzziness('description', 0.7);
            $baseQuery->setFieldMinimumShouldMatch('description', '80%');
        }

        $boolFilter = new \Elastica\Filter\BoolFilter();

        $dateFrom = new \DateTime();
        $dateFrom->sub(new \DateInterval('P7D'));

        $dateTo = new \DateTime();
        $dateTo->add(new \DateInterval('P1D'));

        $boolFilter->addMust(new \Elastica\Filter\Range('createdAt',
            array(
                'gte' => \Elastica\Util::convertDate($dateFrom->getTimestamp()),
                'lte' => \Elastica\Util::convertDate($dateTo->getTimestamp())
            )
        ));

        /*
         * $boolFilter->addMust(
         *     new \Elastica\Filter\Term(array('isPublished' => true))
         * );
         * $boolFilter->addMust(
         *     new \Elastica\Filter\Terms('isPublished', array('1', '2', '3'))
         * );
         */

        /*
         * $baseQuery = new \Elastica\Filter\Bool();
         * $baseQuery->addShould(
         *     new \Elastica\Filter\Term(array('id' => intval($q)))
         * );
         * $baseQuery->addShould(
         *     new \Elastica\Filter\Term(array('amount' => floatval($q)))
         * );
         * $filtered = new \Elastica\Query\Filtered();
         * $filtered->setFilter($baseQuery);
         * return $this->finder->find($filtered);
         */

        /*
         * $baseQuery  = new \Elastica\Query\Bool;
         * $idQueryTerm = new \Elastica\Query\Term;
         * $idQueryTerm->setTerm('id', intval($q));
         * $baseQuery->addShould($idQueryTerm);
         */

        $filtered = new \Elastica\Query\Filtered($baseQuery, $boolFilter);
        $query = \Elastica\Query::create($filtered);

        $query->addSort(array('id' => array('order' => 'asc')));
        $query->setSize(1);

        return $this->find($query);
    }
}
