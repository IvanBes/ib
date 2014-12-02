// © IB
$(document).ready(function() {
  var g = null, e = {}, b = {}, c = {initializeListeners:function() {
    c.discussion_container.on("click", "#discussion .pagination a", function(a) {
      a && a.preventDefault();
      a = $(this);
      var d = Math.round($.trim(a.attr("data-page")));
      e.page != d && (a = {page:d}, $.ibAjax({url:window.Routing.generate("api_ib_commentaire_get_discussion_commentaires", {id:e.id_discussion}), data:a, success:function(a) {
        e.lastdate = a.lastdate;
        e.page = d;
        $("#discussion").html($.trim(a.html));
        c.clearSituation();
      }, spinner:c.discussion_container.find("#loader_page")}));
    });
    c.discussion_container.on("click", "#discussion .expand_child", function(a) {
      a && a.preventDefault();
      a = $(this);
      a.parent().find(".hide").removeClass("hide");
      a.remove();
      $(window).trigger($.Event("ib.media.resize"));
    });
    c.discussion_container.on("click", "#discussion .ib_commentaire_button_edit", function(a) {
      a && a.preventDefault();
      a = $(this).parent().attr("data-id");
      c.canIclearSituation(a, "edit") || $.ibAjax({url:window.Routing.generate("api_ib_commentaire_get_edit_form_commentaire", {id:a}), success:function(a, f) {
        var h = c.discussion_container.find("#ib_commentaire_" + a.id + " > .media-body > .text"), e = c.discussion_container.find("#formulaire");
        e.attr("role-edit") ? c.discussion_container.find("#ib_commentaire_" + b.actual_commentaire + " > .media-body > .text").html($.trim(b.text)) : e.remove();
        b.type = "edit";
        b.actual_commentaire = a.id;
        b.text = h.html();
        h.html($.trim(a.html));
      }, spinner:c.discussion_container.find("#loader_form")});
    });
    c.discussion_container.on("click", "#discussion .ib_commentaire_button_reply", function(a) {
      a && a.preventDefault();
      a = $(this).parent().attr("data-id");
      if (!c.canIclearSituation(a, "reply")) {
        var d = c.discussion_container.find("#ib_commentaire_" + a + " .media-body").first(), f = c.discussion_container.find("#formulaire");
        f.attr("role-edit") ? c.discussion_container.find("#ib_commentaire_" + b.actual_commentaire + " .media-body .text").html($.trim(b.text)) : f.remove();
        b.type = "reply";
        b.actual_commentaire = a;
        d.append(g);
        c.discussion_container.find("#formulaire").attr("role-child", !0).children("form").attr("id", "ib_commentaire_commentaire_reply_form");
        a = $.Event("ib.media.resize");
        $(window).trigger(a);
      }
    });
    c.discussion_container.on("submit", "form#ib_commentaire_commentaire_new_form", function(a) {
      a && a.preventDefault();
      var d = $(this);
      a = d.serializeObject();
      $.ibAjax({type:"POST", url:window.Routing.generate("api_ib_commentaire_post_discussion_commentaire"), data:$.extend(a, e), success:function(a, b) {
        e.lastdate = a.lastdate;
        1 != e.page && (e.page = 1);
        a.reload ? c.reloadCommentaires(a.html) : c.appendCommentaire(a.html, a.count_result);
        d.clearForm();
      }, error:function(a, c) {
        var b = d.parent();
        b.after(a.html);
        b.remove();
      }, spinner:c.discussion_container.find("#loader_form")});
    });
    c.discussion_container.on("submit", "form#ib_commentaire_commentaire_edit_form", function(a) {
      a && a.preventDefault();
      var d = $(this);
      a = d.serializeObject();
      $.ibAjax({type:"POST", url:window.Routing.generate("api_ib_commentaire_put_discussion_commentaire"), data:$.extend(a, {id_commentaire:b.actual_commentaire}), success:function(a, d) {
        b.text = a.edition.replace(/\n/g, "<br />");
        c.clearSituation(true);
      }, error:function(a, c) {
        var b = d.parent();
        b.after(a.html);
        b.remove();
      }, spinner:c.discussion_container.find("#loader_form")});
    });
    c.discussion_container.on("submit", "form#ib_commentaire_commentaire_reply_form", function(a) {
      a && a.preventDefault();
      var d = $(this);
      a = d.serializeObject();
      $.ibAjax({type:"POST", url:window.Routing.generate("api_ib_commentaire_post_discussion_commentaire"), data:$.extend(a, e, {id_commentaire_parent:b.actual_commentaire}), success:function(a, b) {
        e.lastdate = a.lastdate;
        c.appendCommentaireChildren(a.id_commentaire_parent, a.html, a.count_result);
        d.clearForm();
      }, error:function(a, b) {
        var e = d.parent();
        e.after(a.html);
        e.remove();
        c.discussion_container.find("#formulaire").attr("role-child", !0).children("form").attr("id", "ib_commentaire_commentaire_reply_form");
      }, spinner:c.discussion_container.find("#loader_form")});
    });
    c.discussion_container.on("submit", "form.ib_commentaire_commentaire_remove", function(a) {
      a && a.preventDefault();
      var d = $(this);
      a = d.serializeObject();
      var b = d.parent().attr("data-id");
      c.canIclearSituation(b, "remove");
      $.ibAjax({type:"POST", url:window.Routing.generate("api_ib_commentaire_delete_commentaire"), data:$.extend(a, {id:b}), success:function(a, b) {
        var e = c.discussion_container.find("#ib_commentaire_" + a.id);
        a.success && e.find(".media-body").addClass("alert alert-warning");
        e.find("#ib_childs_" + a.id + " .pull-right").remove();
        d.parent().html($.trim(a.message));
      }, error:function(a, c) {
        var b = d.parent();
        b.after(a.message);
        b.remove();
      }, spinner:c.discussion_container.find("#loader_form")});
    });
  }, reloadCommentaires:function(a) {
    $("#discussion").html($.trim(a));
  }, appendCommentaire:function(a, c) {
    var b = $("#discussion > .commentaire").length;
    0 == b ? $("#discussion #avert").replaceWith($.trim(a)) : $("#discussion").prepend($.trim(a));
    b += c;
    6 < b && $.each($.ibRange(1, b - 6), function() {
      $("#discussion > .commentaire").last().remove();
    });
  }, appendCommentaireChildren:function(a, c, b) {
    b = $("#discussion #ib_childs_" + a + " .commentaire_child").length + b;
    $("#discussion #ib_childs_" + a).append($.trim(c));
    4 < b && $.each($.ibRange(1, b - 4), function() {
      $("#discussion #ib_childs_" + a + " .commentaire_child").first().remove();
    });
  }, clearSituation:function(submit) {
    if ("edit" == b.type) {
      var a = c.discussion_container.find("#ib_commentaire_" + b.actual_commentaire + " .media-body .text").first();
      a.html($.trim(b.text));
      if (submit) a.parent().find(".media-heading .update_commentaire").html('<span class="text-muted"> - Modifi\u00e9 <i class="glyphicon glyphicon-pencil"></i></span>');
    } else {
      c.discussion_container.find("#formulaire").remove();
    }
    c.discussion_container.find("#principale_formulaire").html($.trim(g));
    b.actual_commentaire = null;
    a = $.Event("ib.media.resize");
    $(window).trigger(a);
  }, canIclearSituation:function(a, d) {
    return $.isEmptyObject(b) || b.actual_commentaire != a || b.type != d && "remove" != d ? !1 : (c.clearSituation(), !0);
  }};
  $.getDiscussionCommentaires = function(a, d) {
    var f = jQuery.Event("ib_commentaire_before_load_discussion");
    f.params = {slug:encodeURIComponent(d), path:encodeURIComponent(window.location.pathname)};
    c.discussion_container.trigger(f);
    $.ibAjax({url:window.Routing.generate("api_ib_commentaire_get_discussion_form_commentaires", {entity:a}), data:f.params, success:function(a) {
      e = {};
      b = {};
      c.discussion_container.html(a.html);
      e.id_discussion = $("#discussion").attr("data-id");
      e.lastdate = $("#discussion").attr("data-last-date");
      e.page = 1;
      g = c.discussion_container.find("#formulaire").parent().html();
    }}, !0);
  };
  c.discussion_container = $("#ib_commentaire_discussion");
  "undefined" != typeof window.ib_commentaire_discussion_api_body && $.getDiscussionCommentaires(window.ib_commentaire_api_entity, window.ib_commentaire_api_slug);
  c.initializeListeners();
});