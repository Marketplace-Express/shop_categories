<?php
/**
 * User: Wajdi Jurry
 * Date: ١٥‏/٨‏/٢٠١٩
 * Time: ٤:٥٨ م
 */

namespace app\common\graphql\scalarTypes;


use app\common\validators\MongoIdValidator;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Phalcon\Validation;

class MongoIdType extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     *
     */
    public function serialize($value)
    {
        return $value;
    }

    public function validate($value)
    {
        $validator = new Validation();
        $validator->add(
            'value',
            new MongoIdValidator()
        );
        return $validator->validate(['value' => $value]);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        if (count($this->validate($value))) {
            throw new Error("Cannot represent following value as MongoId: " . Utils::printSafe($value));
        }
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param Node $valueNode
     * @param mixed[]|null $variables
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (!$variables instanceof StringValueNode) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }

        if (count($this->validate($valueNode->value))) {
            throw new Error("Not a valid MongoId string", [$valueNode]);
        }

        return $valueNode->value;
    }
}
