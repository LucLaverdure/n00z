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

    function sortAndGroupByDate(selector, as_asc) {
        const $container = $(selector);
        const $items = $container.find('a').get();

        // Remove duplicates
        const uniqueItems = [];
        const seen = new Set();

        $items.forEach(item => {
            const $item = $(item);
            const identifier = $item.data('date') + '-' + $item.data('cat') + '-' + $item.find('span').text();

            if (!seen.has(identifier)) {
                seen.add(identifier);
                uniqueItems.push($item);
            }
        });

        uniqueItems.sort((a, b) => {
            const dateA = $(a).data('date');
            const dateB = $(b).data('date');
            const catA = $(a).data('cat').toLowerCase();
            const catB = $(b).data('cat').toLowerCase();
            const nameA = $(a).find('span').text().toLowerCase();
            const nameB = $(b).find('span').text().toLowerCase();

            if (dateA !== dateB) {
                if (as_asc)
                    return dateA.localeCompare(dateB);
                else
                    return dateB.localeCompare(dateA);
            } else if (catA !== catB) {
                return catA.localeCompare(catB);
            } else {
                return nameA.localeCompare(nameB);
            }
        });

        let currentDate = '';
        let currentCategory = '';
        $container.empty();

        uniqueItems.forEach(item => {
            const date = item.data('date');
            const category = item.data('cat');

            if (date !== currentDate) {
                currentDate = date;
                currentCategory = '';
                $container.append(`<h3>${currentDate}</h3>`);
            }
            if (category !== currentCategory) {
                currentCategory = category;
                $container.append(`<h4>${currentCategory}</h4><div class="content"></div>`);
            }
            $container.find('.content').last().append(item);
        });

        $container.on('click', 'h4', function() {
            $(this).next('.content').slideToggle();
        });
    }

    let data_ticker = 0;
    function tick_data() {
        data_ticker++;
        if (data_ticker === 5) {

            sortAndGroupByDate('#past', false);
            sortAndGroupByDate('#today', false);
            sortAndGroupByDate('#coming', true);

            $(".head svg").hide();
            $(".head img").fadeIn();
            $(".grid .section").slideDown();
        }
    }

    function add_data(data_category, data_output, date_input, link) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const [year, month, day] = date_input.split('-');
        const date = new Date(year, month - 1, day); // Months are zero-indexed
        date.setHours(0, 0, 0, 0);

        if (link === undefined) {
            link = "#";
        }

        const output = '<a href="'+ link +'" data-date="'+date_input+'" data-cat="'+data_category+'"> <span>' + data_output + '</span></a>';

        switch (true) {
            case date.getTime() === today.getTime():
                $('#today').append(output);
                break;
            case date < today:
                $('#past').append(output);
                break;
            case date > today:
                $('#coming').append(output);
                break;
            default:
                console.log("date_error")
        }

    }

    function load_all(key) {

        $.ajax({
            cache: false,
            url: '/n00z/tech',
            data: {key: key},
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                response.forEach(function(item) {
                    add_data("Tech", item.title, item.release_date);
                });
                tick_data();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/music',
            data: {key: key},
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                response.forEach(function(item) {
                    add_data("Music", item.title.replace("User Score", "") + " " + item.artist, item.release_date);
                });
                tick_data();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/movies',
            data: {key: key},
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                response.forEach(function(item) {
                    add_data("Movie", item.title, item.release_date);
                });
                tick_data();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/games',
            data: {key: key},
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                response.forEach(function(item) {
                    add_data("Games", item.title, item.releaseDate);
                });
                tick_data();
            }
        });

        $.ajax({
            cache: false,
            url: '/n00z/news',
            data: {key: key},
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                response.forEach(function(item) {
                    add_data("News", item.title, item.pubDate);
                });
                tick_data();
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

    function load_search(key) {
        $('div.search-results').html("");
        $.ajax({
            cache: false,
            url: '/fetcher?z=' + key,
            dataType: 'html',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function(response) {
                $('div.search-results').html("");
                let search_results = $(response).find('suggestion');
                search_results.each(function() {
                    let data = $(this).attr('data'); // Assuming 'data' is an attribute
                    $('.search-results').append('<a href="#">' + data + '</a>');
                    $('.search-results').show();
                });
            }
        });
    }

    $('.search-wrap input').on('input', function() {
        const key = $(this).val();
        load_search(key);
    });

    $(document).on('click', '.search-results a',  function() {
        const key = $(this).text();
        $('.search-wrap input').val(key);
        $(".search-results").hide();
    });

});
