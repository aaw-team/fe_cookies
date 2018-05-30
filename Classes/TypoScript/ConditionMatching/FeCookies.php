<?php
namespace AawTeam\FeCookies\TypoScript\ConditionMatching;

/*
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * FeCookies
 */
class FeCookies extends \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition
{
    /**
     * {@inheritDoc}
     * @see \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition::matchCondition()
     */
    public function matchCondition(array $conditionParameters)
    {
        return ConditionMatcher::evaluateCondition($conditionParameters);
    }
}
