jQuery(document).ready(function($){

    function populateResultForm(data){
        $('#report-result').val(JSON.stringify(data));
        $('#report-form').submit();
    }

    function runReport(apiUrl, pulseData, reportType)
    {
        var pulseDataCopy = JSON.parse(JSON.stringify(pulseData));
        $('#ajax-message').html("");

        if(pulseDataCopy){
            var unchecked = $('#run-pulse-table .fa-square-o');
            var dateRange = $('#date-range').val();
            unchecked.map(function(key, value){
                delete pulseDataCopy[$(value).data('key')];
            });
            var pulseDataNew = [];
            pulseDataCopy.map(function(value,key){
                if(value){
                    pulseDataNew.push(value);
                }
            });
            pulseDataNew = JSON.stringify(pulseDataNew);

            $.ajax({
                url: apiUrl,
                method: 'POST',
                data: {val:pulseDataNew, type:reportType, dates:dateRange, blog: blogUrl},
            }).fail(function(response, error, errorThrown){
                $('#report-loader').fadeOut(function(){
                    $('#ajax-message').html("Failed to run report");
                });
                return;
            }).done(function(response, status, jqXHR){
                populateResultForm(response);
                $('#report-loader').fadeOut(function(){
                    $('#ajax-message').html("");
                });
                return;
            });
        } else {
            $('#report-loader').fadeOut(function(){
                $('#ajax-message').html("Failed to run report");
            });
            return;
        }
    }

    function filterByMonth(input, month) {
        var monthStart = new Date(month+" 0:00:00");
        var monthEnd = new Date(monthStart.getFullYear(), monthStart.getMonth()+1, 0, 23,59,59);
        var newData = [];
        input.map(function(value,key){
            postDate = new Date(value.date);
            if(postDate >= monthStart && postDate <= monthEnd){
                newData.push(value);
            }
        });
        return newData;
    }

    $('#report-start').click(function(){
        $('#report-loader').fadeIn();
        runReport(apiUrl, pulseData, reportType);
    });

    $('#run-pulse-table .fa').click(function(){
        var currItem = $(this);
        if(currItem.hasClass('fa-check-square-o')){
            currItem.removeClass('fa-check-square-o');
            currItem.addClass('fa-square-o');
        } else {
            currItem.removeClass('fa-square-o');
            currItem.addClass('fa-check-square-o');
        }
    })

});
