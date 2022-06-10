import $ from "jquery";

$(document).ready(function() {
    clockUpdate();
    setInterval(clockUpdate, 1000);

    updateServerStatus();
    setInterval(updateServerStatus, 5000);

    switchTabs();
    setInterval(switchTabs, 20000);

})



    function switchTabs() {
        var $panes = $(".tab-pane");
        var time = 5000;
        $panes.each(function(i, pane) {
            setTimeout( function(){
                let paneId = $(pane).attr('id');
                $('.nav-link').removeClass('active').attr('tabindex', '-1');
                console.log($('#nav-'+paneId));
                $('#nav-'+paneId).addClass('active').removeAttr('tabindex');
                $(".tab-pane").removeClass('active').removeClass('show').addClass('fade');
                $(pane).addClass('active').addClass('show');

            }, time)
            time += 5000;
        });
    }


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
    $.ajax({
        url: '/get_host_ids',
        type: 'POST',
        data: {
            'hostids': hostids
        },
        dataType: 'json',
        async: true,
        success: function (data) {
            $.each( data, function( i, l ){
                if(jQuery.inArray(i, hostids) !== -1) {
                    $('.hostid-' + i).addClass('text-bg-success');
                    $('.hostid-' + i).removeClass('text-bg-danger');

                    $('.hostid-' + i).children('.card-body').children('p.card-text').children('svg').removeClass('fa-face-angry');
                    $('.hostid-' + i).children('.card-body').children('p.card-text').children('svg').addClass('fa-face-smile');
                    $('.hostid-' + i).children('.card-body').children('p.card-text').children('span.problem').text('');
                } else {
                    let color = getSeverityColor(l.severity);
                    let smiley = getSeveritySmiley(l.severity);
                    $('.hostid-' + i).removeClass('text-bg-success');
                    $('.hostid-' + i).addClass('text-bg-'+color);

                   //$('.hostid-' + i).children('.card-body').children('p.card-text').children('svg').removeClass('fa-face-smile');

                    $('.hostid-' + i).children('.card-body').children('p.card-text').children('svg').removeClass (function (index, className) {
                        return (className.match (/(^|\s)fa-face\S+/g) || []).join(' ');
                    });

                    $('.hostid-' + i).children('.card-body').children('p.card-text').children('svg').addClass('fa-face-'+smiley);
                    $('.hostid-' + i).children('.card-body').children('p.card-text').children('span.problem').text(l.problem);
                }
            });

            $('.loading').hide();
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr);
            console.log(textStatus);
            console.log(errorThrown);
            //alert('Ajax request failed.');
        }
    });
}
function getSeverityColor(severity)
{
    let color = '';
    switch(severity) {
        case '1':
            color = 'info';
            break;
        case '2':
            color = 'warning';
            break;
        case '3':
            color = 'danger';
            break;
        case '4':
            color = 'error';
            break;
        default:
            color = 'success';
    }

    return color;
}

function getSeveritySmiley(severity)
{
    let smiley = '';
    switch(severity) {
        case '1':
            smiley = 'frown';
            break;
        case '2':
            smiley = 'flushed';
            break;
        case '3':
            smiley = 'angry';
            break;
        case '4':
            smiley = 'dizzy';
            break;
        default:
            smiley = 'grin-wide';
    }

    return smiley;
}