
$(document).ready(function(){

    $('.faq li').click(function(){
      $(this).children().slideDown('fast');
    });

    $('.tabs a').click(function(){
      $('.tabs a').removeClass('active');
      var classe = $(this).attr('id');
      $('.item-tabs').css('display','none');
      $('.tabs-'+classe).css('display','block');
    });

  windowSizeDetectd(window);

  /*$('.icone-menu').click(function(){
    $('.menu-mobile .menu .itens').addClass('.itens-active');
  });*/
  
  $('#abre-menu').click(function(){
    $('.menu').css('display','block');
  });

  $('#btn-menu').click(function(){
    $('.menu').css('display','none');

  });


});

$(window).resize(function(){
    windowSizeDetectd(this);
}); 

function windowSizeDetectd(w)
{
  var widthScreen = $(w).width();
  var owl = $("#owl-demo");
  var owl_2 = $("#owl2");
  var owl_3 = $("#owl3");
  var owl_4 = $("#owl4");

  if(widthScreen > 1023)
  {

    owl.owlCarousel({
     
      itemsCustom : [
        [0, 3]
      ],
      navigation : false
    });

    owl_2.owlCarousel({
     
      itemsCustom : [
        [0, 4]
      ],
      navigation : false
    });

    owl_3.owlCarousel({
     
      itemsCustom : [
        [0, 2]
      ],
      navigation : false
    });

  }

  else
  {
    if(widthScreen <= 1023 && widthScreen >= 768)
    {

      owl.owlCarousel({
     
      itemsCustom : [
        [1, 2]
      ],
      navigation : false
      });
      
      owl_2.owlCarousel({
     
        itemsCustom : [
          [0, 0]
        ],
        navigation : false
      });
  
    }
    else
    {


      $('.link-nossas-filiais').click(function(){
        $('.nossas-filiais').slideToggle('fast');
      });

      owl.owlCarousel({
     
      itemsCustom : [
        [0, 0]
      ],
      navigation : false
      });

      owl_2.owlCarousel({
     
        itemsCustom : [
          [1, 1]
        ],
        navigation : false
      });

      owl_4.owlCarousel({
     
      itemsCustom : [
        [0, 1]
      ],
      navigation : false
      });

    }
  }

}