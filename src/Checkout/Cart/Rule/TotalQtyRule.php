<?php declare(strict_types=1);

namespace ElgentosTotalQtyInCartRule\Checkout\Cart\Rule;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;
use Shopware\Core\Checkout\Cart\Rule\LineItemScope;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\Framework\Rule\Exception\UnsupportedOperatorException;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class TotalQtyRule
 * @package ElgentosTotalQtyInCartRule\Checkout\Cart\Rule
 */
class TotalQtyRule extends Rule
{
    protected int $count;

    protected string $operator;

    public function __construct(string $operator = self::OPERATOR_EQ, int $count = 0)
    {
        parent::__construct();

        $this->operator = $operator;
        $this->count = $count;
    }

    /**
     * @throws UnsupportedOperatorException
     */
    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof CartRuleScope) {
            return false;
        }

        $goods = $scope->getCart()->getLineItems()->filterGoods();
        if ($this->filter) {
            $context = $scope->getSalesChannelContext();

            $goods = $goods->filter(function (LineItem $lineItem) use ($context) {
                $scope = new LineItemScope($lineItem, $context);

                return $this->filter->match($scope);
            });
        }

        $totalQty = (int) $goods->reduce(function ($carry, LineItem $lineItem): int {
            $carry += $lineItem->getQuantity();
            return $carry;
        }, 0);

        switch ($this->operator) {
            case self::OPERATOR_GTE:
                return $totalQty >= $this->count;

            case self::OPERATOR_LTE:
                return $totalQty <= $this->count;

            case self::OPERATOR_GT:
                return $totalQty > $this->count;

            case self::OPERATOR_LT:
                return $totalQty < $this->count;

            case self::OPERATOR_EQ:
                return $totalQty === $this->count;

            case self::OPERATOR_NEQ:
                return $totalQty !== $this->count;

            default:
                throw new UnsupportedOperatorException($this->operator, self::class);
        }
    }

    public function getConstraints(): array
    {
        return [
            'count' => [new NotBlank(), new Type('int')],
            'operator' => [
                new NotBlank(),
                new Choice(
                    [
                        self::OPERATOR_NEQ,
                        self::OPERATOR_GTE,
                        self::OPERATOR_LTE,
                        self::OPERATOR_EQ,
                        self::OPERATOR_GT,
                        self::OPERATOR_LT,
                    ]
                ),
            ],
        ];
    }

    public function getName(): string
    {
        return 'cartTotalQty';
    }
}
