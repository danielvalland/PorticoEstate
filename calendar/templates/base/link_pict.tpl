<!-- $Id: link_pict.tpl 16925 2006-07-28 03:30:44Z skwashd $    link_pict -->
<!-- BEGIN link_pict -->
{picture}
<!-- END link_pict -->
<!-- BEGIN link_open -->
<a href="{link_link}" onMouseOver="window.status='{lang_view}'; return true;">
<!-- END link_open -->
<!-- BEGIN pict -->
 <img src="{pic_image}" width="{width}" height="{height}" alt="{alt}" title="{title}" border="0">
<!-- END pict -->
<!-- BEGIN link_text -->
 {text}
<!-- END link_text -->
<!-- BEGIN link_close -->
</a>
<!-- END link_close -->
