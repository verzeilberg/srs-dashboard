import $ from "jquery";

$(document).ready(function() {
    clockUpdate();
    setInterval(clockUpdate, 1000);

    updateServerStatus();
    //setInterval(updateServerStatus, 1000);
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
    var d = addZero(date.getDate());
    var mo = addZero(date.getMonth()+1);
    var y = date.getFullYear();
    var h = addZero(date.getHours());
    var m = addZero(date.getMinutes());
    var s = addZero(date.getSeconds());

    $('.digital-clock').text(d + '-' + mo + '-' + y + ' ' + h + ':' + m + ':' + s);
}

function updateServerStatus()
{
    let hostids = [];
    $('.host').each(function() {
        let dataId = $(this).data("hostid");
        hostids.push(dataId)
    });

    console.log(hostids);

    $.ajax({
        url: '/get_host_ids',
        type: 'POST',
        data: {
            'hostids': hostids
        },
        dataType: 'json',
        async: true,
        success: function (data) {
            console.log(data)
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr);
            console.log(textStatus);
            console.log(errorThrown);
            //alert('Ajax request failed.');
        }
    });


}