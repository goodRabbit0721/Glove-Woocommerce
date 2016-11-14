function parseScript(strcode) {
  var scripts = new Array();         // Array which will store the script's code

  // Strip out tags
  while(strcode.indexOf("<!--SPV--><script") > -1) {
    var s   = strcode.indexOf("<!--SPV--><script");
    var s_e = strcode.indexOf(">//SPV", s);
    var e   = strcode.indexOf("</script", s);
    var e_e = strcode.indexOf(">", e);
    
    // Add to scripts array
    scripts.push(strcode.substring(s_e+6, e));
    // Strip from strcode
    strcode = strcode.substring(0, s) + strcode.substring(e_e+1);
  }
  
  // Loop through every script collected and eval it
  for(var i=0; i<scripts.length; i++) {
    try {
      eval(scripts[i]);
    }
    catch(ex) {
      if (typeof console == "object") {
        console.log('eval() failed.');
      }
    }
  }
}
// Call parseScript everytime AJAX call were complete
jQuery( document ).ajaxComplete(function( event, xhr, settings ) {
    parseScript(xhr.responseText);
});