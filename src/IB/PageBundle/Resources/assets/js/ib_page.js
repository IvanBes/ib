// © IB ~ ib_page ~ 2014-2015
$(document).ready(function()
{
  var AjaxLinks = $(".ib_page"), 
      container = $("#content"),
      menu_transversale = $('nav.ib_page'),
      History = window.History, 
      menu_init = false,
  ib_page = 
  {
    initializeListeners: function() 
    {
      AjaxLinks.on("click", "a.ajaxLink", function(event) 
      {
        event.preventDefault();
        event = $(this);
        event = event.attr("href");
        (event == window.location.pathname + window.location.search) || void 0 == event || null == event || ib_page.load(event);
      });

      AjaxLinks.on("click", "a.ajaxPage", function(event) {
        event.preventDefault();
        ib_page.loadPage($(this), window.location.pathname + window.location.search);
      });
    },

    loadPage:function(link, fullpathname) {
      if (!(link.attr('data-page') == 'undefined')) 
      {
        var personal_event = jQuery.Event("ib_page_before_loadPage"),
            section = null;
        
        personal_event.params = {
          _page: link.attr('data-page')
        };

        if (!(link.parent().parent().parent().attr('data-section') == 'undefined')) 
        {
          section = $.trim(link.parent().parent().parent().attr('data-section'));
          personal_event.params = $.extend(personal_event.params, {
            _page_section: section
          }); 
        }

        pathname = (fullpathname.indexOf('?') == -1) ? fullpathname : fullpathname.substr(0, fullpathname.indexOf('?'));
        querystring = (fullpathname.indexOf('?') == -1) ? null : fullpathname.substring(fullpathname.indexOf('?')).split('&');

        $.ibAjax({
          url:pathname,
          querystring:querystring,
          data:personal_event.params,
          success:function(data) {

            if(section !== null) {
              object = $('#'+section);
            } else {
              object = container;
            }

            object.css({
              opacity : '0.0'
            }).html($.trim(data.html)).delay(50).animate({
              opacity : '1.0'
            }, 300);
                          
          },
          'spinner':$('.loader_page')
        });
      }
    },

    load:function(fullpathname, popstate) 
    {    
      popstate = "undefined" !== typeof popstate ? !0 : !1;
  
      var personal_event = jQuery.Event("ib_page_before_load");
      
      personal_event.params = {
        _menu_transversale_page: menu_transversale.attr('data-nav')
      };

      pathname = (fullpathname.indexOf('?') == -1) ? fullpathname : fullpathname.substr(0, fullpathname.indexOf('?'));
      querystring = (fullpathname.indexOf('?') == -1) ? null : fullpathname.substring(fullpathname.indexOf('?')).split('&');

      $.ibAjax({
        url:pathname,
        querystring:querystring,
        data:personal_event.params,
        popstate:popstate,
        success:function(data) 
        {
          container.css({
            opacity : '0.0'
          }).html($.trim(data.html)).delay(50).animate({
            opacity : '1.0'
          }, 300);

          if ("no_change" == data.menu_transversale) 
          {
            // le menu n'a pas changé.

          } else {
            // sinon il y en a un nouveau soit il n'y en a pas

            // on met quand même à jour la face caché de l'iceberg (le génie est là)
            menu_transversale.html(data.menu_transversale);

            // on met à jour le nom du menu tranversale
            menu_transversale.attr('data-nav', data.menu_name);

            if (data.menu_transversale_open) 
            {
              menu_init = true;
              // on vérifie également si le site était dans le mode dans lequel il ne laissait pas apparaître de menu
              if ($('body').hasClass("non-transvervale")) 
              {
                // alors on supprime ce mode
                $('body').removeClass('non-transvervale');
              }
            } else {
              // il n'y pas de menu tout court

              // on vérifie si le mode "no-menu" n'est pas déjà active
              if (!($('body').hasClass('non-transvervale'))) 
              {
                // il ne l'es pas donc on le met !
                $('body').addClass('non-transvervale');
              }
            }
          }
        },
        complete:function() {
          if (menu_init) {
            menu_init = false;
            window.init_menu();
          };         
        }
      }, container);
    }
  };

  History.Adapter.bind(window,'statechange',function() 
  {
    ib_page.load(window.location.pathname + window.location.search, !0);
  });

  ib_page.initializeListeners();
});