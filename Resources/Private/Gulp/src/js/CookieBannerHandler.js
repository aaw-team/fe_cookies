/*!
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

(function() {
    "use strict";

    // Return when no banner has been found
    var banners = document.getElementsByClassName('tx_fe_cookies-banner');
    if (banners.length < 1) {
        return;
    } else if (banners.length > 1) {
        throw 'Error: extension fe_cookies does not support multiple banners on the same page.';
    }

    // Function for hiding the banner
    var hideFeCookieBanner = function(banner) {
        if (banner.classList.contains('tx_fe_cookies-banner-position-bottom') || banner.classList.contains('tx_fe_cookies-banner-position-top')) {
            banner.classList.add('tx_fe_cookies-hiding');
            window.setTimeout(function() {
                banner.setAttribute('style', 'display:none');
                banner.classList.remove('tx_fe_cookies-hiding');
            }, 500);
        } else {
            banner.setAttribute('style', 'display:none');
        }
    };

    for(var i = 0; i < banners.length; i++) {
        var banner = banners[i];

        // Check for cookie
        if (AawTeam.feCookies.hasCookie()) {
            // Ok, cookie already set. Hide the banner and return
            hideFeCookieBanner(banners[i]);
            return;
        }

        var acceptButton = banner.getElementsByClassName('tx_fe_cookies-button-accept');
        if (acceptButton.length === 1) {
            acceptButton = acceptButton[0];
            // Register the accept click event
            acceptButton.addEventListener('click', function(event) {
                event.preventDefault();
                AawTeam.feCookies.setCookie();
            });
        }

        var closeButton = banner.getElementsByClassName('tx_fe_cookies-button-close');
        if (closeButton.length === 1) {
            closeButton = closeButton[0];
            // Register the close click event
            closeButton.addEventListener('click', function(event) {
                event.preventDefault();
                hideFeCookieBanner(banner);
            });
        }

        // Hide cookie banner when setCookie event has been fired
        document.addEventListener('setCookie', function() {
            hideFeCookieBanner(banner);
        });

        // Display cookie banner when removeCookie event has been fired
        document.addEventListener('removeCookie', function() {
            banner.setAttribute('style', '');
        });
    }
})();
