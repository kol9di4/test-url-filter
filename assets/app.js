import './bootstrap.js';
import './styles/app.css';
import 'https://code.jquery.com/jquery-3.7.1.min.js';

function setSliderAttr(){
    $('#minPriceRange').attr('max',$('#maxPriceRange').val());
    $('#maxPriceRange').attr('min',$('#minPriceRange').val());
}

function selectHighlightedInputs(inpClass){
    var nameInputs = [];
    $(inpClass).each(function(){
        if(this.checked === true){
            nameInputs.push(this.getAttribute('name'));
        }
    });
    if (nameInputs.length == $(inpClass).length)
        return [];
    return nameInputs;
}

$(document.body).on('change', 'input[type=checkbox][name=all]',function(){
    $('input[type=checkbox]').prop('checked',this.checked);
});

function setCheckBoxes(){
    $('input[type=checkbox]').each(function(){
        var text = $(this).prop('name');
        if($('h5:contains('+text+')').length>0)
            $(this).prop('checked', true);
        else
            $(this).prop('checked', false);
    });
}

$(function (){
    setCheckBoxes();
    $(document).on('change', '#minPriceRange', function() {
        $('label[for=minPriceRange]').html('Min: '+$(this).val()+'');
        setSliderAttr();
    });
    $(document).on('change', '#maxPriceRange', function() {
        $('label[for=maxPriceRange]').html('Max: '+$(this).val()+'');
        setSliderAttr();
    });
    $(document).on('change', '#colorCheckCheckedAll', function() {
        $('.check-color').each(function(){
            $(this).prop("checked", $('#colorCheckCheckedAll').prop('checked'));
        });
    });
    $(document).on('change', '#materialCheckCheckedAll', function() {
        $('.check-material').each(function(){
            $(this).prop("checked", $('#materialCheckCheckedAll').prop('checked'));
        });
    });

    $(document).on('click', '#submitFilters', function(e) {
        e.preventDefault();
        var data = {
            colors    : selectHighlightedInputs('.check-color'),
            materials    : selectHighlightedInputs('.check-material'),
            minPrice    : $('#minPriceRange').val(),
            maxPrice    : $('#maxPriceRange').val(),
            // availability : 'sometoken'
        };
        window.history.replaceState(null, document.title, '?'+$.param(data))
        $.ajax({
            url: location.pathname,
            method: 'POST',
            data: data,
            success: function (response) {
                if (response!= null){
                    $('.cards-section').html(response);
                }
                setCheckBoxes();
            },
            error: function (error) {
                alert("Ошибка при отправке данных: ", error);
            },
        });
    });
    $(document).on('click', '#download-csv', function(e) {
        var data = {
            colors    : selectHighlightedInputs('.check-color'),
            materials    : selectHighlightedInputs('.check-material'),
            minPrice    : $('#minPriceRange').val(),
            maxPrice    : $('#maxPriceRange').val(),
            // availability : 'sometoken'
        };
        document.location.href = location.pathname+'/download?'+$.param(data);
    });
})