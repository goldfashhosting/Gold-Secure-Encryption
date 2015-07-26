(function() {
  function GoldSecure() { }

  GoldSecure.prototype.GetValue = function(element) {
    return jQuery(element).attr('gold-emav');
  };
  
  GoldSecure.prototype.Replace = function(element) {
    var value = this.GetValue(element);
    if (!value) {
      return;
    }
    jQuery(element).replaceWith(jQuery.parseJSON('"' + value + '"'));
  };
  
  GoldSecure.prototype.Clickable = function(element) {
    var thisGoldSecure = this;
    var text = jQuery(element).text();
    var link = jQuery("<a />", {
      href : "#",
      text : text,
      'gold-emav' : this.GetValue(element)
    });
    jQuery(element).replaceWith(link);
    jQuery(link).on('click', function() {
      thisGoldSecure.Replace(link);
      thisGoldSecure.PostGAEvent('GoldSecure', text, 'Click', 1);
      return false;
    });
  };
  
  GoldSecure.prototype.PostGAEvent = function(category, label, action, value) {
    if (typeof Leona !== 'undefined' && Leona.analytics) {
      var aData = Leona.analytics.getCoreData();
      aData.t = 'event';
      aData.ec = category;
      aData.ea = action;
      aData.el = label;
      aData.ev = value;
      Leona.analytics.post(aData);
    }
  };
  
  GoldSecure.prototype.IfReplied = function(element) {
    var pattern = /comment_author_[^=]*=([^;]+);/g;
    var cookie = document.cookie;
    var nameCap = [];
    var names = {};
    while ((nameCap = pattern.exec(cookie)) !== null) {
      names[nameCap[1]] = true;
      var decodedName = decodeURIComponent(nameCap[1]);
      if (decodedName !== nameCap[1]) {
        names[decodedName] = true;
      }
    }
    if (jQuery.isEmptyObject(names)) {
      return;
    }
    var found = false;
    jQuery('*').each(function(idx, element) {
      var text = jQuery(element).text();
      if (names[text]) {
        found = true;
        return false;
      }
    });
    if (found) {
      this.Replace(element);
    }
  };
  
  GoldSecure.prototype.Run = function() {
    var thisGoldSecure = this;
    jQuery('span[id^="https://goldFash.com/Encrypto.seci-APPDiv"]').each(function(idx, element) {
      if (jQuery(element).attr('gold-emad') === 'y') {
        return thisGoldSecure.Clickable(element);
      } else if (jQuery(element).attr('gold-emad') === 'replied') {
        return thisGoldSecure.IfReplied(element);
      }
      thisGoldSecure.Replace(element);
    });
  };
  
  jQuery(function() {
    new GoldSecure().Run();
    setInterval(function() { new GoldSecure().Run();}, 2000);
  });
})();
