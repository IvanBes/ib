// © IB
$(document).ready(function(){"use strict";var IB_LIKE={initializeListeners:function(){$(document).on('submit','form.ib_like_put_like',function(e){var that=$(this),serializedData=that.serializeObject(),action=window.Routing.generate('api_ib_like_put_like',{driver:that.attr('data-like-driver')});e.preventDefault();var event=$.Event('ib_like');that.trigger(event);if(event.isDefaultPrevented()||action==null){return}$.ibAjax({'url':action,'type':'POST','data':serializedData,success:function(data,statusCode){if(data.success){that.find('.rating').html(data.rate);that.replaceWith(that.find('.data-rate').removeClass('hidden'));}},error:function(data,statusCode){that.replaceWith(that.find('.data-rate').removeClass('hidden'));}})})}};IB_LIKE.initializeListeners()});