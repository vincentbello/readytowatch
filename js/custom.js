spinner = "<i class='fa fa-spinner fa-spin'></i>";
offset = 4; // UTC is +4 hours ahead of NY

$(window).on('load', function (e) {
  if (window.location.hash == "#_=_") {
    window.location.hash = '';
    history.pushState('', document.title, window.location.pathname);
    e.preventDefault();
  }
});

$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 800);
        return false;
      }
    }
  });
});

function addRelated( element, onload, more ) {
  element = $(element);
    var that = (onload) ? element.find('.related') : element.find('.get-more-related');
    if (!onload) {
      that.tooltip('destroy');
      that.removeClass('get-more-related');
      that.addClass('with-spinner');
      that.html(spinner);
    }
    var off = $('.related-mov').length;
    var id = element.attr('id').substring(2);
    $.post( "ajax/more_related_movies.php",
            { id: id, offset: off, more: more },
            function (data) {
              that.parent().hide();
              // hide loading gif
              $('.related-container').append(data);
              $('[data-toggle="tooltip"]').tooltip();
            });
}


$('.reveal-elem-trigger').click( function () {
  var that = $(this);
  var revealed = that.parent().find('.revealed-elem');
  if (!revealed.is(':visible')) {
    revealed.slideDown( function () { revealed.find("input")[0].focus(); });
    that.find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
    
  } else {
    revealed.slideUp();
    that.find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
  }  
});



////////// SEARCH FORMS /////////////

// prevent empty search form
function validate_form(form) {
  var search = $(form).find("input[type='text']");
  var placeholder = search.attr('placeholder');
	valid = ((search.val().length > 0) && (search.val() != placeholder));
	if (valid) {
	 return true
	} else {
	  search.val(placeholder);
	  search.addClass('textbox-error');
	  search.select();
  	search.on('keypress', function() {
      search.removeClass('textbox-error');
    });
	  return false
	}
}

// when clicking back on the search form
function resetSearch() {
  $("#nav-search").select();
  $("#nav-search").css("font-weight", "normal");
}

// // autofill for movies
// $('#search,#nav-search').typeahead({
//   autoSelect: false,
//   items: 10,
//   minLength: 2,
//   source: function (query, process) {
//     $.ajax({
//       url: 'ajax/autocomplete.php',
//       type: 'POST',
//       dataType: 'JSON',
//       data: 'query=' + query,
//       success: function(data) {
//         process(data);
//       }
//     });
//   }
// });

$('.dropdown-button').hover( function () {
  var that = $( this );
  var div = that.parent();
  that.addClass('hovered');
  div.find('ul.dropdown-menu').show();
}, function () {
  var that = $( this );
  var div = that.parent();
  var menu = div.find('ul.dropdown-menu');
  console.log(menu);
  console.log(menu.is(':hover'));
  if (!menu.is(':hover'))
    menu.hide();
});

$('.dropdown ul.dropdown-menu').hover( null, function () { $( this ).hide().parent().find('button').removeClass('hovered'); })

$('.dropdown-menu li a').click( function () { if ($( this ).attr('href')) window.location.href = $( this ).attr('href'); })

$('#results-tabs a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
});

// return to search
$(".return-to-search").click( function () {
  $("#nav-search").val("");
  $("#nav-search").focus();
});

$("#nav-search").focus(function () {
  $(this).animate({ opacity: 1 }, 100);
  $("#search-submit").css("border-color", "#c20427");
  $("#search-submit").animate({ opacity: 1 }, 100);
});
$("#nav-search").focusout(function () {
  $(this).animate({ opacity: 0.8 }, 100);
  $("#search-submit").css("border-color", "#afafaf");
  $("#search-submit").animate({ opacity: 0.8 }, 100);
});

$(".filter .dropdown-menu a").click( function () {
  var div = $(this).parent().parent().parent();
  var id = div.attr("id");
  $("[data-dropdown='#" + id + "']").html($(this).html());
  div.find("input").val($(this).html());
});
//\\\\\\\\ SEARCH FORMS \\\\\\\\\\\\

///////// FILTER SEARCH ////////////
function showNumMovies(totalMovies, year_min, year_max, runtime_min, runtime_max, language, genre, mpaa, orderby) {
  year_min = year_min.replace('< ', '');
  runtime_max = runtime_max.replace('+', '');
  $.ajax({
    type: "POST",
    url: "ajax/get_num_movies.php",
    data: { year_min: year_min, year_max: year_max, runtime_min: runtime_min, runtime_max: runtime_max, language: encodeURIComponent(language),
            genre: genre, mpaa: mpaa, orderby: orderby }
  }).done( function (numMovies) {
    formatNumMovies(numMovies, totalMovies, $('#match-progress'));
  });
}

function formatNumMovies(numMovies, totalMovies, div) {
  var number = parseInt(numMovies);
  var percentage = (number / totalMovies) * 100;
  div.animate({ width: percentage + '%' }, 1000);
  div.attr('title', Math.round(percentage) + '%');
  div.attr('data-original-title', Math.round(percentage) + '%');
  $('#match-movies').html("<b style='color:#c20427'>" + numMovies.replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " movie" + ((number == 1) ? "" : "s") + "</b> that match" + ((number == 1) ? "es" : "") + " your criteria.");
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};
//\\\\\\\ FILTER SEARCH \\\\\\\\

///////// NAVBAR ////////////
function revealNavbar() {
  $(".navigator").addClass("nav-selected");
  $("#container").animate({
    marginLeft: "165px"
  }, { duration: 200, queue: false });
  $("#left-navbar").addClass('in-focus');
}

function revealFixedNavbar() {
  $(".navigator").addClass("nav-selected");
  $("#left-navbar").addClass("left-navbar-fixed in-focus");
}

function hideNavbar() {
  $(".navigator").removeClass("nav-selected");
  $("#container").animate({
    marginLeft: "0"
  }, { duration: 200, queue: false });
  $("#left-navbar").removeClass('in-focus');
}

function hideFixedNavbar() {
  $(".navigator").removeClass("nav-selected");
  $("#left-navbar").removeClass("left-navbar-fixed in-focus");
}
//\\\\\\\\\ NAVBAR \\\\\\\\\\\

//////// REGISTER FORM ////////
$('#reg-username').keyup( function () {
  $.ajax({
    type: "POST",
    url: "ajax/check_existing.php",
    data: { type: 'user', data: $('#reg-username').val() }
  }).done(function (result) {
    var w = $('#reg-username-warning');
    if (result) {
      w.html(result);
      w.parent().find("i").removeClass("fa-check");
      w.parent().find("i").addClass("fa-times");
      $('#reg-username').keyup( function () { w.empty(); w.parent().find("i").removeClass("fa-times") });
    } else {
      if ($('#reg-username').val().length == 0) {
        w.parent().find("i").removeClass("fa-check");
        w.parent().find("i").removeClass("fa-times");
      } else {
        w.parent().find("i").removeClass("fa-times");
        w.parent().find("i").addClass("fa-check");
      }
    }
  });
});

$('#reg-username').focusout( function () {
  var usrnm = $(this);
  var w = $('#reg-username-warning').parent().find("i");
  if (usrnm.val().length == 0) {
    w.removeClass("fa-check");
    w.addClass("fa-times");
    $('#reg-username-warning').html("<i class='fa fa-exclamation-triangle'></i> Please enter a username.");
    usrnm.focus( function () { w.removeClass("fa-check"); w.removeClass("fa-times"); $('#reg-username-warning').empty() });
  } else if ((usrnm.val().length < 3) || (usrnm.val().length > 29)) {
    w.removeClass("fa-check");
    w.addClass("fa-times");
    $('#reg-username-warning').html("<i class='fa fa-exclamation-triangle'></i> Your username must be between 3 and 30 characters long.");
    usrnm.focus( function () { w.removeClass("fa-check"); w.removeClass("fa-times"); $('#reg-username-warning').empty() });
  }
});

$("#reg-email").focusout( function () {
  var w = $('#reg-email-warning').parent().find("i");
  if (!isValidEmailAddress($("#reg-email").val())) {
    $('#reg-email-warning').html("<i class='fa fa-exclamation-triangle'></i> This is not a valid email address.");
    w.removeClass("fa-check");
    w.addClass("fa-times");
    $('#reg-email').keyup( function () { $('#reg-email-warning').empty(); w.removeClass("fa-times") });
  } else {
    $.ajax({
      type: "POST",
      url: "ajax/check_existing.php",
      data: { type: 'email', data: $('#reg-email').val() }
    }).done( function (result) {
      if (result.length > 0) {
        $('#reg-email-warning').html(result);
        w.removeClass("fa-check");
        w.addClass("fa-times");
        $('#reg-email').focus( function () { $('#reg-email-warning').empty(); w.removeClass("fa-times"); w.removeClass("fa-check"); });
      } else {
        $('#reg-email-warning').empty();
        w.removeClass("fa-times");
        w.addClass("fa-check");
      }
    });
  }
});

$("#reg-email-conf").focusout( function () {
  var w = $("#reg-email-conf-warning").parent().find("i");
  if (($(this).val() != $("#reg-email").val()) || ($(this).val().length == 0)) {
    $("#reg-email-conf-warning").html("<i class='fa fa-exclamation-triangle'></i> The email addresses do not match.");
    w.removeClass("fa-check");
    w.addClass("fa-times");
    $("#reg-email-conf").focus( function () { $("#reg-email-conf-warning").empty(); w.removeClass("fa-times"); w.removeClass("fa-check"); });
  } else {
    $('#reg-email-conf-warning').empty(); 
    w.removeClass("fa-times");
    w.addClass("fa-check");
  }
});

$("#reg-password").focusout( function () {
  var w = $("#reg-password-warning").parent().find("i");
  if ($(this).val().length < 6) {
    $("#reg-password-warning").html("<i class='fa fa-exclamation-triangle'></i> Your password is too short.");
    w.removeClass("fa-check");
    w.addClass("fa-times");
    $("#reg-password").focus( function () { $("#reg-password-warning").empty(); w.removeClass("fa-times"); w.removeClass("fa-check"); });
  } else {
    $('#reg-email-conf-warning').empty();
    w.removeClass("fa-times");
    w.addClass("fa-check");
  }
});

// checks login input via ajax
$("#p-reg-form").submit(function (e) {
  e.preventDefault();
  var submit = true;
  $("#p-reg-form i").each(function () {
    if ($(this).hasClass("fa-times"))
      submit = false;
  });
  if (submit) {
    var form = this;
    $("#p-reg-success-message").html("<i class='fa fa-check-circle'></i> Successfully registered!");
    setTimeout( function () {
      $("#p-reg-success-message").fadeOut();
      form.submit();
    },1000);
  } else {
    $("#p-reg-form fa-times").parent().parent().find("input").focus();
  }
});  
//\\\\\\\\ REGISTER FORM \\\\\\\\\\

////////// FEEDBACK FORM //////////
$("#feedback-form textarea").keyup( function () {
  var that = $(this);
  if (that.val().length > 0)
    $("#feedback-form button").removeAttr("disabled");
  else
    $("#feedback-form button").attr("disabled", "disabled");
});

$("#feedback-form").submit( function (e) {
  var that = $(this);
  var textarea = that.find('textarea');
  e.preventDefault();
  var feedback = textarea.val();
  $.ajax({
    type: "POST",
    url: 'ajax/submitFeedback.php',
    data: { feedback: feedback }
  }).done(function(data) {
    that.find("button").attr('disabled', 'disabled');
    textarea.val('');
    var message = that.find(".message-success");
    message.show().html('<i class="fa fa-check-circle"></i> Thank you! Your feedback has been submitted.');
    setTimeout( function () { message.fadeOut(); }, 3000);
  });
});
//\\\\\\\\ FEEDBACK FORM \\\\\\\\\\


function loadLink(id, linkType, t, resultType) {
  if (resultType == 'results') {
      return $.post( "links/" + linkType + "/" + linkType + ".php", { id: id }, function (data) {
      var linkCount = t.find(".link").length;
      if ((data.length > 0) && (linkCount >= 2))
        t.parent().find(".loading-links").hide();
      if ((linkCount >= 3) && (data.length > 0))
        t.append("<div class='link-more'>" + data + "</div>");
      else
        t.append(data);
      });
  } else {
    return $.post( "links/" + linkType + "/" + linkType + "-mov.php", { id: id}, function (data) {
      var linkSection = $('#' + linkType);
      var section = linkSection.find(".link-section-info");
      section.html(data.text);
      if (data.link.length > 0) {
        linkSection.find("a").attr("href", data.link);
      } else {
        linkSection.addClass('disabled').find("h3").addClass('disabled');
        var alertButton = section.find('.alert-me');
        alertButton.click( function () { alertMe(id, linkType, alertButton); });
      }
    }).fail(function () { console.log("error"); });
  }
}

function alertMe (id, type, that) {
  //var that = $(this);
  var section = that.parent();
  section.html(spinner);
    $.ajax({
      type: "POST",
      url: "ajax/alert_me.php",
      data: { id: id, type: type }
    }).done( function (data) {
      section.html(data);
    });
}

//////// FAVORITES ///////////

function addToFavorites(movid, elem) {
  $.ajax({
    type: "POST",
    url: 'ajax/add_to_favorites.php',
    data: { id: movid }
  }).done(function(r) {
    if (elem.hasClass("favorite-star")) {
      elem.removeClass("favorite-star fa-star-o");
      elem.addClass("favorite-check fa-star");
    } else {
      if (elem.hasClass('fav-result'))
        elem.html("<i class='fa fa-star' style='color:white'></i>");
      else
        elem.html(" <i class='fa fa-star' style='color:white'></i> Favorite");
      elem.addClass('favorited');
    }
    elem.attr("title", "Remove from favorites").tooltip('fixTitle').tooltip('show');
  }).fail(function() {
    console.log("ERROR");
  });
}

function removeFromFavorites(movid, elem) {
  $.ajax({
     type: "POST",
     url: 'ajax/remove_from_favorites.php',
     data: { id: movid }
   }).done(function() {
       if (window.location.pathname.indexOf("favorites") !== -1) {
         removeFromFavoritesPage(elem);
       } else if (elem.hasClass("favorite-check")) {
         elem.removeClass("favorite-check fa-star");
         elem.addClass("favorite-star fa-star-o");
         elem.attr("title", "Add to favorites").tooltip('fixTitle').tooltip('show');
       } else {
          elem.removeClass('favorited');
          elem.attr('title', 'Add to favorites').tooltip('fixTitle').tooltip('show');
          if (elem.hasClass('fav-result'))
            elem.html("<i class='fa fa-plus-circle'></i>");
          else
            elem.html(" <i class='fa fa-plus-circle'></i> Favorites");
       }
   }).fail(function() {
     console.log("ERROR");
   });
}

function removeFromFavoritesPage(elem) {
  elem.parent().fadeOut(300);
  var totalFav = parseInt($("#fav-count b").html()) - 1;
  var favCount = $(".mov_entry:visible").length - 1;
  var shown = parseInt($("#shown").html()) - 1;
  $("#shown").html(shown);
  if ((favCount == 0) && (totalFav > 0)) {
    location.reload(); // true to refresh from the server
  } else if ((favCount == 0) && (totalFav == 0)) {
    $("#fav-count").html("no favorites");
  } else if (totalFav == 1) {
    $("#fav-count").html("<b>1</b> favorite movie");
  } else {
    $("#fav-count").html("<b>" + totalFav + "</b> favorite movies");
  }
}

$(".mov_entry, .movie-lg").hover( function () {
  $(this).find(".fav-button").not('.favorited').show();
}, function () {
  $(this).find(".fav-button").not('.favorited').hide();
});

$(".fav-button").click( function () {
  var elem = $(this);
  var movid = elem.attr("id").substring(3);
  if (elem.find('.fa-plus-circle').length)
    addToFavorites(movid, elem);
  else
    removeFromFavorites(movid, elem);
});

$(".favorite-star, .favorite-check").click( function () {
  var elem = $(this);
  var movid = elem.attr("id").substring(4);
  if (elem.hasClass("favorite-star")) // want to add as favorite
    addToFavorites(movid, elem);
  else
    removeFromFavorites(movid, elem);
});
//\\\\\\\\\ FAVORITES \\\\\\\\\\\

////////// MORE INFO //////////////
$(".mov_entry").on("click", ".more-info-button", function (e) {
  var entry = $(e.delegateTarget);
  var button = $(e.currentTarget);
  var castContainer = entry.find('.complete-cast');
  var message = entry.find('.complete-cast-message');
  var id = entry.data('id');
  var ellipsis = entry.find('.ellipsis');

  if (ellipsis.hasClass("ellipsis-full")) { // if already expanded
    ellipsis.removeClass("ellipsis-full");
    castContainer.hide();
    entry.find(".cast-short").show();
    button.html("<i class='fa fa-angle-double-down'></i>").attr("title", "More").tooltip('fixTitle').tooltip('show');
  } else {
    entry.find(".cast-short").hide();
    var toLoad = (!castContainer.html()) ? true : false;
    message.show();
    ellipsis.addClass("ellipsis-full");
    button.html("<i class='fa fa-angle-double-up'></i>").attr("title", "Less").tooltip('fixTitle').tooltip('show');
    if (toLoad) {
      $.ajax({
        type: "POST",
        url: 'ajax/cast_entry.php',
        data: { id: id }
      }).done( function (response) {
        message.hide();
        castContainer.show().html(response);
      }).fail(function () {
        message.html("Sorry, we could not load the cast.");
      });
    } else {
      message.hide();
      castContainer.show();
    }
  }
});
//\\\\\\\\\ MORE INFO \\\\\\\\\\\

/////////// HOVERCARD ///////////

$(".actorLink").mouseover( function (e) {
  var that = $( this );
  var id = that.data('hovercard');
  var targetOffset = $(e.currentTarget).offset();
  var offTop = targetOffset.top + that.height();
  var offLeft = targetOffset.left;
  var curr = $('#container').find("div.hovercard[data-hovid='" + id + "']");
  var below = false;
  //arrow up or down
  var navHeight = $("#navbar").height();
  var midPoint = ($(window).height() - navHeight)/2;
  var domainOffset = that[0].getBoundingClientRect().top - navHeight;
  if (domainOffset < midPoint)
    below = true;
  // if ((targetOffset.top-$(window).scrollTop()) > ($(window).height()/2))
  //   below = false;

  if (curr.length) {
    // if (!below) {
    //   offTop = targetOffset.top - curr.find('.hovercard-contents').height() - 10;
    // }
    curr.show().css({
        'top' : ((below) ? offTop : (targetOffset.top - curr.find('.hovercard-contents').height() - 20)) + 'px',
        'left' : offLeft + 'px'
      }).find('.hov-triangle').addClass((below) ? '' : 'point-down').removeClass((below) ? 'point-down' : '').css('left', (that.width()/2)-5);
  } else {
    $.get(
      'ajax/hovercard.php?type=a&id=' + id
      ).done( function (data) {
        var card = $(data);
        card.appendTo('#container').show().css({
        'top' : ((below) ? offTop : (targetOffset.top - card.find('.hovercard-contents').height() - 20)) + 'px',
        'left' : offLeft + 'px'}).show()
        .find('.hov-triangle').addClass((below) ? '' : 'point-down').css('left', (that.width()/2)-5);
    });
  }
});

$(".actorLink").mouseleave( function (e) {
  var id = $(e.currentTarget).data('hovercard');
  var curr = $('#container').find("div.hovercard[data-hovid='" + id + "']");
  if (curr.length && curr.is(':visible') && !(curr.is(':hover'))) {
    curr.hide();
  }

});

$('#container').on('mouseleave', '.hovercard', function (e) {
  $(e.currentTarget).hide();
});

//\\\\\\\\\ HOVERCARD \\\\\\\\\\\

$('.link-section:not(.disabled)').click( function () {
  if (!$( this ).hasClass('disabled')) {
    $( this ).find('.link-feedback').show();
  }
});

$('.link-feedback:not(.received)').click( function () {
  var that = $( this );
  that.addClass('received');
  that.html(spinner);
  $.ajax({
    type: "POST",
    url: "ajax/report_broken_link.php",
    data: {
      id: that.parent().parent().attr('id').substring(2),
      type: that.parent().attr('id')
    }
  }).done( function (response) {
    that.html(response).delay(3000).fadeOut();
  });
})

/////////// IMAGE UPLOAD ////////

function usrImgUpload() {
  // hide image, replace it with loading gif
  var fd = new FormData();
  fd.append('file', document.getElementById('usr-img-upload').files[0]);
  //fd.append('label', 'WEBUPLOAD');
  $.ajax({
    url: 'ajax/usr_img_upload.php',
    type: 'POST',
    data: fd,
    dataType: 'JSON',
    enctype: 'multipart/form-data',
    processData: false,
    contentType: false
  }).done( function (data) {
    var message = $('#response');
    message.removeClass();
    if (data.status == 1) {
      message.addClass('message-success');
      $('.usr-img-box-main img, .usr-img-box img').attr('src', data.filename);
      message.show().delay(3000).fadeOut();
    } else {
      message.addClass('message-danger');
      message.show();
    }
    message.html(data.response);
  });
}

$('#usr-img-upload').change( function () {
  usrImgUpload();
});

$("#uploadbutton").click( function () { $('#usr-img-upload').click(); });
//\\\\\\\\\ IMAGE UPLOAD \\\\\\\\\\\

////////// ACCOUNT PAGE ////////////
$("#edit-password").click(function () {
  if ($("#password").css("display") == "none") {
    $("#password").css("display", "block");
    $("#password-change").css("display", "none");
    $("#settings-submit").css("margin-left", "0");
    $("#settings-submit").addClass("disabled");
  } else {
    $("#password").css("display", "none");
    $("#password-change").css("display", "block");
    $("#settings-submit").css("margin-left", "20%");
    $("#settings-submit").removeClass("disabled");
  }
});

$("#new-email").keyup(function() {
  var that = $(this);
  if (that.val().length > 0) {
    $('#settings-submit').removeClass('disabled');
  } else {
    $('#settings-submit').addClass('disabled');
  }
});

$("#settings-submit").click(function () {
  var currentPass = $("#current-pass").val();
  var newPass = $("#new-pass").val();
  var confirmPass = $("#confirm-pass").val();
  var newEmail = $("#new-email").val();
  $.ajax({
    type: "POST",
    url: "ajax/update_account.php",
    data: { currentPass: $("#current-pass").val(), newPass: $("#new-pass").val(), confirmPass: $("#confirm-pass").val(), newEmail: newEmail }
  }).done( function (response) {
    switch (response) {
      case "1":
        $("#pass-save-message").html("<span class='message-success'><i class='fa fa-check-circle fa-lg'></i> Your password has successfully been changed!</span>");
        setTimeout(function () {
          $("#pass-save-message").fadeOut();
          $("#password").css("display", "block");
          $("#password-change").hide();
          $("#settings-submit").addClass('disabled').css("margin-left", "0");
        }, 1500);
        break;
      case "2":
        $("#pass-save-message").html("<span class='message-danger'><i class='fa fa-times-circle-o fa-lg'></i> The current password is incorrect.</span>");
        $("#current-pass").focus();
        $("#current-pass").keyup(function () { $("#pass-save-message").empty() });
        break;
      case "3":
        $("#pass-save-message").html("<span class='message-danger'><i class='fa fa-times-circle-o fa-lg'></i> The new passwords do not match.</span>");
        $("#confirm-pass").focus();
        $("#confirm-pass, #new-pass").keyup(function () { $("#pass-save-message").empty() });
        break;
      case "4":
        $("#pass-save-message").html("<span class='message-danger'><i class='fa fa-times-circle-o fa-lg'></i> That is not a valid email address.</span>");
        $("#new-email").focus();
        $("#new-email").keyup(function () { $("#pass-save-message").empty() });
        break;
      case "5":
        $("#pass-save-message").html("<span class='message-danger'><i class='fa fa-times-circle-o fa-lg'></i> This email address is already in use.</span>");
        $("#new-email").focus();
        $("#new-email").keyup(function () { $("#pass-save-message").empty() });
        break;
      case "6":
        $("#pass-save-message").html("<span class='message-success'><i class='fa fa-check-circle fa-lg'></i> The email address has successfully been added.</span>");
        setTimeout(function () {
          $("#pass-save-message").fadeOut();
          $("#settings-submit").addClass('disabled');
          $("#new-email").prop('disabled', true);
        }, 1500);
        break;      
    }
  });
});

//\\\\\\\\ ACCOUNT PAGE \\\\\\\\\\\\
////////// LOG IN //////////////////

$("#login-form, #login-dropdown").click( function (event) {
  event.stopPropagation();
}); 
$( "#login-dropdown" ).click( function () {
  $( "#login-form" ).slideToggle( "fast" );
  $( "#login-username" ).focus();
});

// checks login input via ajax
$("#login-form").submit(function (e) {
  e.preventDefault();
  var form = this;
  var user = $("#login-username").val();
  var rememberme = ($(form).find('input[name="stay-logged"]').is(':checked')) ? 1 : 0;
  $.ajax({
    type: "POST",
    url: 'ajax/preLogin.php',
    data: { username: user, password: $("#login-password").val(), rememberme: rememberme }
  }).done( function(result) {
      if (result.length == 0) {
        $("#login-message").html("<span class='message-success' style='margin-bottom: 15px'><i class='fa fa-check-circle' style='font-size:20px'></i> Welcome, " + user + "!</span>");
        setTimeout( function () {
          form.submit();
        }, 1000);
      } else {
        $("#login-message").append(result);
        if (result.indexOf("password") != -1) { // if password is incorrect
          $("#login-password").focus();
          $("#login-password").keydown(function () { $("#login-message").empty() });
        } else {
          $("#login-username").focus();
          $("#login-username").keydown(function () { $("#login-message").empty() });
        }
      }
  }).fail(function() {
    console.log("ERROR");
  });
});

// checks login input via ajax
$("#p-form").submit(function (e) {
  e.preventDefault();
  var form = this;
  var user = $("#p-username").val();
  var rememberme = ($(form).find('input[name="stay-logged-full"]').is(':checked')) ? 1 : 0;
  $.ajax({
    type: "POST",
    url: "ajax/preLogin.php",
    data: { username: user, password: $("#p-password").val(), rememberme: rememberme }
  }).done(function (result) {
      if (result.length == 0) {
        $("#p-message").html("<span class='message-success' style='margin-bottom: 15px'><i class='fa fa-check-circle' style='font-size:22px'></i> Welcome, " + user + "!</span>");
        setTimeout(function () { form.submit() }, 1000);
      } else {
        $("#p-message").append(result);
        if (result.indexOf("password") != -1) { // if password is incorrect
          $("#p-password").focus();
          $("#p-password").keydown(function () { $("#p-message").empty() });
        } else {
          $("#p-username").focus();
          $("#p-username").keydown(function () { $("#p-message").empty() });
        }
      }
  }).fail(function() {
    console.log("ERROR");
  });
});
//\\\\\\\\ LOG IN \\\\\\\\\\\\

/////// PREFERENCES //////////
$("#movieprefs-submit").click(function () {
  var adult = $("#movieprefs [name='adult']").bootstrapSwitch('state');
  var amazon = $("#movieprefs [name='amazon']").bootstrapSwitch('state');
  var netflix = $("#movieprefs [name='netflix']").bootstrapSwitch('state');
  $.ajax({
    url: "ajax/update_movieprefs.php",
    data: "adult=" + adult + "&amazon=" + amazon + "&netflix=" + netflix
  }).done( function () {
    $("#save-message").css("display", "inline");
    $("#save-message").html("<i class='fa fa-check-circle fa-lg' style='font-size:22px'></i> Your changes have successfully been saved!");
    setTimeout(function () {
      $("#save-message").fadeOut();
      $("#movieprefs-submit").addClass("disabled");
    }, 1500);
  });
});
//\\\\\\ PREFERENCES \\\\\\\\\

////////// ALERTS ////////////

$("#alerts-form [type='checkbox'][name='any']").on('switchChange.bootstrapSwitch', function(event, state) {
  $("#alerts-submit").removeClass("disabled");
  var index = $("tr").index($(this).closest("tr"));
  $("#alerts-form tr:eq(" + index + ")").find("input[type='checkbox'][name!='any']").bootstrapSwitch('state', state.value, true);
});

$("#alerts-form [type='checkbox'][name!='any']").on('switchChange.bootstrapSwitch', function(event, state) {
  $("#alerts-submit").removeClass("disabled");
  var index = $("tr").index($(this).closest("tr"));
  var any = $("input[type='checkbox'][name='any']:eq(" + (index-1) + ")");
  if (any.bootstrapSwitch('state')) { // if any is checked, uncheck it
    any.bootstrapSwitch('state', false, true);
  }
});

$("#alerts-form button").click( function () {
  $("#alerts-submit").addClass("disabled");
  $('.message-success').show();
  var alerts = {};
  $("tr.mov-alert").each( function () {
    var id = $(this).attr('id').substring(1);
    alerts[id] = {};
    $(this).find("input").each( function () {
      alerts[id][$(this).attr('name')] = ($(this).bootstrapSwitch('state')) ? 1 : 0;
    });
  });
    $.ajax({ type: "POST",
          url: "ajax/updateAlerts.php",
          data: alerts,
          cache: false,
          success: function(response) {
            $('.message-success').show();
            $('.message-success span').hide();
            setTimeout(function () {
              $('.message-success').fadeOut();
            }, 2000);
            console.log(response);
          }
    });
  return false;
});

$('.table-alerts .fa-times-circle').click( function () {
  var that = $(this);
  var id = that.attr('id').substring(10);
  $.ajax({
    type: "POST",
    url: "ajax/removeAlert.php",
    data: { id: id }
  }).done(function () {
    var count = parseInt($("#alerts-count b").html()) - 1;
    if (count == 0) {
      $("#alerts-form").fadeOut();
      $("#alerts-count").html("have no alerts set up");
    } else {
      $("#alerts-count").html("have <b>" + count + "</b> alert" + ((count == 1) ? "" : "s") + " set up");
      that.parent().parent().fadeOut(); // fade out entire table row
    }
  });
});

//\\\\\\\\ ALERTS \\\\\\\\\\\\


$(document).click(function() {
  if ($( "#login-form" ).css('display') == 'block')
    $( "#login-form" ).slideToggle( "fast" );
});

$( document ).ready( function () {

 $.ajaxSetup({ 
    cache: false 
  });

 // $.get('movies3.json', function (data) {
 //    $('#search,#nav-search').typeahead({
 //      source:data,
 //      autoSelect: false
 //    });
 //  },'json');

// DATES, TIMES

$('.timestamp').each( function () {
  var nyTimestamp = $( this ).data('timestamp');
  var m = moment(new Date(nyTimestamp * 1000));
  var diff = m.diff(moment(), 'days');
  var format = ((diff < 7) ? ((diff <= 1) ? '[today]' : 'dddd') : 'MMMM D') + ', [at] h:mm A';
  $( this ).html(m.format(format));
});

$('#search, #nav-search').typeahead({
  autoSelect: false,
  items: 10,
  minLength: 2,
  source: function (query, process) {
    $.ajax({
      url: 'ajax/autocomplete.php',
      type: 'POST',
      dataType: 'JSON',
      data: { query: query},
      success: function(data) {
        process(data);
      }
    });
  }
});


  $( window ).resize( function () {
    if ($( window ).width() > 1160) {
      if ($(".navigator").hasClass("nav-selected"))
        revealNavbar();
    } else {
      if ($(".navigator").hasClass("nav-selected"))
        hideNavbar();
    }
  });

  $(".navigator").click( function () {
    if ($( window ).width() > 1160) { // big screen
      if ($(".navigator").hasClass("nav-selected")) // if the navbar is already down
        hideNavbar();
      else // navbar is not down; put it down
        revealNavbar();
    } else { // small screen
      if ($(".navigator").hasClass("nav-selected")) // if the navbar is already down
        hideFixedNavbar();
      else // navbar is not down; put it down
        revealFixedNavbar();
    }
  });

  ////////// LINKS //////////////
  
  var linksContainer = $('.links-container');
  if (linksContainer.length) {
    var id = linksContainer.attr('id').substring(2);
    $.when(loadLink(id, "itunes", linksContainer, 'movie'), loadLink(id, "amazon", linksContainer, 'movie'), 
      loadLink(id, "netflix", linksContainer, 'movie'), loadLink(id, "youtube", linksContainer, 'movie'),
      loadLink(id, "crackle", linksContainer, 'movie'), loadLink(id, "google_play", linksContainer, 'movie'));
  }
  
  $('.result-right-info button').click( function () {
    var that = $( this );
    that.hide();
    var t = that.parent();
    t.removeClass('result-right-info');
    t.parent().find('.loading-links').show();
    var id = t.attr('id').substring(2);
    $.when(loadLink(id, "itunes", t, 'results'), loadLink(id, "amazon", t, 'results'), loadLink(id, "netflix", t, 'results'), loadLink(id, "youtube", t, 'results'), loadLink(id, "crackle", t, 'results'), loadLink(id, "google_play", t, 'results')
      ).then( function () {
          t.parent().find(".loading-links").hide();
          if (t.find(".link").length == 0) {
            t.parent().find('.no-links').show();
            t.parent().find('.alert-me').click( function () { 
              alertMe(id, 'any', $(this));
            });
          }
          $('[data-toggle="tooltip"]').tooltip();
      });
  })
  
  //\\\\\\\\\\ LINKS \\\\\\\\\\\\\\

  /// GET MORE RELATED MOVIES (UP TO 5)
  $('.related-container').on('click', '.get-more-related', function (e) {
    addRelated(e.delegateTarget, false, 6);
  });

  /// /GET MORE RELATED MOVIES



  
  // ACTIVATE DROPDOWNS, TOOLTIPS, ETC
  $('.dropdown-menu').dropdown();
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover();
});