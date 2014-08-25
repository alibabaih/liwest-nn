$( document ).ready(function() {

//    function scroll_to_top(speed) {
//        $('body,html').animate({scrollTop: 0}, speed);
//    }
//
//    function scroll_to_bottom(speed) {
//        var height= $("body").height();
//        $("html,body").animate({"scrollTop":height},speed);
//    }
    //Обработка нажатия на кнопку "Вверх"
    $(document).ready(function(){
//Обработка нажатия на кнопку "Вверх"
        $("#up").click(function(){
//Необходимо прокрутить в начало страницы
            var curPos=$(document).scrollTop();
            var scrollTime=curPos/1.73;
            $("body,html").animate({"scrollTop":0},scrollTime);
        });

//Обработка нажатия на кнопку "Вниз"
        $("#down").click(function(){
//Необходимо прокрутить в конец страницы
            var curPos=$(document).scrollTop();
            var height=$("body").height();
            var scrollTime=(height-curPos)/1.73;
            $("body,html").animate({"scrollTop":height},scrollTime);
        });
    });




    $(function () {
        var flag = false;
        $("#up").css("background-color", "#5db9f0");
        setTimeout(function () {
            $("#up").css("background-color", "#5db9f0");
            setInterval(function () {
                $("#up").css("background-color", flag? "#5db9f0":"#6ac0f3");
                flag = !flag;
            }, 500)
        }, 500);
    });

    $(function () {
        var flag = false;
        $("#down").css("background-color", "#5db9f0");
        setTimeout(function () {
            $("#down").css("background-color", "#5db9f0");
            setInterval(function () {
                $("#down").css("background-color", flag? "#5db9f0":"#6ac0f3");
                flag = !flag;
            }, 500)
        }, 500);
    });

});