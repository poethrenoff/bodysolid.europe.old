function compareItem(id, compareLink){
    if (!$(compareLink).parent().hasClass('selected')) {
        $.get('/compare/add/' + id, {}, function (response){
            if (response.error) {
                alert(response.error);
            } else if (response.message) {
                $(compareLink).parent().addClass('selected');
                $('div.compare').html(response.message);
            }
        }, 'json');
    }
    return false;
}

function buyItem(id, buyLink){
    if (!$(buyLink).parent().hasClass('selected')) {
        $.get('/cart/add/' + id + '/', {}, function (response){
            $(buyLink).parent().addClass('selected');
            $("div.cart").html(response);
        });
    }
    return false;
}

function likeItem(id, likeLink){
    $.get('/client/like/' + id + '/', {}, function (response) {
        if (response) {
            $(likeLink).removeClass('like').addClass('unlike').attr('title', 'Удалить из Избранного');
        } else {
            $(likeLink).removeClass('unlike').addClass('like').attr('title', 'Добавить в Избранное');
        }
    }, 'json');
    return false;
}

function incItem(incLink){
    return shiftItem(incLink, +1);
}

function decItem(decLink){
    return shiftItem(decLink, -1);
}

function shiftItem(shiftLink, shift){
    var $row = $(shiftLink).parents('tr:first');
    var $qntInput = $row.find('input[name^=quantity]');
    var $priceInput = $row.find('input[name^=price]');
    var qnt = parseInt($qntInput.val());
    var price = parseInt($priceInput.val());
    var $qntCell = $row.find('td').eq(2);
    var $costCell = $row.find('td').eq(3);
    
    qnt = qnt + shift;
    
    if (qnt > 0) {
        $qntInput.val(qnt);
        $qntCell.find('span').html(qnt);
        $costCell.html(qnt * price);
        
        updateCart();
    }
    
    return false;
}

function updateCart(){
    var totalQnt = 0; var totalSum = 0;
    $('form.cart-form').find('input[name^=quantity]').each(function(){
        var $qntInput = $(this);
        var $priceInput = $qntInput.parent().find('input[name^=price]');
        var qnt = parseInt($qntInput.val());
        var price = parseInt($priceInput.val());
        totalQnt += qnt;
        totalSum += qnt * price;
    });
    
    var $totalRow = $('form.cart-form').find('tr.total');
    var $totalQntCell = $totalRow.find('td').eq(2);
    var $totalSumCell = $totalRow.find('td').eq(3);
    $totalQntCell.html(totalQnt);
    $totalSumCell.html(totalSum);
    
    var $discountInput = $('form.cart-form').find('input[name^=discount]');
    var discount = parseFloat($discountInput.val());
    if (!isNaN(discount)) {
        var $finalRow = $('form.cart-form').find('tr.final');
        var $finalSumCell = $finalRow.find('td').eq(2);
        $finalSumCell.html(Math.round(totalSum * discount));
    }
    
    $('form.cart-form').ajaxSubmit(function(response){
        $(".cart").html(response);
    });
}

function callback() {
    $.get('/callback', function (response){
        $(response).modal({
            opacity: 30,
            overlayClose: true,
            closeHTML: '<a class="modalCloseImg" title="Закрыть"></a>'
        });
    });
    return false;
}

function setMark(mark) {
    $('.vote .star').removeClass('active');
    $('.vote .star:lt(' + mark + ')').addClass('active');
}

var background = [
    '/image/background01.jpg', '/image/background02.jpg', '/image/background03.jpg'
];

$(function () {
    $.supersized({
        slides: [{
            image: background[Math.floor(Math.random() * 3)]
        }]
    });
    
    $('.teaser-slideshow').cycle();
    
    $('.product').each(function () {
        $(this).find('.product-hover').hover(function () {
            $(this).find('.product-description').show();
        }, function () {
            $(this).find('.product-description').hide();
        });
	});
    
    $('.card-tab a').click(function() {
        if (!$(this).hasClass('selected')) {
            $('.card-content').children().hide('slow');
            $('#' + $(this).attr('for')).show('slow');
            
            $('.card-tab a').removeClass('selected');
            $(this).addClass('selected');
		}
        return false;
    });
    
    $('input[href]').bind('click', function(e) {
        if (!$(this).attr('confirm') || confirm($(this).attr('confirm'))) {
            location.href = $(this).attr('href');
        }
    });
    
    $('.vote .star').mouseenter(function(){
        if ($('.vote').hasClass('enabled')) {
            setMark($(this).attr('mark'));
        }
    }).click(function(){
        if ($('.vote').hasClass('enabled')) {
            $.post('/product/vote/' + $(this).attr('id'), {mark: $(this).attr('mark')}, function(data){
                var rating = Math.round(data.rating);
                $('.vote').attr('rating', rating);
                $('.vote').mouseleave().removeClass('enabled');
            }, 'json');
        }
    });
    $('.vote').mouseleave(function(){
        if ($('.vote').hasClass('enabled')) {
            setMark($(this).attr('rating'));
        }
    });    
});
