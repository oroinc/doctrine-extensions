<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Oro\ORM\Query\AST\Functions\String\GroupConcat as Base;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class GroupConcat extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $isDistinct = !empty($this->parameters[Base::DISTINCT_KEY]);
        $result = 'array_to_string(array_agg(' . ($isDistinct ? 'DISTINCT ' : '');

        $fields = array();
        /** @var Node[] $pathExpressions */
        $pathExpressions = $this->parameters[Base::PARAMETER_KEY];
        foreach ($pathExpressions as $pathExp) {
            $fields[] = $pathExp->dispatch($sqlWalker);
        }

        if (count($fields) === 1) {
            $concatenatedFields = reset($fields);
        } else {
            $platform = $sqlWalker->getConnection()->getDatabasePlatform();
            $concatenatedFields = call_user_func_array(array($platform, 'getConcatExpression'), $fields);
        }
        $result .= $concatenatedFields;

        if (!empty($this->parameters[Base::ORDER_KEY])) {
            $result .= ' ' . $sqlWalker->walkOrderByClause($this->parameters[Base::ORDER_KEY]);
        }

        $result .= ')';

        if (isset($this->parameters[Base::SEPARATOR_KEY])) {
            $separator = $this->parameters[Base::SEPARATOR_KEY];
        } else {
            $separator = ',';
        }

        $result .= ', ' . $sqlWalker->walkStringPrimary($separator);

        $result .= ')';

        return $result;
    }
}
