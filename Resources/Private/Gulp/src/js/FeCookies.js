/*!
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

(function() {
    "use strict";

    // Check for our namespace. Create when not defined.
    window.AawTeam = window.AawTeam || {};

    /**
     * FeCookies is a small API for accessing some usable cookie functions.
     */
    var FeCookies = function(configuration) {
        // Check the configuration
        if (typeof configuration != 'object') {
            configuration = {};
        }

        // "Merge" the configuration with our default values
        this.configuration = {
            name: configuration.name || 'tx_fecookies',
            domain: configuration.domain || null,
            path: configuration.path || '/',
            lifetime: configuration.lifetime || null,
            secure: (configuration.secure) ? true : false
        };

        // Define events
        this.events = {
            setCookie: document.createEvent('event'), // gets called when a cookie has been set
            removeCookie: document.createEvent('event'), // gets called when a cookie has been deleted
        };

        // Initialize events
        this.events.setCookie.initEvent('setCookie', true, false);
        this.events.removeCookie.initEvent('removeCookie', true, false);

        return this;
    };

    /**
     * Gets the getConfiguration
     *
     * @returns {object} configuration
     */
    FeCookies.prototype.getConfiguration = function() {
        return this.configuration;
    };

    /**
     * Gets the cookie name
     *
     * @returns {text} name of the cookie
     */
    FeCookies.prototype.getCookieName = function() {
        return this.configuration.name;
    };

    /**
     * Sets the cookie
     *
     * @returns {boolean} whether the cookie has been set or not
     */
    FeCookies.prototype.setCookie = function(options) {
        if (this.hasCookie()) {
            // Cookie has not been set yet so simply return false
            return false;
        }

        var cookie = this.configuration.name + '=1'; // @TODO: implement values

        // Check for domain
        if (this.configuration.domain) {
            cookie = cookie + '; domain=' + this.configuration.domain;
        }

        // Allways add the path
        cookie = cookie + '; path=' + this.configuration.path;

        // Check for lifetime
        if (this.configuration.lifetime) {
            cookie = cookie + '; expires=' + new Date(Date.now() + this.configuration.lifetime * 1000).toUTCString();
        }

        // Check for secure flag
        if (this.configuration.secure) {
            cookie = cookie + '; secure=true';
        }

        // Now set the cookie!
        document.cookie = cookie;

        // And fire the setCookie event
        document.dispatchEvent(this.events.setCookie);

        return true;
    };

    /**
     * Returns whether the cookie has been set or not
     *
     * @returns {boolean}
     */
    FeCookies.prototype.hasCookie = function() {
        return document.cookie.match(this.configuration.name);
    };

    /**
     * Removes the cookie
     *
     * @returns {boolean} whether the cookie has been removed or not
     */
    FeCookies.prototype.removeCookie = function() {
        if (!this.hasCookie()) {
            return false;
        }

        var cookie = this.configuration.name + '=';

        if (this.configuration.domain) {
            cookie = cookie + '; domain=' + this.configuration.domain;
        }

        cookie = cookie + '; path=' + this.configuration.path;
        cookie = cookie + '; expires=Thu, 01 Jan 1970 00:00:00 GMT';

        document.cookie = cookie;

        // Fire the removeCookie event
        document.dispatchEvent(this.events.removeCookie);

        return true;
    };

    // Create new instance of FeCookies and bind it to the AawTeam namespace
    AawTeam.feCookies = new FeCookies(typeof AawTeam.fe_cookies_configuration === 'object' ? AawTeam.fe_cookies_configuration : {});
})();
