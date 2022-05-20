import $ from "jquery";

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

function udateServerStatus()
{

}