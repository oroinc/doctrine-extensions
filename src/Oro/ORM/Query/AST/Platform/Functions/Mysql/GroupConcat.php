<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Mysql;

use Doctrine\ORM\Query\AST\Node;
use Oro\ORM\Query\AST\Functions\String\GroupConcat as Base;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class GroupConcat extends PlatformFunctionNode
{
    /**
     * @url http://sysmagazine.com/posts/181666/
     *
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $isDistinct = !empty($this->parameters[Base::DISTINCT_KEY]);
        $result = 'GROUP_CONCAT(' . ($isDistinct ? 'DISTINCT ' : '');

        $fields = array();
        /** @var Node[] $pathExpressions */
        $pathExpressions = $this->parameters[Base::PARAMETER_KEY];
        foreach ($pathExpressions as $pathExp) {
            $fields[] = $pathExp->dispatch($sqlWalker);
        }

        $result .= sprintf('%s', implode(', ', $fields));

        if (!empty($this->parameters[Base::ORDER_KEY])) {
            $result .= ' ' . $sqlWalker->walkOrderByClause($this->parameters[Base::ORDER_KEY]);
        }

        if (isset($this->parameters[Base::SEPARATOR_KEY])) {
            $result .= ' SEPARATOR ' . $sqlWalker->walkStringPrimary($this->parameters[Base::SEPARATOR_KEY]);
        }

        $result .= ')';

        return $result;
    }
}
