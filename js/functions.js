/**
 * Description: Creates a cookie for the user to stay logged in. This is used to query the database, indirectly.
 * @param name
 * @param value
 * @param days
 */
function createCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 * Description: Get a cookie status
 * @param name
 * @returns {string|null}
 */
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

/**
 * Description: Erases a cookie
 * @param name
 */
function eraseCookie(name) {
    createCookie(name, "", -1);
}

/**
 * Description: This is used in routing.php, so get-links is not duplicated in the url.
 * @returns {string}
 */
function getUrlPathName()
{
    var url = new URL(window.location.href);

    var pathName = url.pathname;

    var removeGetLinks = pathName.replace("/get-links/", "");

    return removeGetLinks;
}


function lazyLoading()
{

    $(document).ready(function(){
        var limit = 4;
        var start = 0;
        var action = 'inactive';

        function load_link_data(limit, start)
        {
            $.ajaxSetup({
                async: true
            });

            $("#loader").addClass('sr-onl');

            $.ajax({
                url:"fetch.php",
                method:"POST",
                data:{limit:limit, start:start},
                cache:false,
                success:function(data)
                {
                    $('#load_data').append(data);

                    if(data === '')
                    {
                        $("#pleaseWait").addClass('d-none');
                        $('#load_data_message').html("<button type='button' class='btn btn-info'>No data found</button>");
                        action = 'active';
                    }
                    else
                    {
                        $("#pleaseWait").addClass('d-none');

                        $('#load_data_message').html(" <div class='text-center'> <button type='button' class='btn text-center btn-info'>Loading, please wait...</button></div>");
                        action = 'inactive';
                    }
                }
            });
        }

        if(action === 'inactive')
        {
            action = 'active';
            load_link_data(limit, start);
        }

        $(window).scroll(function(){
            console.log($(window).scrollTop() + $(window).height());
            console.log($("#load_data").height());
            if(($(window).scrollTop() + $(window).height() > $("#load_data").height()) && action === 'inactive')
            {
                action = 'active';
                start = start + limit;
                console.log(start);
                setTimeout(function(){
                    load_link_data(limit, start);
                }, 1000);
            }
        });
    });
}