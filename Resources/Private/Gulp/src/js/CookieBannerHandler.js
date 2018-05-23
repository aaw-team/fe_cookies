/*!
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

(function() {
    "use strict";

    // Return when no banner has been found
    if (!document.getElementById('tx_fe_cookies-banner')) {
        return;
    }

    // Function for hiding the banner
    var hideFeCookieBanner = function() {
        if (document.getElementById('tx_fe_cookies-banner').classList.contains('tx_fe_cookies-banner-bottom') || document.getElementById('tx_fe_cookies-banner').classList.contains('tx_fe_cookies-banner-top')) {
            document.getElementById('tx_fe_cookies-banner').classList.add('tx_fe_cookies-hiding');
            window.setTimeout(function() {
                document.getElementById('tx_fe_cookies-banner').setAttribute('style', 'display:none');
                document.getElementById('tx_fe_cookies-banner').classList.remove('tx_fe_cookies-hiding');
            }, 500);
        } else {
            document.getElementById('tx_fe_cookies-banner').setAttribute('style', 'display:none');
        }
    };

    // Check for cookie
    if (AawTeam.feCookies.hasCookie()) {
        // Ok, cookie already set. Hide the banner and return
        hideFeCookieBanner();

        return;
    }

    // Register the accept click event
    document.getElementById('tx_fe_cookies-button-accept').addEventListener('click', function(event) {
        event.preventDefault();

        AawTeam.feCookies.setCookie();
    });

    // Register the close click event
    if (document.getElementById('tx_fe_cookies-button-close')) {
        document.getElementById('tx_fe_cookies-button-close').addEventListener('click', hideFeCookieBanner);
    }

    // Hide cookie banner when setCookie event has been fired
    document.addEventListener('setCookie', hideFeCookieBanner);

    // Display cookie banner when removeCookie event has been fired
    document.addEventListener('removeCookie', function() {
        document.getElementById('tx_fe_cookies-banner').setAttribute('style', '');
    });
})();
