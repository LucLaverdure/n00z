let timelock = null;

$(document).ready(function() {

    function extractYear(str) {
        const start = str.lastIndexOf('(') + 1;
        const end = str.lastIndexOf(')');
        return str.substring(start, end);
    }
    function extractText(str) {
        const yearStart = str.lastIndexOf('(');
        return str.substring(0, yearStart).trim();
    }


    function updateClock() {
        var now = new Date();
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        var timeString = hours + ':' + minutes + ':' + seconds;
        $('#time').text(timeString);
    }

    setInterval(updateClock, 1000);

    function load_all(key) {

        if (key !== '') {
            // Algotron
            $.ajax({
                cache: false,
                url: 'https://nolife.app/api/v2/',
                data: {msg: key},
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                success: function (response) {
                    $('#algotron').text(response.nolife).parents(".section").show().find("svg").hide();
                }
            });
        } else {
            $('#algotron').parents(".section").hide().find("svg").hide();
        }

        $.ajax({
            cache: false,
            url: '/n00z/tech',
            data: {key: key},
            dataType: 'html',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                let output = "";
                $(response).find("a").each(function() {
                    let $this = $(this);
                    if ($this.attr("target") === "_blank") {
                        return;
                    }
                    let caption = $this.text();
                    let link = $this.next("a[target]").attr("href");
                    if ($this.text().trim() !== '') {
                        output += '<a href="' + link + '"><span>' + caption + "</span></a>";
                    }
                });
                $('#tech').html(output).parents(".section").show().find("svg").hide();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/music',
            data: {key: key},
            dataType: 'html',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                let output = "";
                $(response).find("h3 em").each(function() {
                    if ($(this).text().trim() !== '') {
                        let caption = $(this).text();
                        let link = $(this).parents("a").attr("href");
                        let artist = $(this).parents("a").next("div").text();
                        output += '<a href="'+link+'"><span>' + caption + '</span> by <span>'+ artist + "</span></a>";
                    }
                });
                $('#music').html(output).parents(".section").show().find("svg").hide();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/movies',
            data: {key: key},
            dataType: 'html',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                let output = "";
                $(response).each(function() {
                    let caption = $(this).text();
                    let link = $(this).attr("href");
                    let year = extractYear(caption);
                    if ($(this).text().trim() !== '') {
                        output += '<a href="'+ link +'"><span>' + extractText(caption) + '</span> in <span>'+ year + "</span></a>";
                    }
                });
                $('#movies').html(output).parents(".section").show().find("svg").hide();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/games',
            data: {key: key},
            dataType: 'html',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                $('#games').html(response).parents(".section").show().find("svg").hide();
            }
        });


    }

    function load_data(key) {
        $(".section svg").show();
        clearTimeout(timelock);
        timelock = setTimeout(function() {
            load_all(key);
        }, 3000);

    }

    load_data('');

    /*
    $(document).on('keyup', 'input[type="text"]', function() {
        var key = $('input[type="text"]').val();
        load_data(key);
    });
    */

});
