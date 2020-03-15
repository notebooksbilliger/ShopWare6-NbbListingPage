describe('Filter config panel', () => {
    beforeEach(() => {
        cy.activateShopwareTheme();

        cy.loginViaApi()
            .then(() => {
                cy.visit('/admin#/sw/category/index');
            });
    });

    it('Contains filter configuration panel', () => {
        cy.get('.sw-tree-item__toggle:first').click();
        cy.get('.sw-tree-item__children > .sw-tree-item:nth-child(1)').click();
        cy.get('.sw-custom-field-set-renderer').contains('Filter configuration').should('exist');
        cy.get('.sw-custom-field-set-renderer').contains('Filter configuration').click();
    });

    it('Contains properties to choose from and persists', () => {
        cy.get('.sw-tree-item__toggle:first').click();
        cy.get('.sw-tree-item__children > .sw-tree-item:nth-child(1)').click();

        cy.contains('Available filters').parent().next().click();

        cy.get('.sw-select-result-list__item-list > li:nth-child(1)').click();
        cy.get('.sw-select-result-list__item-list > li:nth-child(2)').click();
        cy.get('.sw-select-result-list__item-list > li:nth-child(3)').click();

        cy.get('.sw-button-process__content').contains('Save').click();
    });
});
