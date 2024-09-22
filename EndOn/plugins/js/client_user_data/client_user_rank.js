$(document).ready(function()
{
      // 背景顏色選擇類型
      $('.JqnType0').on('click' , function()
      {
            $('[data-block]').removeClass('active');
            $('[data-block="'+$(this).data('radio')+'"]').addClass('active');
      });

      // 漸層背景顏色增加顏色
      $('.JqBtnBgAdd').on('click' , function()
      {
            var sHtml = '';
            var nId = $('.JqBgList').length+1;
            sHtml += '<div class="cilentUserRankBgList JqBgList MarginBottom10">';
            sHtml +=    '<div class="InlineBlock MarginRight5">';
            sHtml +=          nId;
            sHtml +=    '</div>';
            sHtml +=    '<div class="Ipt MarginRight10">';
            sHtml +=    '      <input type="color" name="sBackgroundColor['+nId+']" value="#000000">';
            sHtml +=    '</div>';
            sHtml +=    '<div class="InlineBlock MarginRight5">'+sPos+'</div>';
            sHtml +=    '<div class="Ipt MarginRight5">';
            sHtml +=    '      <input type="number" name="nBackgroundColorPos['+nId+']" value="">';
            sHtml +=    '</div>';
            sHtml +=    '<span class="InlineBlock MarginRight10">%</span>';
            sHtml +=    '<span class="InlineBlock BtnAny JqBtnBgDelete"><i class="fas fa-minus"></i></span>';
            sHtml += '</div>';
            $('.JqBgLBox').append(sHtml);
      });

      // 漸層背景顏色刪除顏色
      $('.JqBgLBox').on('click' , '.JqBtnBgDelete' , function()
      {
            $(this).parents('.JqBgList').remove();
            if($('.JqBgList').length == 2)
            {
                  $('.JqBtnBgDelete').remove();
            }
      });
});