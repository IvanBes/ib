// © IB
$(document).ready(function(){var b={initializeListeners:function(){b.like_container.one("submit","form#ibSondagesondagebundleform",function(e){var h=$(this),f=h.serializeObject(),g=window.Routing.generate("api_ib_sondage_post_sondage_vote",{_format:"json"});e.preventDefault();e=$.Event("ib_sondage_submit_vote");h.trigger(e);e.isDefaultPrevented()||null==g||$.ibAjax({url:g,type:"POST",data:f,success:function(d,c){h.parent().html(d.html)},error:function(d,c){h.parent().html(d.message)}})})}};b.like_container=$("#sondage");b.initializeListeners()});