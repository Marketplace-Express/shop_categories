<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٨‏/٢٠١٩
 * Time: ٣:٢٢ م
 */

namespace app\common\validators\rules;


class AttributeRules implements IValidationRules
{
    /** @var int  */
    public $minAttrNameLength = 3;

    /** @var int  */
    public $maxAttrNameLength = 50;

    /** @var bool  */
    public $allowAttrNameWhiteSpace = true;

    /** @var bool  */
    public $allowAttrNameUnderscore = true;

    /** @var int  */
    public $minValNameLength = 3;

    /** @var int  */
    public $maxValNameLength = 50;

    /** @var bool  */
    public $allowValueNameWhiteSpace = false;

    /** @var bool  */
    public $allowValueNameUnderscore = false;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'minAttrNameLength' => $this->minAttrNameLength,
            'maxAttrNameLength' => $this->maxAttrNameLength,
            'allowAttrNameWhiteSpace' => $this->allowAttrNameWhiteSpace,
            'allowAttrNameUnderscore' => $this->allowAttrNameUnderscore,
            'minValNameLength' => $this->minValNameLength,
            'maxValNameLength' => $this->maxValNameLength,
            'allowValNameWhiteSpace' => $this->allowValueNameWhiteSpace,
            'allowValNameUnderscore' => $this->allowValueNameUnderscore
        ];
    }
}
