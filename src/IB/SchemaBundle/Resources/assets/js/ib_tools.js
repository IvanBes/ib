// © IB ~ Outils/Utilitaires ~ 2014-2015 V1.0.0
(function($, window, undefined) {

  var already_permananted_link = !1,
      request_active = false,
      bread_crumb = $('#ribbon ol.breadcrumb'),
      History = window.History, 
      throttle_delay = 320,
      elems = $([]), 
      jq_resize = $.resize = $.extend($.resize, {}), 
      timeout_id,
      str_setTimeout = 'setTimeout', 
      str_resize = 'resize',
      str_data = str_resize + '-special-event', 
      str_delay = 'delay', 
      str_throttle = 'throttleWindow';

  jq_resize[str_delay] = throttle_delay;

  jq_resize[str_throttle] = true;

  $.event.special[str_resize] = {

    setup : function() {
      if (!jq_resize[str_throttle] && this[str_setTimeout]) {
        return false;
      }

      var elem = $(this);
      elems = elems.add(elem);
      $.data(this, str_data, {
        w : elem.width(),
        h : elem.height()
      });
      if (elems.length === 1) {
        loopy();
      }
    },
    teardown : function() {
      if (!jq_resize[str_throttle] && this[str_setTimeout]) {
        return false;
      }

      var elem = $(this);
      elems = elems.not(elem);
      elem.removeData(str_data);
      if (!elems.length) {
        clearTimeout(timeout_id);
      }
    },

    add : function(handleObj) {
      if (!jq_resize[str_throttle] && this[str_setTimeout]) {
        return false;
      }
      var old_handler;

      function new_handler(e, w, h) {
        var elem = $(this), data = $.data(this, str_data);
        data.w = w !== undefined ? w : elem.width();
        data.h = h !== undefined ? h : elem.height();

        old_handler.apply(this, arguments);
      };
      if ($.isFunction(handleObj)) {
        old_handler = handleObj;
        return new_handler;
      } else {
        old_handler = handleObj.handler;
        handleObj.handler = new_handler;
      }
    }
  };

  function loopy() {
    timeout_id = window[str_setTimeout](function() {
      elems.each(function() {
        var elem = $(this), width = elem.width(), height = elem.height(), data = $.data(this, str_data);
        if (width !== data.w || height !== data.h) {
          elem.trigger(str_resize, [data.w = width, data.h = height]);
        }

      });
      loopy();

    }, jq_resize[str_delay]);

  };

  // verifie si un object jquery existe
  $.fn.exists = function() {
    return 0 !== this.length;
  };

  // Transmet les valeurs d'un formulaire dans un tableau [object]
  $.fn.serializeObject = function() {
    var object = {}, d = $(this).serializeArray();
    $.each(d, function() {
      object[this.name] !== null ? (object[this.name].push || (object[this.name] = [object[this.name]]), object[this.name].push(this.value || "")) : object[this.name] = this.value || "";
    });
    return object;
  };

  // nettoie un formulaire
  $.fn.clearForm = function() {
    $(this).find(":input").each(function() {
      switch(this.type) {
        case "password":
        ;
        case "select-multiple":
        ;
        case "select-one":
        ;
        case "text":
        ;
        case "textarea":
          $(this).val("");
          break;
        case "checkbox":
        ;
        case "radio":
          this.checked = !1;
      }
    });
    $(this).find("div[class*='error']").remove();
  };

  // génère un tableau contenant des clés
  $.ibRange = function(debut, fin, pas) {
    var array = [];
    pas = pas || 1;
    var e = !1;
    if (isNaN(debut) || isNaN(fin)) {
      isNaN(debut) && isNaN(fin) ? (e = !0, debut = debut.charCodeAt(0), fin = $.charCodeAt(0)) : (debut = isNaN(debut) ? 0 : debut, fin = isNaN(fin) ? 0 : fin);
    }
    if (debut > fin) {
      for (;debut >= fin;) {
        array.push(e ? String.fromCharCode(debut) : debut), debut -= pas;
      }
    } else {
      for (;debut <= fin;) {
        array.push(e ? String.fromCharCode(debut) : debut), debut += pas;
      }
    }
    return array;
  };

  // Json To Array
  $.ibparseJSON = function(json) {
    try {
      return JSON && JSON.parse(json) || $.parseJSON(json);
    } catch (error) {
      return json;
    }
  };

  // récupere un paramètre d'une url
  $.getUrlParameter = function(href, parameter, default_) {
    default_ = "undefined" !== typeof default_ ? default_ : 1;
    parameter_value = RegExp("[\\?&amp;]" + href + "=([^&amp;#]*)").exec(parameter);
    return null !== parameter_value ? parameter_value[1] : default_;
  };

   $.drawBreadCrumb = function() {
    var nav_elems = $('nav li.active > a'), count = nav_elems.length;

    bread_crumb.empty();
    bread_crumb.append($("<span class=\"glyphicon glyphicon-arrow-right\"></span> <li>"+ $('.ib_navigation > a.active').first().text() +"</li>"));
    nav_elems.each(function() {
      bread_crumb.append($("<li></li>").html($.trim($(this).clone().children(".badge").remove().end().text())));
      if (!--count) document.title = bread_crumb.find("li:last-child").text();
    });
  };

  // active tout les liens qui sont associé à l'url (pathname)
  $.ibPermanantLink = function(requestURI) 
  {    
    already_permananted_link && $('ul[class*="nav-selected"] li.active').removeClass("active") && $('a.active').removeClass("active");
    
    if ('/' !== requestURI) 
    {
      $(".nav-selected-partiel").each(function(object) 
      {
        var data_split = $(this).attr("data-split"),
            splited_path = requestURI.split("/");
  
        if ((splited_path.length - 1) >= data_split) 
        {
          splited_path[splited_path.length - 1] = splited_path[splited_path.length - 1].split("?")[0];
          var LinkToActive = $.isNumeric(data_split) && (object = splited_path[data_split], $(this).find('a[href^="' + requestURI.substr(0, requestURI.indexOf(object) + object.length) + '"]').first());
        } else if ((splited_path.length - 0) >= data_split && requestURI.indexOf('?') !== -1) {
          var LinkToActive = $.isNumeric(data_split) && (object = splited_path[data_split-1], $(this).find('a[href^="' + requestURI.substr(0, requestURI.indexOf(object) + object.length) + '"]').first());
        };
  
        if ("undefined" !== typeof LinkToActive)
        {
          if (LinkToActive.parent().is('li')) 
          {
            if (LinkToActive.parent().parent().attr("data-split") == data_split) 
            {
              LinkToActive.parent().addClass("active");
              LinkToActive.parent().parent().parent().is('li');
              LinkToActive.parent().parent().parent().addClass("active");
            }
          } 
          else if (LinkToActive.parent().is('div')) 
          {
            LinkToActive.addClass("active");
          };
        };
      })
    }
  
    $.drawBreadCrumb();
    already_permananted_link = !0;
  };

  $.link_domaine = function() 
  {
    var mail=$('a[href^="mailto:"]'),
    link=$('a[href^="javascript:"]');
    $("a").attr("target",function(){
      if(this.href && ($(this).attr('target') == undefined))
      {
        return this.host==location.host||mail.is(this)||link.is(this)?"_self":"_blank"
      }
    });
  };

  // noyau ajax - toutes les requetes ajax passe par là.
  $.ibAjax = function(options, container, force) 
  { 
    container = "undefined" !== typeof container ? container : $('#content');
  	force = "undefined" !== typeof force ? !0 : !1;

    if (request_active && !force) 
    {
      return !1; 
    };

    var default_options = 
    {
      url:null,
      data:{},
      querystring:null,
      success: function() {
        alert("success");
      }, 
      wrapError:function() {
        $.bigBox({
            title : "Erreur",
            content : "Une érreur est survenue !\nVeuillez rafra\u00eechir la page. <br>Ce message va disparaître dans 6 secondes.",
            color : "#C46A69",
            icon : "fa fa-warning shake animated",
            number : "1",
            timeout : 6000
        });
      },
      dataType:"json",
      type:"GET",
      cache:!1,
      popstate:false,
      spinner:$("#app_loader")
    },

    options = $.extend(default_options, options);
    
    error_listener = function(response) 
    {
      var object = $.ibparseJSON(response.responseText);
      if ("undefined" !== typeof options.error) 
      {
        options.error(object, response.status);
      }
      else 
      {
        if(object.message == null) 
        {
          return options.wrapError()
        } else {
            $.bigBox({
                title : "Erreur ~ "+ object.code,
                content : object.message + ". Veuillez rafra\u00eechir la page. <br>Ce message va disparaître dans 6 secondes.",
                color : "#C46A69",
                icon : "fa fa-warning shake animated",
                number : "1",
                timeout : 6000
            });
        }
      }
    };

    options.url && options.data && $.ajax({
      type:options.type,
      dataType:options.dataType,
      url:(options.querystring !== null) ? options.url + "." + options.dataType + options.querystring : options.url + "." + options.dataType,
      data:options.data,
      cache:options.cache,
      error:error_listener,
      success:function(data) 
      {
        pushStateUrl = (options.querystring !== null) ? options.url + options.querystring : options.url;
        options.popstate || History.pushState({title:"transition", randomData: window.Math.random()}, "ib_ajax_transition", pushStateUrl);
        options.success(data);
      },
      beforeSend:function() 
      {
        request_active = true;
        
        $(":submit").prop("disabled", !0);
        options.spinner.removeClass("hide");
        
        "undefined" !== typeof options.beforeSend && options.beforeSend();

        $("html").animate({
          scrollTop : 0
        }, "fast");
      },
      complete:function() 
      { 
        $.ibPermanantLink((options.querystring !== null) ? options.url + options.querystring : options.url);
        $.link_domaine();
        options.spinner.addClass("hide");
        $(":submit").prop("disabled", !1);

        "undefined" !== typeof options.complete && options.complete();

        var event = $.Event("ibAjax.complete");
        $(window).trigger(event);

        request_active = false;
      }
    });

    return!0;
  };

})(jQuery, this);