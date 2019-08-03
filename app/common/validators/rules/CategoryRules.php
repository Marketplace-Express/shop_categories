<?php
/**
 * User: Wajdi Jurry
 * Date: ٣‏/٨‏/٢٠١٩
 * Time: ٢:٥١ م
 */

namespace app\common\validators\rules;


class CategoryRules implements IValidationRules
{
    /** @var int */
    public $minNameLength = 3;

    /** @var int  */
    public $maxNameLength = 100;

    /** @var bool  */
    public $allowNameWhiteSpace = true;

    /** @var bool  */
    public $allowNameUnderscore = true;

    /** @var int  */
    public $minOrder = 0;

    /** @var  */
    public $maxOrder = 999;

    /** @var bool  */
    public $allowOrderFloat = false;

    /** @var bool  */
    public $allowOrderSign = false;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'minNameLength' => $this->minNameLength,
            'maxNameLength' => $this->maxNameLength,
            'allowWhiteSpace' => $this->allowNameWhiteSpace,
            'allowUnderscore' => $this->allowNameUnderscore,
            'minOrder' => $this->minOrder,
            'maxOrder' => $this->maxOrder,
            'allowOrderFloat' => $this->allowOrderFloat,
            'allowOrderSign' => $this->allowOrderSign
        ];
    }
}
