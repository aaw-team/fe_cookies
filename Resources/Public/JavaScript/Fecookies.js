/*!
 * Copyright 2018 Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
!function(){"use strict";window.AawTeam=window.AawTeam||{};var e=function(e){return"object"!=typeof e&&(e={}),this.configuration={name:e.name||"tx_fecookies",domain:e.domain||null,path:e.path||"/",lifetime:e.lifetime||null,secure:!0===e.secure||null===e.secure&&null!==window.location.protocol.match(/^https/)},this.events={setCookie:document.createEvent("event"),removeCookie:document.createEvent("event")},this.events.setCookie.initEvent("setCookie",!0,!1),this.events.removeCookie.initEvent("removeCookie",!0,!1),this};e.prototype.getConfiguration=function(){return this.configuration},e.prototype.getCookieName=function(){return this.configuration.name},e.prototype.setCookie=function(e){if(this.hasCookie())return!1;var o=this.configuration.name+"=1";return this.configuration.domain&&(o=o+"; domain="+this.configuration.domain),o=o+"; path="+this.configuration.path,this.configuration.lifetime&&(o=o+"; expires="+new Date(Date.now()+1e3*this.configuration.lifetime).toUTCString()),this.configuration.secure&&(o+="; secure=true"),document.cookie=o,document.dispatchEvent(this.events.setCookie),!0},e.prototype.hasCookie=function(){return document.cookie.match(this.configuration.name)},e.prototype.removeCookie=function(){if(!this.hasCookie())return!1;var e=this.configuration.name+"=";return this.configuration.domain&&(e=e+"; domain="+this.configuration.domain),e=e+"; path="+this.configuration.path,e+="; expires=Thu, 01 Jan 1970 00:00:00 GMT",document.cookie=e,document.dispatchEvent(this.events.removeCookie),!0},AawTeam.feCookies=new e("object"==typeof AawTeam.fe_cookies_configuration?AawTeam.fe_cookies_configuration:{})}();