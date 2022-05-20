/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/global.scss';

import $ from 'jquery';

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
require('bootstrap/js/dist/tooltip');
require('bootstrap/js/dist/popover');

$(document).ready(function() {
    clockUpdate();
    setInterval(clockUpdate, 1000);
})

function clockUpdate() {
    var date = new Date();
    function addZero(x) {
        if (x < 10) {
            return x = '0' + x;
        } else {
            return x;
        }
    }

    var d = addZero(date.getDay());
    var mo = addZero(date.getMonth());
    var y = date.getFullYear();
    var h = addZero(date.getHours());
    var m = addZero(date.getMinutes());
    var s = addZero(date.getSeconds());

    $('.digital-clock').text(d + '-' + mo + '-' + y + ' ' + h + ':' + m + ':' + s);
}