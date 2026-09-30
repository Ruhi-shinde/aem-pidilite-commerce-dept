define(['MicroFrontend_AssetSelector', 'jquery'], function (mfeAssetSelector) {
    'use strict';

    function hasRequiredConfig(imsConfig) {
        return !!(imsConfig && imsConfig.imsClientId && imsConfig.imsScope && imsConfig.imsApiKey);
    }

    function registerAssetsSelectorsAuthService(imsConfig) {
        if (!imsConfig || imsConfig.imsEnabled) {
            return;
        }

        if (!hasRequiredConfig(imsConfig)) {
            console.warn('Adobe Asset Selector auth skipped because required IMS values are not configured.');
            return;
        }

        var imsProps = {
            imsClientId: imsConfig.imsClientId,
            apiKey: imsConfig.imsApiKey,
            imsScope: imsConfig.imsScope,
            env: imsConfig.env,
            redirectUrl: window.location.href,
            modalMode: true,
            featureSet: ['upload', 'collections', 'detail-panel', 'advisor'],
            adobeImsOptions: {
                modalSettings: {
                    allowOrigin: window.location.origin
                },
                useLocalStorage: true
            }
        };

        mfeAssetSelector.registerAssetsSelectorsAuthService(imsProps);
    }

    function initializeAssetSelectorAuth() {
        var startTime = Date.now();
        var checkImsConfig = setInterval(function () {
            if (window.imsConfig || Date.now() - startTime > 3000) {
                clearInterval(checkImsConfig);
                if (window.imsConfig) {
                    registerAssetsSelectorsAuthService(window.imsConfig);
                }
            }
        }, 1000);
    }

    return initializeAssetSelectorAuth;
});
