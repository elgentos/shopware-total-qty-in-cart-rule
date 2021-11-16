import '../core/component/elgentos-totalqty-rule';

Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('cartTotalQty', {
        component: 'elgentos-totalqty-rule',
        label: 'Total Quantity In Cart',
        scopes: ['cart']
    });

    return ruleConditionService;
});
