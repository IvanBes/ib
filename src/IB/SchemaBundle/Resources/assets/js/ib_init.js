// © IB ~ Initialisation frontend ~ 2014-2015 V1.0.0
$.menu_speed = 235;
$.navbar_height = 49;
$.navigation_height = 45;
$.root_ = $('body');
$.left_panel = $('#left-panel');
$.shortcut_dropdown = $('#shortcut');

$.device = null;
$.enableJarvisWidgets = true;
$.enableMobileWidgets = false;

var ismobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));

if (!ismobile) {
  $.root_.addClass("desktop-detected");
  $.device = "desktop";
} else {
  $.root_.addClass("mobile-detected");
  $.device = "mobile";
}

$(document).ready(function() 
{
  $('.minifyme').click(function(e) {
    $('body').toggleClass("minified");
    $(this).effect("highlight", {}, 500);
    e.preventDefault();
  });

  $('#hide-menu >:first-child > a').click(function(e) {
    $('body').toggleClass("hidden-menu");
    e.preventDefault();
  });
  
  $('#logout a').click(function(e) {
    var $this = $(this);
    $.loginURL = $this.attr('href');
    $.logoutMSG = $this.data('logout-msg');

    $.SmartMessageBox({
      title : "<i class='fa fa-sign-out txt-color-orangeDark'></i> Logout <span class='txt-color-orangeDark'><strong>" + $('#show-shortcut').text() + "</strong></span> ?",
      content : $.logoutMSG || "You can improve your security further after logging out by closing this opened browser",
      buttons : '[No][Yes]'

    }, function(ButtonPressed) {
      if (ButtonPressed == "Yes") {
        $.root_.addClass('animated fadeOutUp');
        setTimeout(logout, 1000)
      }

    });
    e.preventDefault();
  });

  function logout() {
    window.location = $.loginURL;
  }
});

function init_menu() 
{
  if (!null) 
  {
    $('nav ul').jarvismenu({
      accordion : true,
      speed : $.menu_speed,
      closedSign : '<em class="glyphicon glyphicon-collapse-down"></em>',
      openedSign : '<em class="glyphicon glyphicon-collapse-up"></em>'
    });
  } else {
    alert("Error - menu anchor does not exist");
  }
}

function nav_page_height() {
  
  var minHeight = 500;
  var mainHeight = $('#main').find('#content').outerHeight() + 42;
  var menuHeight = $.left_panel.find('nav').outerHeight() + 40;

  if (!$('body').hasClass('hidden-menu')) 
  {
    $.left_panel.css('min-height', '');
  }

  if (mainHeight > menuHeight) 
  {  
    if (mainHeight < minHeight) {
      mainHeight = minHeight;
      $('#main').css('min-height', mainHeight + 'px');
    };
    if ($('body').hasClass('hidden-menu')) 
    {
      $.left_panel.css('min-height', mainHeight + 'px');
    }
  } 
  if (mainHeight < menuHeight) 
  {
    if (menuHeight < minHeight) 
    {
      menuHeight = minHeight;
      if ($('body').hasClass('hidden-menu')) 
      {
        $.left_panel.css('min-height', menuHeight + 'px');
      };
    };
    $('#main').css('min-height', menuHeight + 'px');
  }
}

$('#main').resize(function() {
  nav_page_height();
  check_if_mobile_width();
})

$('nav').resize(function() {
  nav_page_height();
})

$(window).resize(function() {
  nav_page_height();
})

function check_if_mobile_width() {
  if ($(window).width() < 979) {
    $.root_.addClass('mobile-view-activated')
  } else if ($.root_.hasClass('mobile-view-activated')) {
    $.root_.removeClass('mobile-view-activated');
  }
}

var ie = ( function() {

  var undef, v = 3, div = document.createElement('div'), all = div.getElementsByTagName('i');

  while (div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->', all[0]);

  return v > 4 ? v : undef;

}());

$.fn.extend({

  jarvismenu : function(options) {

    var defaults = {
      accordion : 'true',
      speed : 200,
      closedSign : '[+]',
      openedSign : '[-]'
    };

    var opts = $.extend(defaults, options);
    var $this = $(this);

    $this.find("li").each(function() {
      if ($(this).find("ul").size() != 0) {
        $(this).find("a:first").append("<b class='collapse-sign'>" + opts.closedSign + "</b>");

        if ($(this).find("a:first").attr('href') == "#") {
          $(this).find("a:first").click(function() {
            return false;
          });
        }
      }
    });

    $this.find("li.active").each(function() {
      $(this).parents("ul").slideDown(opts.speed);
      $(this).parents("ul").parent("li").find("b:first").html(opts.openedSign);
      $(this).parents("ul").parent("li").addClass("open")
    });

    $this.find("li a").click(function() {

      if ($(this).parent().find("ul").size() != 0) {

        if (opts.accordion) {
          if (!$(this).parent().find("ul").is(':visible')) {
            parents = $(this).parent().parents("ul");
            visible = $this.find("ul:visible");
            visible.each(function(visibleIndex) {
              var close = true;
              parents.each(function(parentIndex) {
                if (parents[parentIndex] == visible[visibleIndex]) {
                  close = false;
                  return false;
                }
              });
              if (close) {
                if ($(this).parent().find("ul") != visible[visibleIndex]) {
                  $(visible[visibleIndex]).slideUp(opts.speed, function() {
                    $(this).parent("li").find("b:first").html(opts.closedSign);
                    $(this).parent("li").removeClass("open");
                  });

                }
              }
            });
          }
        }
        if ($(this).parent().find("ul:first").is(":visible") && !$(this).parent().find("ul:first").hasClass("active")) {
          $(this).parent().find("ul:first").slideUp(opts.speed, function() {
            $(this).parent("li").removeClass("open");
            $(this).parent("li").find("b:first").delay(opts.speed).html(opts.closedSign);
          });

        } else {
          $(this).parent().find("ul:first").slideDown(opts.speed, function() {
            $(this).parent("li").addClass("open");
            $(this).parent("li").find("b:first").delay(opts.speed).html(opts.openedSign);
          });
        }
      }
    });
  }
});

function launchFullscreen(element) {

  if (!$.root_.hasClass("full-screen")) {

    $.root_.addClass("full-screen");

    if (element.requestFullscreen) {
      element.requestFullscreen();
    } else if (element.mozRequestFullScreen) {
      element.mozRequestFullScreen();
    } else if (element.webkitRequestFullscreen) {
      element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) {
      element.msRequestFullscreen();
    }

  } else {
    
    $.root_.removeClass("full-screen");
    
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }

  }
}

 $.root_.on("click", '[data-action="launchFullscreen"]', function(b) {
  //alert('lol');
      launchFullscreen(document.documentElement), b.preventDefault();
 })

function setup_widgets_desktop() {

  if ($.fn.jarvisWidgets && $.enableJarvisWidgets) {

    $('#widget-grid').jarvisWidgets({

      grid : 'article',
      widgets : '.jarviswidget',
      localStorage : true,
      deleteSettingsKey : '#deletesettingskey-options',
      settingsKeyLabel : 'Reset settings?',
      deletePositionKey : '#deletepositionkey-options',
      positionKeyLabel : 'Reset position?',
      sortable : true,
      buttonsHidden : false,
      toggleButton : true,
      toggleClass : 'fa fa-minus | fa fa-plus',
      toggleSpeed : 200,
      onToggle : function() {
      },
      deleteButton : true,
      deleteClass : 'fa fa-times',
      deleteSpeed : 200,
      onDelete : function() {
      },
      editButton : true,
      editPlaceholder : '.jarviswidget-editbox',
      editClass : 'fa fa-cog | fa fa-save',
      editSpeed : 200,
      onEdit : function() {
      },
      colorButton : true,
      fullscreenButton : true,
      fullscreenClass : 'fa fa-resize-full | fa fa-resize-small',
      fullscreenDiff : 3,
      onFullscreen : function() {
      },
      customButton : false,
      customClass : 'folder-10 | next-10',
      customStart : function() {
        alert('Hello you, this is a custom button...')
      },
      customEnd : function() {
        alert('bye, till next time...')
      },

      buttonOrder : '%refresh% %custom% %edit% %toggle% %fullscreen% %delete%',
      opacity : 1.0,
      dragHandle : '> header',
      placeholderClass : 'jarviswidget-placeholder',
      indicator : true,
      indicatorTime : 600,
      ajax : true,
      timestampPlaceholder : '.jarviswidget-timestamp',
      timestampFormat : 'Last update: %m%/%d%/%y% %h%:%i%:%s%',
      refreshButton : true,
      refreshButtonClass : 'fa fa-refresh',
      labelError : 'Sorry but there was a error:',
      labelUpdated : 'Last Update:',
      labelRefresh : 'Refresh',
      labelDelete : 'Delete widget:',
      afterLoad : function() {
      },
      rtl : false,
      onChange : function() {
        
      },
      onSave : function() {
        
      },
      ajaxnav : $.navAsAjax

    });

  }
}

function setup_widgets_mobile() {

  if ($.enableMobileWidgets && $.enableJarvisWidgets) {
    setup_widgets_desktop();
  }
}

function pageSetUp() {
  $("[rel=tooltip]").tooltip();
  $("[rel=popover]").popover();

  if ($.device === "desktop")
  {
    setup_widgets_desktop();
  } else {
    setup_widgets_mobile(); 
  }

  $.ibPermanantLink(window.ib_api_pathInfo);
  $.link_domaine();
  if (!($('body').hasClass('non-transvervale'))) 
  {
    init_menu();
  }
  nav_page_height();
}