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

$(function (){
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
        var obj = {
            colors    : selectHighlightedInputs('.check-color'),
            materials    : selectHighlightedInputs('.check-material'),
            minPrice    : $('#minPriceRange').val(),
            maxPrice    : $('#maxPriceRange').val(),
            // availability : 'sometoken'
        };

        document.location.href = location.pathname+'?'+$.param(obj);
    });
    $(document).on('click', '#download-csv', function(e) {
        // e.preventDefault();
        var obj = {
            colors    : selectHighlightedInputs('.check-color'),
            materials    : selectHighlightedInputs('.check-material'),
            minPrice    : $('#minPriceRange').val(),
            maxPrice    : $('#maxPriceRange').val(),
            // availability : 'sometoken'
        };
        // $.ajax({
        //     url: location.pathname+'/download',
        //     method: 'POST',
        //     data: obj,
        // });

        document.location.href = location.pathname+'/download?'+$.param(obj);
    });
})