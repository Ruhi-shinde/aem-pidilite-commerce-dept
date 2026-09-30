define([], function () {
    'use strict';

    function AssetSelector(options, imsOptions, mfeAssetSelectorOptions) {
        this.errors = [];
        this.options = options || {};
        this.imsOptions = imsOptions || {};
        this.mfeAssetSelectorOptions = mfeAssetSelectorOptions || {};

        if (!this.imsOptions.imsClientId || !this.imsOptions.imsScope) {
            console.warn('Adobe Asset Selector skipped because required IMS values are missing.');
            this.errors.push('Adobe Asset Selector skipped because required IMS values are missing.');
        }
    }

    AssetSelector.prototype.handleErrorsOnRender = function (errors) {
        console.warn('AssetSelector::handleErrorsOnRender', errors);
    };

    AssetSelector.prototype.render = function () {
        if (this.errors.length > 0) {
            this.handleErrorsOnRender(this.errors);
            return;
        }
    };

    return AssetSelector;
});
