<html><head><script>
function subst() {
  var vars={};
  var x=document.location.search.substring(1).split('&');
  for(var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
  var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
  for(var i in x) {
    var y = document.getElementsByClassName(x[i]);
    for(var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
  }
}
</script></head><body style="border:0; margin: 0;font-size: 9pt" onload="subst()">
<table style="width: 100%;font-size: 9pt">
  <tr>
    <td class="section1" style="text-align: left"></td>
    <td class="section2" style="text-align: center">
      {{$footer_line1}}
      <br>
      {{$footer_line2}}
    </td>
    <td style="text-align:right" width="50">
       <span class="page"></span> / <span class="topage"></span>
    </td>
  </tr>
</table>
</body></html>