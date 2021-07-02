import { Application } from 'src/core/shopware';
import '../core/component/elgentos-totalqty-rule';

Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('cartTotalQty', {
        component: 'elgentos-totalqty-rule',
        label: 'Total Quantity In Cart',
        scopes: ['cart']
    });

    return ruleConditionService;
});
