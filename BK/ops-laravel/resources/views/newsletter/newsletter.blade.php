{!! $html !!}

<script>
    document.querySelectorAll("[contenteditable]").forEach(function(el){
        el.removeAttribute("contenteditable");
      })
</script>